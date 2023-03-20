<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Model;

class ProfileInternal extends Model
{
    use MapTrait;

    protected $fillable = ['profile_id', 'internal_id', 'internal_code'];

    protected $table = "profile_internal";

    protected $primaryKey = "internal_id";

    public $incrementing = false;

    public $timestamps = true;

    protected $connection = "mysql";

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }
}
