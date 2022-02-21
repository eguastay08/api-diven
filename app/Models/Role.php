<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey="cod_rol";

    protected $fillable=[
        "name",
        "detail"
    ];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

    public function users(){
        return $this->hasMany(User::class,'id');
    }

    public function access(){
        return $this->belongsToMany(Access::class,'access_role','cod_rol','cod_access');
    }
}
