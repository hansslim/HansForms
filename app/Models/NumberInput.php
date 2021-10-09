<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_element_id',
        'min',
        'max',
        'can_be_decimal',
    ];

    public function inputElement() {
        return $this->belongsTo(InputElement::class);
    }
}
