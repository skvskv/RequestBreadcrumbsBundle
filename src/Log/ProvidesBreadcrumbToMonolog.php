<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle\Log;

use skvskv\LogBreadcrumbsBundle\Service\UidServiceInterface;

class ProvidesBreadcrumbToMonolog
{
    public function __construct(private UidServiceInterface $uidService)
    {
    }

    public function __invoke(array $record): array
    {
        $record['context']['trace_id'] = $this->uidService->getUid();
        return $record;
    }
}
