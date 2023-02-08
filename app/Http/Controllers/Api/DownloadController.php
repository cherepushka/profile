<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    /**
     * Скачать документ, который принадлежит пользователю, отбор по id документа
     *
     * @param $documentId
     * @return BinaryFileResponse - зашифрованный файл с помощью OpenSSL и Хэша пароля пользователя
     */
    public function downloadFileById($documentId): BinaryFileResponse
    {
        $fileDocument = Document::where('id', $documentId)->first();

        if (isset($fileDocument->id)) {
            $path = storage_path("app/documents/orders/{$fileDocument->order_id}/$fileDocument->filename");
        }

        return response()->file($path);
    }

    /**
     * Скачать все файлы из группы в 1 архиве
     *
     * request json_body [ 'order_id' => 0 ]
     * @param $docType
     * @return JsonResponse
     */
    public function downloadFiles($docType): JsonResponse
    {
        return response()->json([]);
    }

    /**
     * Скачать все файлы к заказу
     *
     * request json_body [ 'order_id' => 0 ]
     * @return JsonResponse
     */
    public function downloadGeneralArchive(): JsonResponse
    {
        return response()->json([]);
    }
}
