<?php

namespace App\Console\Commands;

use App\Enums\Shipment\MajorExpress\Event;
use App\Models\ShipmentTrackInfo;
use Illuminate\Console\Command;

class FillUpEventGroupes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill-up:event_group';

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
    public function handle()
    {
        $info_items = ShipmentTrackInfo::where('event_group', null)->get();

        foreach ($info_items as $info_item){
            $info_item->event_group = Event::matchEvent($info_item->event_title)->getEventGroup()->value;
            $info_item->save();
        }

        return Command::SUCCESS;
    }
}
