<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $primaryKey='cod_question';

    protected $fillable=[
        'name',
        'question',
        'required',
        'image',
        'type',
        'order',
        'cod_section'
    ];

    public function section(){
        return $this->belongsTo(Section::class,'cod_section');
    }

    public function options(){
        return $this->hasMany(Option::class,'cod_question');
    }
}
