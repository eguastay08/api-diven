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
        'cod_survey'
    ];

    public function survey(){
        return $this->belongsTo(Survey::class,'cod_survey');
    }

    public function options(){
        return $this->hasMany(Option::class,'cod_question');
    }
}
