<?php

namespace App\Process\DeliveryStatus;

use App\Enums\DeliveryService;
use App\Jobs\DeliveryServices\FetchStatusJobInterface;
use App\Jobs\DeliveryServices\MajorExpress\SendToFetchQueue;
use App\Models\InvoiceShipmentDetail;
use Illuminate\Contracts\Queue\ShouldQueue;

class Observe
{
    /**
     * @var array<class-string<ShouldQueue&FetchStatusJobInterface>>
     */
    private array $availableWatchers = [
        DeliveryService::MAJOR_EXPRESS->value => SendToFetchQueue::class
    ];

    public function observe(InvoiceShipmentDetail $shipmentDetail): void
    {
        $service = DeliveryService::match($shipmentDetail->transport_company);
        if(!$service) {
            return;
        }

        if (!isset($this->availableWatchers[$service->value])) {
            return;
        }

        $watcherClass = $this->availableWatchers[$service->value];

        $job = forward_static_call_array([$watcherClass, 'dispatch'], [$shipmentDetail->id]);
        $job->delay(now()->addMinute());
    }

}
