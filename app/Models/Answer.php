<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable=[
        'cod_answer',
        'cod_question',
        'cod_option',
        'answer_txt',
        'latitude',
        'length',
        'id_user'
    ];
}
