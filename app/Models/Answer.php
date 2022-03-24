<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $primaryKey='cod_answer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable=[
        'latitude',
        'longitude',
        'id_user',
        'cod_survey'
    ];

    /**
     *Returns the poll to which the answer belongs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function survey(){
        return $this->belongsTo(Survey::class,'cod_answer');
    }

    /**
     *Returns the answers to the questions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function responses(){
        return $this->hasMany(AnswersOptionsQuestions::class,'cod_answer');
    }

}
