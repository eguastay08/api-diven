<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $primaryKey ='cod_survey';

    protected $fillable=[
        'cod_survey',
        'name',
        'date_init',
        'date_finally',
        'max_answers',
        'status',
        'detail',
        'cod_project'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_init' => 'datetime',
        'date_finally' => 'datetime',
    ];

    /**
     * Returns the project to which the survey belongs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(){
        return $this->belongsTo(Project::class,'cod_project');
    }

    /**
     *Returns the sections of a survey
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections(){
        return $this->hasMany(Section::class,'cod_survey')->orderBy('order');
    }

    /**
     * Returns survey answers
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers(){
        return $this->hasMany(Answer::class,'cod_survey');
    }

    public function totAnswers(){
        return $this->answers()->count();
    }
}
