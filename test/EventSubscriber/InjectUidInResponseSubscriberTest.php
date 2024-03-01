<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use skvskv\LogBreadcrumbsBundle\Service\UidServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class InjectUidInResponseSubscriberTest extends TestCase
{
    /** @var string */
    private $headerName = 'some-header-name';

    /** @var MockObject&UidServiceInterface */
    private $uidService;

    /** @var ProvidesBreadcrumbInApplicationHttpResponse */
    private $subscriber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uidService = $this->createMock(UidServiceInterface::class);
        $this->subscriber = new ProvidesBreadcrumbInApplicationHttpResponse(
            $this->headerName,
            $this->uidService
        );
    }

    public function testGetSubscribedEvents(): void
    {
        $events = ProvidesBreadcrumbInApplicationHttpResponse::getSubscribedEvents();

        self::assertEquals([ResponseEvent::class => 'onKernelResponse'], $events);
    }

    public function testInjectUidOnKernelResponseIfHeaderAlreadyExists(): void
    {
        $response = new Response();
        $response->headers->set($this->headerName, 'some-already-existing-uid');

        $event = new ResponseEvent(
            $this->createMock(KernelInterface::class),
            new Request(),
            1,
            $response
        );

        $this->subscriber->onKernelResponse($event);

        self::assertEquals('some-already-existing-uid', $response->headers->get($this->headerName));
    }

    public function testInjectUidOnKernelResponse(): void
    {
        $this->uidService
            ->expects(self::once())
            ->method('getUid')
            ->willReturn('some-new-uid');

        $response = new Response();

        $event = new ResponseEvent(
            $this->createMock(KernelInterface::class),
            new Request(),
            1,
            $response
        );

        $this->subscriber->onKernelResponse($event);

        self::assertEquals('some-new-uid', $response->headers->get($this->headerName));
    }
}
