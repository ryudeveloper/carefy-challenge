<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internment extends Model
{
    use HasFactory;

    protected $fillable = [
        'var_patient_id',
        'var_guia',
        'var_entrada',
        'var_saida'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'var_patient_id');
    }
}

