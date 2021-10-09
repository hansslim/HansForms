<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id'
    ];

    public function form() {
        return $this->belongsTo(Form::class);
    }

    public function booleanInputAnswers() {
        return $this->hasMany(BooleanInputAnswer::class);
    }

    public function dateInputAnswers() {
        return $this->hasMany(DateInputAnswer::class);
    }

    public function textInputAnswers() {
        return $this->hasMany(TextInputAnswer::class);
    }

    public function numberInputAnswers() {
        return $this->hasMany(NumberInputAnswer::class);
    }

    public function selectInputAnswers() {
        return $this->hasMany(SelectInputAnswer::class);
    }
}
