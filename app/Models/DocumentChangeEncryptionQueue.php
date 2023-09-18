<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentChangeEncryptionQueue extends Model
{
    use HasFactory;

    protected $table = "document_change_encryption_queue";

    protected $primaryKey = "id";

    protected $fillable = ['document_id', 'old_profile_password', 'profile_id'];
}
