<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $primaryKey='id_file';
    protected $fillable=[
        'name',
        'extension',
        'type',
        'id_user',
        'path'
    ];
    protected $hidden=[
        'created_at',
        'updated_at',
        'path'
    ];
}
