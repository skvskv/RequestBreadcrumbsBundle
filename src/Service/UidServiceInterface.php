<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle\Service;

interface UidServiceInterface
{
    public function createUid(): string;

    public function getUid(): string;
}
