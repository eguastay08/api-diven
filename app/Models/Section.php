<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $primaryKey='cod_section';

    protected $fillable=[
        'name',
        'order',
        'detail',
        'cod_survey'
    ];

    public function question(){
        return $this->belongsTo(Project::class,'cod_section');
    }
}
