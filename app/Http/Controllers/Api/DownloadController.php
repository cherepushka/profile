<?php

namespace App\Http\Controllers\Api;

use App\Models\Document;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\DocumentServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Скачать документ, который принадлежит пользователю, отбор по id документа
     *
     * @param int $docId Номер документа
     * @return StreamedResponse|JsonResponse - зашифрованный файл с помощью OpenSSL и Хэша пароля пользователя
     */
    public function downloadFileById(int $docId): StreamedResponse|JsonResponse
    {
        $document = Document::where(['id' => $docId])->first();

        $internalIds = $this->getUserInternalIds();

        $invoice = Invoice::where('order_id', $document->order_id)
            ->whereIn('user_id', $internalIds)
            ->first();

        // Проверка на то, что заказ принадлежит пользователю
        if(is_null($invoice)) {
            return response()->json(['message' => 'Заказ пользователя не найден'], 401);
        }

        return Storage::disk('orders')->download($document->order_id . '/'. $document->filename);
    }

    /**
     * Скачать все файлы к заказу
     */
    public function downloadOrderArchive(string $orderId): JsonResponse|BinaryFileResponse
    {
        $profile = auth()->user();
        $docService = new DocumentServices();

        if (is_null($profile)) {
            return new JsonResponse(['error' => 'Profile is undefined'], 500);
        }

        $documents = Document::where(['order_id' => $orderId])
            ->where('extension', '!=', 'zip')
            ->get();

        if (count($documents) <= 0) {
            return new JsonResponse(['error' => 'Cannot execute method using this data.'], 500);
        }

        $zipPath = $docService->archiveEncryptedDocuments($orderId, $documents, $profile->password);

        register_shutdown_function([$docService, 'deleteTmpArchive'], $zipPath);

        return response()->file($zipPath);
    }

}
