<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'ip_address',
        'uuid',
        'os_version',
        'device_model',
        'app_version',
        'device_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}