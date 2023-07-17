<?php

namespace App\Jobs\DeliveryServices\MajorExpress;

use App\Enums\DeliveryService;
use App\Models\ShipmentTrackInfo;
use App\Packages\DeliveryServices\MajorExpress\MajorExpress;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class FetchResultFromQueue implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $api_endpoint = 'https://manager.fluid-line.ru/api/majorexpress/invoice/everything';
    private readonly string $api_key;
    private readonly string $deliveryId;

    private string $final_status = 'Груз доставлен получателю';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private int $shipmentDetailId,
        string $uniformDeliveryId
    ) {
        $this->api_key = config('services.major_express.api_key');
        $this->deliveryId = $uniformDeliveryId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $api = $this->getMajorExpressApi($this->api_key);
        $response = $api->orders()->fetchStatus($this->deliveryId);

        $consolidatedCargo = $response['major_express']['Сборные грузы'];
        $expressCargo = $response['major_express']['Экспресс-доставка'];

        if (!empty($consolidatedCargo)) {
            $cargo = $consolidatedCargo;
        } else {
            $cargo = $expressCargo;
        }

        if (empty($cargo)) {
            throw new RuntimeException('Не найдено событий по грузу');
        }

        if (!isset($cargo['major_invoice']['invoice_history'])) {
            throw new RuntimeException('Не найдено поле `invoice_history`');
        }

        $history = $cargo['major_invoice']['invoice_history'];
        if (count($history) <= 1) {
            throw new RuntimeException('Список истории груза пуст');
        }

        ShipmentTrackInfo::where('shipment_id', $this->shipmentDetailId)->delete();

        // пропускаем элемент с названиями полей
        array_shift($history);

        $lastEventTitle = '';
        foreach ($history as $historyItem) {

            $eventDateTime = Carbon::parse($historyItem[2] . ' ' . $historyItem[3]);

            ShipmentTrackInfo::create([
                'shipment_id' => $this->shipmentDetailId,
                'transport_company' => DeliveryService::MAJOR_EXPRESS->value,
                'event_title' => $historyItem[0],
                'event_current_geo' => $historyItem[1],
                'event_date' => $eventDateTime,
            ]);

            $lastEventTitle = $historyItem[0];
        }

        if($lastEventTitle === $this->final_status) {
            return;
        }

        static::dispatch($this->shipmentDetailId, $this->deliveryId)->delay(now()->addHours(6));
    }

    private function getMajorExpressApi(string $deliveryId): MajorExpress
    {
        return new MajorExpress($deliveryId);
    }
}
