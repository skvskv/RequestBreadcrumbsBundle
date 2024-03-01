<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestBasedUidServiceTest extends TestCase
{
    /** @var string */
    private $headerName = 'someHeaderName';

    /** @var RequestStack&MockObject */
    private $requestStack;

    /** @var BasicUidService */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->service = new BasicUidService($this->headerName, $this->requestStack);
    }

    public function testGetUidWhenThereIsNoRequest(): void
    {
        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $uid = $this->service->getUid();

        self::assertNotEmpty($uid);
        self::assertEquals(32, strlen($uid));
    }

    public function testGetUidWhenThereIsNoTraceInRequest(): void
    {
        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request = new Request());

        $uid = $this->service->getUid();
        $secondUid = $this->service->getUid();

        self::assertNotEmpty($uid);
        self::assertEquals(32, strlen($uid));
        self::assertSame($uid, $secondUid);
    }

    public function testGetUid(): void
    {
        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request = new Request());

        $request->headers->set($this->headerName, 'some-request-id');

        $uid = $this->service->getUid();

        self::assertSame('some-request-id', $uid);

        $uidFromSecondCall = $this->service->getUid();

        self::assertSame($uid, $uidFromSecondCall);
    }

    public function testReset(): void
    {
        $this->requestStack
            ->expects(self::atLeastOnce())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $uid = $this->service->getUid();

        $this->service->reset();

        $secondUid = $this->service->getUid();

        self::assertNotSame($uid, $secondUid);
    }


    public function testResetSkipIfRequestStackExists(): void
    {
        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request = new Request());

        $request->headers->set($this->headerName, 'some-request-id');

        $uid = $this->service->getUid();

        self::assertSame('some-request-id', $uid);

        $this->service->reset();

        $uidFromSecondCall = $this->service->getUid();

        self::assertSame($uid, $uidFromSecondCall);
    }
}
