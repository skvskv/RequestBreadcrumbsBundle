<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle\Service;

use Exception;
use Symfony\{Component\HttpFoundation\RequestStack, Contracts\Service\ResetInterface};

class BasicUidService implements UidServiceInterface, ResetInterface
{
    private null|string $uid;

    private string $headerName;

    private RequestStack $requestStack;

    private bool $isResetAllowed = true;

    public function __construct(
        string       $headerName,
        RequestStack $requestStack
    )
    {
        $this->headerName = $headerName;
        $this->requestStack = $requestStack;
    }

    public function reset(): void
    {
        if ($this->isResetAllowed) {
            $this->uid = null;
        }
    }

    /**
     * @throws Exception
     */
    public function getUid(): string
    {
        if ($this->uid !== null) {
            return $this->uid;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            $this->isResetAllowed = false;
            $this->uid = $request->headers->get($this->headerName);

        }

        // Make uid if there's none so far; esp for consumer/cron/command/script/whatnot
        if (empty($this->uid)) {
            $this->uid = $this->createUid();
        }

        return $this->uid;
    }

    /**
     * For better testability & mocking
     *
     * @return string Unique ID
     * @throws Exception
     */
    public function createUid(): string
    {
        $rdata = random_bytes(16);

        $rdata[6] = chr(ord($rdata[6]) & 0x0f | 0x40); // set version to 0100
        $rdata[8] = chr(ord($rdata[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($rdata), 4));
    }
}
