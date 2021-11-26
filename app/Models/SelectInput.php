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
        'strict_amount_of_answers',
        'min_amount_of_answers',
        'max_amount_of_answers',
        'has_hidden_label'
    ];

    protected $with = [
      'selectInputChoices'
    ];

    public function inputElement() {
        return $this->belongsTo(InputElement::class);
    }

    public function  selectInputChoices() {
        return $this->hasMany(SelectInputChoice::class);
    }
}
