<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey='cod_project';

    protected $fillable=[
        'name',
        'resolution',
        'detail',
        'image',
        'cod_dpa'
    ];

    protected $hidden=[
        'create_at',
        'update_at'
    ];

    public function dpa(){
        return $this->belongsTo(Dpa::class,'cod_dpa');
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function surveys(){
        return $this->hasMany(Survey::class,'cod_project')->selectRaw('surveys.*,count(answers.cod_survey) as tot')
            ->leftjoin('answers','answers.cod_survey','surveys.cod_survey')
            ->groupBy('answers.cod_survey');
    }
}
