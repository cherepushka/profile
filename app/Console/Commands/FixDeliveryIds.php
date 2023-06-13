<?php

namespace App\Console\Commands;

use App\Models\InvoiceShipmentDetail;
use http\Exception\RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ReflectionException;

class FixDeliveryIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixDeliveryIds';

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
     * @throws ReflectionException
     */
    public function handle()
    {
        $jobs = DB::select(DB::raw("SELECT * FROM failed_jobs WHERE `exception` LIKE 'RuntimeException: Не найдено событий по грузу%'"));

        foreach ($jobs as $job){
            $payload = json_decode($job->payload);
            $command = unserialize($payload->data->command);

            $jobObj = new \ReflectionClass($command);
            $p = $jobObj->getProperty('shipmentDetailId');
            $p->setAccessible(true);
            $shipmentDetailId = $p->getValue($command);
            $p->setAccessible(false);

            $shipmentDetail = InvoiceShipmentDetail::find($shipmentDetailId);

            if(!preg_match("#deliveryId\"\;(s\:\d+)\:\"([\da-z]+)\"#iu", $payload->data->command, $mathces)){
                throw new RuntimeException('Невалидная регулярка');
            }

            $payload->data->command = str_replace(
                $mathces[0],
                'deliveryId";s:' . strlen($shipmentDetail['transport_company_id']) . ':"' . $shipmentDetail['transport_company_id'] . '"',
                $payload->data->command
            );
            $job->payload = json_encode($payload);

            unserialize($payload->data->command);

            DB::statement("UPDATE failed_jobs SET `payload` = :payload WHERE `uuid` = :uuid", [
                'uuid' => $job->uuid,
                'payload' => $job->payload
            ]);
        }
        return Command::SUCCESS;
    }
}
