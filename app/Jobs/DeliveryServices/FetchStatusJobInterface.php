<?php

namespace App\Jobs\DeliveryServices;

interface FetchStatusJobInterface
{

    public function __construct(int $shipmentDetailId);

}
