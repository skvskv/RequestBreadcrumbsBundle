<?php

declare(strict_types=1);

namespace skvskv\LogBreadcrumbsBundle\EventListener;

use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use skvskv\LogBreadcrumbsBundle\Service\UidServiceInterface;

/**
 * A Symfony subscriber of kernel.event_listener kind.
 * Listens for the eight_points_guzzle.pre_transaction event and injects breadcrumb (uid)
 * into Guzzle HTTP Request as header (X-Breadcrumb)
 *
 */
class ProvidesBreadcrumbForGuzzleCommunication
{
    public function __construct(private string $headerName, private UidServiceInterface $uidService)
    {
    }

    /**
     * Adds breadcrumb header to guzzle-emitted requests
     *
     * @param PreTransactionEvent $event
     */
    public function onEightPointsGuzzleRequest(PreTransactionEvent $event): void
    {
        $modifiedRequest = $event->getTransaction()
            ->withHeader($this->headerName, $this->uidService->getUid());
        $event->setTransaction($modifiedRequest);
    }
}
