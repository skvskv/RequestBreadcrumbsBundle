<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle\EventListener;

use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use skvskv\LogBreadcrumbsBundle\Service\UidServiceInterface;

class InjectUidInRequestListenerTest extends TestCase
{
    /**
     * Header containing breadcrumb (uid)
     *
     * @var string
     */
    private $headerName = 'SomeHeaderName';

    /** @var MockObject&UidServiceInterface */
    private $uidService;

    /** @var ProvidesBreadcrumbForGuzzleCommunication */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uidService = $this->createMock(UidServiceInterface::class);
        $this->listener = new ProvidesBreadcrumbForGuzzleCommunication($this->headerName, $this->uidService);
    }

    public function testOnEightPointsGuzzleRequest(): void
    {
        $headers = ['some-header' => 'some-header-value'];
        $request = new Request('GET', 'https://some-url', $headers, 'some-body');
        $event = new PreTransactionEvent($request, 'some-service-name');

        $this->uidService
            ->expects(self::once())
            ->method('getUid')
            ->willReturn('some-uid');

        $this->listener->onEightPointsGuzzleRequest($event);

        $modifiedRequest = $event->getTransaction();

        self::assertEquals('some-body', $modifiedRequest->getBody());
        self::assertEquals('GET', $modifiedRequest->getMethod());
        self::assertEquals('https://some-url', $modifiedRequest->getUri());

        self::assertSame('some-header-value', $modifiedRequest->getHeaderLine('some-header'));
        self::assertSame('some-uid', $modifiedRequest->getHeaderLine($this->headerName));
    }
}
