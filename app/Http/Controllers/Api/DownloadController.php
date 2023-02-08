<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Http\Controllers\Controller;
use App\Http\Requests\downloadFileRequest;
use App\Models\Document;
use App\Services\DocumentServices;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    /**
     * Скачать документ, который принадлежит пользователю, отбор по id документа
     *
     * @param int $docId Номер документа
     * @return JsonResponse|BinaryFileResponse - зашифрованный файл с помощью OpenSSL и Хэша пароля пользователя
     */
    public function downloadFileById(DownloadFileRequest $request, int $docId) : JsonResponse|BinaryFileResponse
    {
        $downloadRequest = $request->validated();

        $document = Document::where(['order_id' => $downloadRequest['order_id'], 'id' => $docId])->first();

        return $this->getFile($document);
    }

    /**
     * Скачать все файлы из группы в 1 архиве
     *
     * request json_body [ 'order_id' => 0 ]
     * @param $docType
     * @return JsonResponse
     */
    public function downloadSectionArchive(DownloadFileRequest $request, $docType) : JsonResponse|BinaryFileResponse
    {
        $downloadRequest = $request->validated();

        foreach (Section::cases() as $case) {
            if ($case->name == strtoupper($docType)) {
                $docType = $case->getSection();
                break;
            }
        }
        $document = Document::where(['order_id' => $downloadRequest['order_id'], 'section' => $docType, 'extension' => 'zip'])->first();

        return $this->getFile($document);
    }

    /**
     * Скачать все файлы к заказу
     *
     * request json_body [ 'order_id' => 0 ]
     * @return JsonResponse
     */
    public function downloadOrderArchive(DownloadFileRequest $request) : JsonResponse
    {
//        $password = 'ae83b45cae85c7f64a5d85d4b2612c1a9038f79097a6478cc7b8d3fc2ac15f44';
//
//        $downloadRequest = $request->validated();
//
//        $documents = Document::where(['order_id' => $downloadRequest['order_id']])
//            ->where('extension', '!=', 'zip')
//            ->get();
//
//        foreach ($documents as $document) {
//            $docService = new DocumentServices();
//            $file = fopen(storage_path("app/documents/orders/{$document->order_id}/{$document->filename}"), 'r');
//
//            $decrypt = $docService->decrypt($file, $password);
//            dd($decrypt);
//        }
//
        return response()->json([]);
    }

    private function getFile($document) : JsonResponse|BinaryFileResponse
    {
        if (!is_null($document)) {
            $path = storage_path("app/documents/orders/{$document->order_id}/{$document->filename}");

            return response()->file($path);
        }

        return response()->json(['error' => 'File is not isset']);
    }
}
