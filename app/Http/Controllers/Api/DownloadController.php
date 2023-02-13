<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Models\Document;
use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadArchiveRequest;
use App\Http\Requests\downloadFileRequest;
use App\Models\Profile;
use App\Services\DocumentServices;
use App\Services\UserService;
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
    public function downloadOrderArchive(DownloadArchiveRequest $request) : JsonResponse|BinaryFileResponse
    {
        $downloadRequest = $request->validated();
        $userService = new UserService();
        $email_hash = $userService->encryptUserData($downloadRequest['email']);

        $profile = Profile::where(['email' => $email_hash])->first();

        if (!is_null($profile)) {
            $docService = new DocumentServices();

            $order_id = $downloadRequest['order_id'];
            $documents = Document::where(['order_id' => $order_id])
                ->where('extension', '!=', 'zip')
                ->get();

            if (count($documents) > 0) {
                $storage = storage_path("app/documents/orders/$order_id");
                $filename = uniqid();

                $zip = new \ZipArchive;
                $zip_status = $zip->open($storage . "/" . $filename, \ZipArchive::CREATE);

                if ($zip_status === true) {

                    $tmpFiles = [];
                    foreach ($documents as $document) {

                        $file = fopen($storage . "/" . $document->filename, 'r');
                        $decrypt = $docService->decrypt($file, $profile->password);

                        $tmp = uniqid() . "_" . $document->filename;
                        $tmpFile = fopen($storage . "/" . $tmp, "a+");
                        fwrite($tmpFile, $decrypt);
                        fclose($tmpFile);

                        $zip->addFile($storage . "/" . $tmp, $tmp);

                        array_push($tmpFiles, $tmp);
                    }

                    $zip->close();

                    $docService->encryptArchive($storage . "/" . $filename, $profile->password);

                    register_shutdown_function(array($this, 'clearRepo'), $order_id, $filename, $tmpFiles);

                    return response()->file($storage . "/" . $filename);

                } else {
                    return response()->json("Failed opening archive: " . @$zip->getStatusString() . " (code: " . $zip_status . ")");
                }
            }
        } else {
            return new JsonResponse(['error' => 'Profile is undefined']);
        }

        return new JsonResponse(['error' => 'Cannot execute method using this data.']);
    }

    private function clearRepo($order_id, $tmpZip, $tmpFiles) {
        $storage = storage_path("app/documents/orders/$order_id/");

        unlink($storage . $tmpZip);

        foreach ($tmpFiles as $file) {
            unlink($storage . $file);
        }
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
