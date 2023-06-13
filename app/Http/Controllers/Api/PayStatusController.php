<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayStatusRequest;
use App\Http\Traits\MapTrait;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\InvoicePaymentItem;
use App\Models\InvoiceShipment;
use App\Models\InvoiceShipmentDetail;
use App\Models\InvoiceShipmentDetailItem;
use App\Models\Profile;
use App\Models\ProfileInternal;
use App\Services\DocumentServices;
use App\Services\PaymentService;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;

class PayStatusController extends Controller
{
    use MapTrait;

    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly ShipmentService $shipmentService,
    ){}

    /**
     * Получение json об обновлении статуса заказа
     *
     * @param PayStatusRequest $request
     * @return JsonResponse
     */
    public function updateStatus(PayStatusRequest $request): JsonResponse
    {
        /**
         * Получение валидированных параметров запроса
         * @var PayStatusRequest $payStatusRequest
         */
        $payStatusRequest = $request->validated();

        if(!isset($payStatusRequest['data']) && !isset($payStatusRequest['data_shipment'])){
            $this->paymentService->setIsPaid($request->get('order_id'));

            return response()->json(['status' => 'ok']);
        }

        if(isset($payStatusRequest['data'])){
            foreach ($payStatusRequest['data'] as $dataShipment){
                $this->paymentService->savePaymentInfo($dataShipment);
            }
        }

        if(isset($payStatusRequest['data_shipment'])){

            foreach ($payStatusRequest['data_shipment'] as $dataShipment){
                $this->shipmentService->saveShipmentInfo($dataShipment);

                $this->createShipmentFiles($dataShipment['order_id'], $dataShipment['shipment_file']);
            }
        }

        return response()->json(['status' => 'ok']);
    }


    /**
     * Создание файлов отгрузки
     *
     * @param $order_id
     * @param $filesData
     * @return void
     */
    private function createShipmentFiles($order_id, $filesData): void
    {
        $hash = $this->getUserHash($order_id);

        if (is_null($hash)) {
            return;
        }

        $docService = new DocumentServices();
        $document = new Document;

        $array = $filesData += ['order_id' => $order_id];
        $docService->getData($document->map($array), $filesData['file_data'], Section::SHIPMENT,  $hash, $filesData['file_pswd']);
    }

    /**
     * Получение Хэша пользователя для создания архива отгрузки
     *
     * @param string $order_id
     * @return string|null
     */
    private function getUserHash(string $order_id) : ?string
    {
        $invoice = Invoice::where(['order_id' => $order_id])->first();
        if (is_null($invoice)) {
            return null;
        }

        $profileInternal = ProfileInternal::where(['internal_id' => $invoice->user_id])->first();
        if (is_null($profileInternal)) {
            return null;
        }

        $profile = Profile::where(['id' => $profileInternal->profile_id])->first();
        if (!is_null($profile)) {
            return $profile->password;
        }

        return null;
    }
}
