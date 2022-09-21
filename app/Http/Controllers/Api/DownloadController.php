<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DownloadController extends Controller
{
    /**
     * Скачать 1 определенный файл
     *
     * request json_body [ 'order_id' => 0 ]
     * @param $docType
     * @param $docId
     * @return JsonResponse Base64 зашифрованный файл с помощью хэша пользователя
     */
    public function downloadFileById($docId)
    {
        $path = storage_path('app/documents/test-encrypted.zip');
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
