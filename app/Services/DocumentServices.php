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
     * @param Document $document | Экземпляр модели Document из модели Invoice. Необходимо пройти предварительную валидацию и обработку методом map()
     * @param string $file - Файл преобразованный в base64 строку
     * @param Section $section - Перечисление секций загрузки
     * @param string $user_password - Хэш, пароль пользователя
     * @param string $archive_password - Пароль от архива
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
        $zipFilesArray = $this->unpack($storage, $document, $archive_password);

        /**
         * Сохранение данных о новых файлах
         */
        $this->setZipFiles($document, $zipFilesArray);


        /**
         * Создание нового архива
         */
        $this->pack($storage, $document->filename, $zipFilesArray);


        /**
         * Зашифровка архива и новых файлов
         */
        $this->encrypt('EVERYTHING', $storage, $document, $user_password, $document->filename, $zipFilesArray);
    }

    /**
     * Сохранение полученного из данных файла в базу данных
     *
     * @param Document $doc
     * @param Section $section
     * @return void
     */
    private function dbSaveDoc(Document $document, Section $section): void
    {
        $document->section = $section->getSection();

        $fileinfo = pathinfo($document->filename);
        $document->extension = $fileinfo['extension'];

        Document::updateOrCreate(
            [
                'order_id' => $document->order_id,
                'filename' => $document->filename,
                'section' => $document->section
            ],
            [
                'extension' => $fileinfo['extension'],
                'updated_at' => time(),
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
    private function pack($storage, string $filename, array $zipFilesArray): void
    {
        unlink($storage.$filename);
        $zip = new \ZipArchive;
        $zip_status = $zip->open($storage.$filename, \ZipArchive::CREATE);

        if ($zip_status === true) {
            foreach ($zipFilesArray as $file) {
                $info = pathinfo($file);

                if (isset($info['extension']) and $info['extension'] != "zip") {
                    $zip->addFile($storage.$file, $file);
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
     */
    private function unpack(string $storage, Document $document, string $password)
    {
        $zipFileArray = [];
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

                for ($i = 0; $i < $zip->count(); $i++) {
                    array_push($zipFileArray, $zip->getNameIndex($i));
                }

                $zip->close();

                return $zipFileArray;

            } else {
                response()->json("Failed opening archive: " . @$zip->getStatusString() . " (code: " . $zip_status . ")");
            }
        }
    }

    /**
     * Шифрование openssl файлов и каталогов.
     *
     * @param string $case | Выбор шифрования: Для всего - "EVERYTHING", для архива - "ARCHIVE", для файлов - "FILES"
     * @param $storage - Путь до директории
     * @param $document - Модель документа
     * @param $user_password - Хэш пароль пользователя
     * @param string $filename - Обязательный параметр, при кодировки архива или заданного файла.
     * @return void
     */
    private function encrypt(string $case, string $storage, Document $document, string $user_password, string $zipArchiveName = "", array $zipFilesArray = []): void
    {
        switch ($case) {
            case 'EVERYTHING':
                $this->encryptArchive($storage.$zipArchiveName, $user_password);
                $this->encryptFiles($storage, $zipFilesArray, $user_password);
                break;

            case 'ARCHIVE':
                $this->encryptArchive($storage.$zipArchiveName, $user_password);
                break;

            case 'FILES':
                $this->encryptFiles($storage, $zipFilesArray, $user_password);
                break;
        }
    }

    /**
     * Шифрование нового архива
     * 
     * openssl v1.1.1 command:
     * openssl aes-256-cbc -e -salt -pbkdf2 -iter 10000 -in 'путь до входного файла' -out 'путь до выходного файла'
     * 
     * @param $filepath
     * @param $password
     */
    public function encryptArchive($filepath, $password) {
        $content = file_get_contents($filepath);
        $salt = openssl_random_pseudo_bytes(8);

        $derivatedKey = openssl_pbkdf2(
            $password,
            $salt,
            // key_length is 48 bytes because
            // the key itself is 32 bytes (256 bits, because aes 256)
            // and the IV is 16 bytes (returned by openssl_cipher_iv_length)
            // so 32+16 -> 48
            key_length: 48,
            // 10000 is a of 2021 the amount recommended by the NIST
            // see https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-63b.pdf section 5.1.1.2
            iterations: 10000,
            digest_algo: 'sha256',
        );

        // the key itself is 32 bytes (i.e 256 bits, because aes *256*)
        $key = mb_substr($derivatedKey, 0, 32, '8bit');
        $iv = mb_substr($derivatedKey, 32, openssl_cipher_iv_length('aes-256-cbc'), '8bit');
        // 16 is the 8 bytes of `Salted__`  and 8 bytes of salt itself
        $cypherText = mb_substr($content, 0, encoding: '8bit');

        $bin_prefix = 'Salted__'; // для совместимости с терминальным вызовом openssl

        @unlink($filepath);
        file_put_contents(
            $filepath, 
            $bin_prefix . $salt . openssl_encrypt($cypherText, 'aes-256-cbc', $key, true, $iv)
        );
    }

    /**
     * Шифрование новых файлов
     * @param $storage
     * @param $zipFilesArray
     * @param $password
     */
    private function encryptFiles($storage, $zipFilesArray, $password) {
        foreach ($zipFilesArray as $file) {

            $filepath = $storage.$file;
            $this->encryptArchive($filepath, $password);
        }
    }

    /**
     * Расшифрока файлов и каталогов
     * 
     * openssl v1.1.1 command:
     * openssl aes-256-cbc -d -salt -pbkdf2 -iter 10000 -in 'путь до входного файла' -out 'путь до выходного файла'
     *
     * @return never
     * @throws Exception
     */
    public function decrypt($file, $password) : bool|string
    {
        $keyBytes = stream_get_contents($file, length: 16);
        // 16 is the 8 bytes of `Salted__`  and 8 bytes of salt itself
        $cypherText = stream_get_contents($file, offset: 16);

        $salt = mb_substr($keyBytes, 8, 8, '8bit');

        $derivatedKey = openssl_pbkdf2(
            $password,
            $salt,
            // key_length is 48 bytes because
            // the key itself is 32 bytes (256 bits, because aes 256)
            // and the IV is 16 bytes (returned by openssl_cipher_iv_length)
            // so 32+16 -> 48
            key_length: 48,
            // 10000 is a of 2021 the amount recommended by the NIST
            // see https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-63b.pdf section 5.1.1.2
            // quote:
            // For PBKDF2, the cost factor is an iteration count: the more times the PBKDF2 function is
            // iterated, the longer it takes to compute the password hash. Therefore, the iteration count
            // SHOULD be as large as verification server performance will allow, typically at least 10,000
            // iterations.
            iterations: 10000,
            digest_algo: 'sha256',
        );

        // the key itself is 32 bytes (i.e 256 bits, because aes *256*)
        $key = mb_substr($derivatedKey, 0, 32, '8bit');
        $iv = mb_substr($derivatedKey, 32, openssl_cipher_iv_length('aes-256-cbc'), '8bit');

        return openssl_decrypt($cypherText, 'aes-256-cbc', $key, true, $iv);
    }

    /**
     * Просмотр директории после распаковки и добавление файлов в базу данных
     *
     * @param Document $parent
     * @param $storage
     */
    private function setZipFiles(Document $parent, $zipFilesArray)
    {
        foreach ($zipFilesArray as $value) {
            $info = pathinfo($value);

            if ($info['extension'] != "zip") {
                Document::updateOrCreate(
                    [
                        'order_id' => $parent->order_id,
                        'filename' => $value,
                        'section' => $parent->section],
                    [
                        'extension' => $info['extension'],
                        'updated_at' => time() // Почему-то не записывает, видимо необходимо изменять данные.
                    ]
                );
            }
        }
    }
}
