<?php

namespace App\Services;

use App\Enums\Section;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentServices
{
    public function __construct()
    {
        return $this;
    }

    public function getData(Document $document, string $file, Section $section, string $user_password,
                            string $archive_password)
    {
        $storage = storage_path("app/documents/orders/{$document->order_id}/");

        /**
         * Сохранение документа в базу данных
         */
        $this->dbSaveDoc($document, $section);

        /**
         * Сохранение документа в директорию
         */
        $this->saveInvoiceFile($document, $file);

        /**
         * Распаковка invoice файла ZIP
         */
        $this->unpack($storage, $document, $archive_password);

        /**
         * Сохранение данных о новых файлах
         */
        $this->scanNewFiles($document, $storage);

        /**
         * Создание нового архива
         */
        $this->pack($storage, $document->section, $document->filename);

        /**
         * Зашифровка архива
         */
        $this->encrypt($storage, $document, $user_password, $document->filename);
        /**
         * Зашифровка новых файлов
         */
        $this->encrypt($storage, $document, $user_password);
    }

    private function dbSaveDoc(Document $doc, Section $section)
    {
        $doc->section = $section->getSection();

        $fileinfo = pathinfo($doc->filename);
        $doc->extension = $fileinfo['extension'];
        $doc->filename =  str_replace(['.', " "], '', $fileinfo['filename']).".".$fileinfo['extension'];

        Document::updateOrCreate(
            ['filename' => $doc->filename, 'section' => $doc->section],
            [
                'order_id' => $doc->order_id,
                'filename' => $doc->filename,
                'extension' => $fileinfo['extension'],
                'section' => $doc->section,
                'updated_at' => time() // Почему-то не записывает, видимо необходимо изменять данные.
            ]
        );
    }

    private function saveInvoiceFile(Document $doc, string $fileBase64)
    {
        $storage = Storage::disk('orders');
        $storage->makeDirectory($doc->order_id);
        $storage->put("/{$doc->order_id}/".$doc->filename, base64_decode($fileBase64));
    }

    private function pack($storage, $section, $filename)
    {
        unlink($storage.$filename);
        $zip = new \ZipArchive;
        $zip_status = $zip->open($storage.$filename, \ZipArchive::CREATE);

        if ($zip_status === true) {

            $files = array_diff(scandir($storage), ['.', '..']);
            foreach ($files as $file) {
                $info = pathinfo($file);

                if (isset($info['extension']) and $info['extension'] != "zip") {
                    if (preg_match("/$section/", $file)) {
                        $zip->addFile($storage.$file, $file);
//                        $zip->setEncryptionName($storage.$file, \ZipArchive::EM_AES_256, 'password');
                    }
                }
            }
            $zip->close();
        } else {
            return response()->json("Failed opening archive: ". @$zip->getStatusString() . " (code: ". $zip_status .")");
        }
    }

    private function unpack($storage, $document, $password)
    {
        $file = $storage.$document->filename;
        $info = pathinfo($file);

        if (isset($info['extension']) and $info['extension'] == "zip") {
            $zip = new \ZipArchive;
            $zip_status = $zip->open($file);

            if ($zip_status === true) {
                if ($zip->setPassword($password)) {
                    if (!$zip->extractTo($storage)) {
                        return response()->json("Extraction failed (wrong password?)");
                    }
                }

                $zip->close();
            } else {
                return response()->json("Failed opening archive: ". @$zip->getStatusString() . " (code: ". $zip_status .")");
            }
        }
    }

    private function encrypt($storage, $document, $user_password, $filename = "")
    {
        $files = scandir($storage);

        if (empty($filename)) {
            foreach ($files as $value) {
                $info = pathinfo($value);

                if ($info['extension'] != "zip") {
                    if (preg_match("/{$document->section}/", $info['filename'])) {
                        $content = file_get_contents($storage.$value);

                        $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
                        $iv = openssl_random_pseudo_bytes($ivlen);

                        $ciphertext = @openssl_encrypt($content, 'AES-256-CBC', $user_password, true, $iv);
                        file_put_contents($storage.$value, $iv.$ciphertext);
                    }
                }
            }
        } else {
            $content = file_get_contents($storage.$filename);

            $ivlen = openssl_cipher_iv_length($cipher="AES-256-CTR");
            $iv = openssl_random_pseudo_bytes($ivlen);

            $ciphertext = @openssl_encrypt($content, 'AES-256-CTR', $user_password, true, $iv);
            file_put_contents($storage.$filename, $iv.$ciphertext);
        }
    }
    private function decrypt()
    {
//        $file = file_get_contents("test");
//        $key = "123";
//        $ciphertext = openssl_decrypt($file, 'AES-256-CTR', $key, true);
//        file_put_contents("decrypt.zip", $ciphertext);
    }

    private function scanNewFiles(Document $parent, $storage)
    {
        $files = array_diff(scandir($storage), [".", ".."]);

        foreach ($files as $value) {
            $info = pathinfo($value);

            if ($info['extension'] != "zip") {
                Document::updateOrCreate(
                    ['filename' => $value, 'section' => $parent->section],
                    [
                        'order_id' => $parent->order_id,
                        'filename' => $value,
                        'extension' => $info['extension'],
                        'section' => $parent->section,
                        'updated_at' => time() // Почему-то не записывает, видимо необходимо изменять данные.
                    ]
                );
            }
        }
    }
}
