<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'var_nome',
        'var_nascimento',
        'var_codigo'
    ];

    public function internments()
    {
        return $this->hasMany(Internment::class, 'var_patient_id');
    }
}

