<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $primaryKey='cod_option';

    protected $fillable=[
        'cod_option',
        'option',
        'image',
        'cod_question'
    ];

    public function question(){
        return $this->belongsTo(Question::class,'cod_question');
    }
}
