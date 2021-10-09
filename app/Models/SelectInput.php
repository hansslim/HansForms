<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_element_id',
        'is_multiselect',
        'is_dropdown',
        'strict_amount_of_answers',
    ];

    public function inputElement() {
        return $this->belongsTo(InputElement::class);
    }

    public function  selectInputChoices() {
        return $this->hasMany(SelectInputChoice::class);
    }
}
