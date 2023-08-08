<?php

namespace App\Console\Commands;

use App\Enums\Shipment\EventGroup;
use App\Enums\Shipment\MajorExpress\Event;
use App\Models\InvoiceShipmentDetail;
use App\Models\ShipmentTrackInfo;
use App\Process\DeliveryStatus\Observe;
use Illuminate\Console\Command;

class FillUpShipmentDetailEventInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill-up:shipment-detail_event-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Observe $observer): int
    {
        $info_items = ShipmentTrackInfo::where('event_group', EventGroup::DELIVERED->value)->get();

        $unique_details = [];
        foreach ($info_items as $info_item){

            $detail = InvoiceShipmentDetail::find($info_item->shipment_id);
            if (!isset($unique_details[$detail->id])){
                $unique_details[] = $detail;
            }

            $detail->last_event_group = EventGroup::DELIVERED->value;
            $detail->delivery_date = $info_item->event_date;

            $detail->save();
        }

        $details = InvoiceShipmentDetail::where('last_event_group', null)->get();
        foreach ($details as $detail){
            $latest_event = ShipmentTrackInfo::where('shipment_id', $detail->id)
                ->orderBy('id', 'desc')
                ->limit(1)
                ->first();

            if ($latest_event === null) {
                continue;
            }

            $details->last_event_group = $latest_event->event_group;
            $details->save();
        }

        $details = InvoiceShipmentDetail::where('transport_company_id', 'IS NOT', null)
            ->where('last_event_group', null)
            ->get();
        foreach ($details as $detail){
            $observer->observe($detail);
        }

        return Command::SUCCESS;
    }
}
