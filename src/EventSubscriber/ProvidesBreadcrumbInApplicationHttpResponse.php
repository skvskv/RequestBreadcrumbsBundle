<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle\EventSubscriber;

use skvskv\LogBreadcrumbsBundle\Service\UidServiceInterface;
use Symfony\Component\{EventDispatcher\EventSubscriberInterface, HttpKernel\Event\ResponseEvent};

/**
 * A Symfony subscriber of kernel.event_subscriber kind.
 * Injects breadcrumb (uid) into HTTP Response as header (X-Breadcrumb)
 *
 */
class ProvidesBreadcrumbInApplicationHttpResponse implements EventSubscriberInterface
{
    public function __construct(private string $headerName, private UidServiceInterface $uidService)
    {
    }

    /**
     * @return array<class-string, string|string[]>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $headersBag = $event->getResponse()->headers;
        if (!$headersBag->has($this->headerName)) {
            $headersBag->set($this->headerName, $this->uidService->getUid());
        }
    }
}
