<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function downloadFileById($docType, $docId): JsonResponse
    {
        return response()->json([]);
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
