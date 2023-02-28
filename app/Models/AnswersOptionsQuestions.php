<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswersOptionsQuestions extends Model
{
    use HasFactory;

    protected $fillable=[
        'cod_question',
        'cod_option',
        'answer_txt',
        'cod_answer',
        'id_file'
    ];

    /**
     * Returns the group of answers to which the answer to the question belongs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function answer(){
        return $this->belongsTo(Survey::class);
    }
}
