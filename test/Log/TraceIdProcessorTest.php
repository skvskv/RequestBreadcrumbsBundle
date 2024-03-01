<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle\Log;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use skvskv\LogBreadcrumbsBundle\Service\UidServiceInterface;

class TraceIdProcessorTest extends TestCase
{
    /** @var MockObject&UidServiceInterface */
    private $uidService;

    /** @var ProvidesBreadcrumbToMonolog */
    private $traceIdProcessor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uidService = $this->createMock(UidServiceInterface::class);
        $this->traceIdProcessor = new ProvidesBreadcrumbToMonolog($this->uidService);
    }

    public function testProcess(): void
    {
        $logData = ['some key' => 'some log data'];

        $this->uidService
            ->expects(self::atLeastOnce())
            ->method('getUid')
            ->willReturn('some-uid');

        $result = ($this->traceIdProcessor)($logData);

        self::assertEquals([
            'some key' => 'some log data',
            'context' => [
                'trace_id' => 'some-uid',
            ],
        ], $result);
    }
}
