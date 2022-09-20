<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{

    public function __construct(Document $document, string $file)
    {
        $document->filename = str_replace('nbsp', '', $document->filename);

        $exp = explode('.', $document->filename);
        $exp = array_values(
            array_diff($exp, [' ', ''])
        );
        $document->extension = $exp[1];

        $exp = explode('№', $exp[0]);
        $document->section = trim($exp[0]);
        $document->save();

        Storage::disk('documents')->put('/'.$document->filename, base64_decode($file));
    }
}
