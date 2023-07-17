<?php

namespace App\Services;

use App\Enums\Section;
use App\Models\Document;
use App\Packages\Crypto\File;
use ArrayAccess;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

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
    ): void {
        // Путь к активной директории
        $tmpPath = $this->getTmpDir() . '/';
        $zipArchiveFilename = $document->filename;
        $zipArchivePath = $tmpPath . $zipArchiveFilename;

        // Сохранение документа в базу данных
        $this->dbSaveDoc($document, $section);

        $decoded = base64_decode($file);
        if ($decoded === false) {
            throw new \RuntimeException("Cannot base64_decode $zipArchiveFilename");
        }

        // Сохраненяем архив
        if (file_put_contents($zipArchivePath, $decoded) === false) {
            throw new \RuntimeException("Cannot save base64 decoded file $zipArchiveFilename");
        }

        // Распаковка содержимого из файла ZIP
        $zipFilesArray = $this->unpackZip($zipArchivePath, $archive_password);

        // Сохранение данных о новых файлах
        $this->dbSetZipFiles($document, $zipFilesArray);

        // Создание нового архива (чтобы архив был без пароля)
        unlink($zipArchivePath);
        $this->pack($tmpPath, $zipArchiveFilename, $zipFilesArray);

        // Зашифровка архива и новых файлов
        File::encrypt($zipArchivePath, $user_password, $document->order_id);
        Storage::disk('orders')->put(
            $document->order_id . '/' . $zipArchiveFilename,
            file_get_contents($zipArchivePath)
        );

        foreach ($zipFilesArray as $zipFile) {

            $filepath = $tmpPath . $zipFile;
            File::encrypt($filepath, $user_password, $document->order_id);

            Storage::disk('orders')->put(
                $document->order_id . '/' . $zipFile,
                file_get_contents($filepath)
            );
        }

        $this->deleteTmpDir($tmpPath);
    }

    /**
     * Сохранение полученного из данных файла в базу данных
     *
     * @param Document $document
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
     * Распаковка архива
     *
     * @param string $archivePath
     * @param string $password
     * @return array
     */
    private function unpackZip(string $archivePath, string $password): array
    {
        $zipFileArray = [];
        $info = pathinfo($archivePath);

        if (!isset($info['extension']) || $info['extension'] !== "zip") {
            throw new \RuntimeException('Unknown file extension');
        }

        $zip = new \ZipArchive();
        $zip_status = $zip->open($archivePath);

        if ($zip_status !== true) {
            throw new \RuntimeException("Failed opening archive: " . @$zip->getStatusString() . " (code: " . $zip_status . ")");
        }

        if ($zip->setPassword($password)) {
            if (!$zip->extractTo(dirname($archivePath))) {
                throw new \RuntimeException('Extraction failed (wrong password?)');
            }
        }

        for ($i = 0; $i < $zip->count(); $i++) {
            $zipFileArray[] = $zip->getNameIndex($i);
        }

        $zip->close();

        return $zipFileArray;
    }

    /**
     * Просмотр директории после распаковки и добавление файлов в базу данных
     *
     * @param Document $parent
     * @param $zipFilesArray
     */
    private function dbSetZipFiles(Document $parent, $zipFilesArray): void
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

    /**
     * Создание нового архива
     *
     * @param string $storage
     * @param string $filename
     * @param array $zipFilesArray
     *
     * @return void
     */
    private function pack(string $storage, string $filename, array $zipFilesArray): void
    {
        $zip = new \ZipArchive();
        $zip_status = $zip->open($storage . $filename, \ZipArchive::CREATE);

        if ($zip_status !== true) {
            throw new \RuntimeException("Failed opening archive: " . @$zip->getStatusString() . " (code: " . $zip_status . ")");
        }

        foreach ($zipFilesArray as $file) {
            $info = pathinfo($file);

            if (isset($info['extension']) && $info['extension'] != "zip") {
                if ($zip->addFile($storage . $file, $file) === false) {
                    throw new \RuntimeException("Cannot add $file to zip");
                }
            }
        }

        if (!$zip->close()) {
            throw new \RuntimeException('Failed to close ZIP archive');
        }
    }

    private function getTmpDir(): string
    {
        for ($i = 0; $i < 100; $i++) {
            $tempdir = sys_get_temp_dir() . '/' . uniqid();

            if (is_dir($tempdir)) {
                continue;
            }

            mkdir($tempdir);
            return $tempdir;
        }

        throw new \RuntimeException('Unable to create unique tmp dir');
    }

    private function deleteTmpDir(string $dirPath): void
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }

        if (!str_ends_with($dirPath, '/')) {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {

            if (is_dir($file)) {
                $this->deleteTmpDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    /**
     * @param string $orderID
     * @param ArrayAccess<Document> $documents
     * @param string $password
     *
     * @return string - путь до архива
     */
    public function archiveEncryptedDocuments(string $orderID, ArrayAccess $documents, string $password): string
    {
        $tmpDir = $this->getTmpDir();

        $resultZipPath = $tmpDir . "/" . uniqid() . '.zip';
        touch($resultZipPath);

        $resultZip = new \ZipArchive();
        $status = $resultZip->open($resultZipPath, \ZipArchive::CREATE);
        if ($status !== true) {
            throw new \RuntimeException("Failed opening archive: " . @$resultZip->getStatusString());
        }

        foreach ($documents as $document) {

            $contentEncrypted = Storage::disk('orders')->get($orderID . '/' . $document->filename);
            $filepath = $tmpDir . $document->filename;

            file_put_contents($filepath, $contentEncrypted);
            $decryptedContent = File::decrypt($filepath, $password);
            unlink($filepath);

            $decryptedFilePath = $tmpDir . '/' . uniqid() . "_" . $document->filename;
            $decryptedFileStream = fopen($decryptedFilePath, "a+");
            fwrite($decryptedFileStream, $decryptedContent);
            fclose($decryptedFileStream);

            $resultZip->addFile($decryptedFilePath, basename($decryptedFilePath));
        }

        $resultZip->close();

        File::encrypt($resultZipPath, $password);

        return $resultZipPath;
    }

    public function deleteTmpArchive(string $zipPath): void
    {
        $this->deleteTmpDir(dirname($zipPath));
    }
}
