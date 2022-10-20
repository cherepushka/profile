<?php

namespace App\Services;

use App\Enums\Section;
use App\Models\Document;
use Exception;
use Illuminate\Support\Facades\Storage;

class DocumentServices
{
    public function __construct()
    {
        return $this;
    }

    /**
     * Обработка информации о документах
     *
     * @param Document $document - Экземпляр модели Document из модели Invoice.
     * Прошедший предварительную валидацию и обработку методом map()
     * @param string $file - Файл преобразованный в base64 строку
     * @param Section $section - Перечисление секций загрузки
     * @param string $user_password - Хэш, пароль пользователя
     * @param string $archive_password - Пароль от архива
     * @return void
     */
    public function getData(
        Document $document, 
        string $file, 
        Section $section, 
        string $user_password,
        string $archive_password
    ): void
    {
        /**
         * Путь к активной директории
         */
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

    /**
     * Сохранение полученного из данных файла в базу данных
     *
     * @param Document $doc
     * @param Section $section
     * @return void
     */
    private function dbSaveDoc(Document $doc, Section $section): void
    {
        $doc->section = $section->getSection();

        $fileinfo = pathinfo($doc->filename);
        $doc->extension = $fileinfo['extension'];
        /**
         * Требует дополнительной проверки
         */
        //$doc->filename =  str_replace(['.', " "], '', $fileinfo['filename']).".".$fileinfo['extension'];

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

    /**
     * Сохранение файла с полученных данных
     *
     * @param Document $doc
     * @param string $fileBase64
     * @return void
     */
    private function saveInvoiceFile(Document $doc, string $fileBase64): void
    {
        $storage = Storage::disk('orders');
        $storage->makeDirectory($doc->order_id);
        $storage->put("/{$doc->order_id}/".$doc->filename, base64_decode($fileBase64));
    }

    /**
     * Создание нового архива
     *
     * @param $storage
     * @param $section
     * @param $filename
     * @return void
     */
    private function pack($storage, $section, $filename): void
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
            response()->json("Failed opening archive: " . @$zip->getStatusString() . " (code: " . $zip_status . ")");
        }
    }

    /**
     * Распаковка архива
     *
     * @param string $storage
     * @param Document $document
     * @param string $password
     * @return void
     */
    private function unpack(string $storage, Document $document, string $password): void
    {
        $file = $storage.$document->filename;
        $info = pathinfo($file);

        if (isset($info['extension']) and $info['extension'] == "zip") {
            $zip = new \ZipArchive;
            $zip_status = $zip->open($file);

            if ($zip_status === true) {
                if ($zip->setPassword($password)) {
                    if (!$zip->extractTo($storage)) {
                        response()->json("Extraction failed (wrong password?)");
                        return;
                    }
                }

                $zip->close();
            } else {
                response()->json("Failed opening archive: " . @$zip->getStatusString() . " (code: " . $zip_status . ")");
            }
        }
    }

    /**
     * Шифрование openssl файлов и каталогов.
     *
     * @param $storage - Путь до директории
     * @param $document - Модель документа
     * @param $user_password - Хэш пароль пользователя
     * @param string $filename - Обязательный параметр, при кодировки архива или заданного файла.
     * @return void
     */
    private function encrypt(string $storage, Document $document, string $user_password, string $filename = ""): void
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
                        file_put_contents($storage.$value, $iv.$ciphertext); // Не забыть удалять 16 байт в скаченном файле
                    }
                }
            }
        } else {
            $content = file_get_contents($storage.$filename);

            $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);

            $ciphertext = @openssl_encrypt($content, 'AES-256-CBC', $user_password, true, $iv);
            file_put_contents($storage.$filename, $iv.$ciphertext); // Не забыть удалять 16 байт в скаченном файле
        }
    }

    /**
     * Расшифрока файлов и каталогов
     *
     * @return never
     * @throws Exception
     * @deprecated
     */
    private function decrypt(): void
    {
        /* Последовательность расшифровки посредством языка PHP

            $content - @string / Получить архив
            $iv = @string / Векторное смещение => fread($сcntent, 16); / Считать первые 16 байт
            $key = @string / Пароль пользователя;
            $zip = fopen("here.zip", "r");
            $ciphertext = openssl_decrypt(
                stream_get_contents($file), / Обрезание первых символов
                'AES-256-CBC', / Метод кодировки
                $key, true, $iv
            );
        */
        throw new Exception('Deprecated function not allowed to execute.', 404);
    }

    /**
     * Просмотр директории после распаковки и добавление файлов в базу данных
     *
     * @param Document $parent
     * @param $storage
     * @return void
     */
    private function scanNewFiles(Document $parent, $storage): void
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
