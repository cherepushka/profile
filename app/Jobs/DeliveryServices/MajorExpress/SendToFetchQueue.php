<?php

namespace App\Jobs\DeliveryServices\MajorExpress;

use App\Jobs\DeliveryServices\FetchStatusJobInterface;
use App\Models\InvoiceShipmentDetail;
use App\Packages\DeliveryServices\MajorExpress\MajorExpress;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendToFetchQueue implements ShouldQueue, FetchStatusJobInterface
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private readonly int $shipmentDetailId;
    private readonly string $api_key;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $shipmentDetailId)
    {
        $this->shipmentDetailId = $shipmentDetailId;

        $this->api_key = config('services.major_express.api_key');
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $shipmentDetail = InvoiceShipmentDetail::find($this->shipmentDetailId);
        if (!$shipmentDetail){
            throw new \RuntimeException('InvoiceShipmentDetail на найден по id ' . $this->shipmentDetailId);
        }

        $deliveryId = 'L' . $this->trimToUniform($shipmentDetail->transport_company_id);

        $majorExpressApi = $this->getMajorExpressApi($this->api_key);

        $majorExpressApi->orders()->enqueue($deliveryId);

        FetchResultFromQueue::dispatch($shipmentDetail->id, $deliveryId)->delay(now()->addMinutes(20));
    }

    private function trimToUniform(string $deliveryId): string
    {
        return preg_replace('#^[a-z]+#ui', '', $deliveryId);
    }

    private function getMajorExpressApi(string $deliveryId): MajorExpress
    {
        return new MajorExpress($deliveryId);
    }
}
