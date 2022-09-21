<?php

namespace App\Services;

use App\Enums\Section;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentServices
{
    public function __construct(Document $document, string $file, Section $section)
    {
        $document->section = $section->getSection();

        $fileinfo = pathinfo($document->filename);
        $document->extension = $fileinfo['extension'];
        $document->filename =  $fileinfo['filename'].".".$fileinfo['extension'];

        $document->save();

        Storage::disk('invoice')->put('/{$document->order_id}/'.$document->filename, base64_decode($file));
    }
}
