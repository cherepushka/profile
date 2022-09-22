<?php

namespace App\Services;

use App\Enums\Section;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;

class DocumentServices
{
    /**
     * @param Document $document
     * @param string $file - Контент файла base64
     * @param Section $section
     */
    public function __construct(Document $document, string $file, Section $section, string $user_password,
                                string $archive_password)
    {
        $document->section = $section->getSection();

        $fileinfo = pathinfo($document->filename);
        $document->extension = $fileinfo['extension'];
        $document->filename =  str_replace(['.', " "], '', $fileinfo['filename']).".".$fileinfo['extension'];

        $document->save();

        $storage = Storage::disk('orders');
        $storage->makeDirectory($document->order_id);
        $storage->put("/{$document->order_id}/".$document->filename, base64_decode($file));

        $storage = storage_path('app/documents/orders');

        $this->unpack($storage, $document, $archive_password);
        $this->encrypt($storage, $document, $user_password);
    }

    private function pack($pack)
    {
//        $zip =
//        $time = time();
//
//        $file = "$time.zip";
//
//        $zip = new ZipArchive;
//        $res = $zip->open($file, ZipArchive::CREATE); //Add your file name
//        if ($res === TRUE) {
//            $zip->addFile('Коммерческое предложение №31388 от 21 сентября 2022 г..pdf');
//            $zip->setEncryptionName('Коммерческое предложение №31388 от 21 сентября 2022 г..pdf', ZipArchive::EM_AES_256, '123'); //Add file name and password dynamically
//            $zip->close();
//            echo 'ok';
//        } else {
//            echo 'failed';
//        }
    }

    private function unpack($storage, $document, $password)
    {
        $filepath = "$storage/{$document->order_id}/";

        $zip = new \ZipArchive;
        $zip_status = $zip->open($filepath.$document->filename);

        if ($zip_status === true) {
            if ($zip->setPassword($password)) {
                if (!$zip->extractTo($filepath)) {
                    echo ("Extraction failed (wrong password?)"); // ? Придумать возможность отобразить ?
                }
            }

            $zip->close();
        } else {
            echo ("Failed opening archive: ". @$zip->getStatusString() . " (code: ". $zip_status .")"); // ? Придумать возможность отобразить ?
        }
    }

    private function encrypt($storage, $document, $user_password)
    {
        $files = scandir("$storage/{$document->order_id}/");

        foreach ($files as $value) {
            $info = pathinfo($value);
            if (isset($info['extension']) and $info['extension'] != "zip") {
                if (preg_match("/{$document->section}/", $info['filename'])) {
                    $content = file_get_contents("$storage/{$document->order_id}/".$value);

//                    $ivlen = openssl_cipher_iv_length($cipher="AES-256-CTR");
//                    $iv = openssl_random_pseudo_bytes($ivlen);
                    try {
                        $ciphertext = @openssl_encrypt($content, 'AES-256-CTR', $user_password, true);
                        file_put_contents("$storage/{$document->order_id}/$value", $ciphertext);
                    } catch (\Exception $e) {
                        dd($e);
                    }
//                    file_put_contents("$storage/{$document->order_id}/{$document->section}-block-len", ($ivlen));
//                    file_put_contents("$storage/{$document->order_id}/{$document->section}-block-v", ($iv));

                }
            }
        }


    }
    private function decrypt()
    {
//        $file = file_get_contents("test");
//        $key = "123";
//
//        $ciphertext = openssl_decrypt($file, 'AES-256-CTR', $key, true);
//
//        file_put_contents("decrypt.zip", $ciphertext);
    }
}
