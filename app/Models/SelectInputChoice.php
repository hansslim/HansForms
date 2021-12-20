<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectInputChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'description',
        'hidden_label',
        'order',
        'select_input_id',
    ];

    protected $visible = [
        'id',
        'text',
        'description',
        'hidden_label',
        'order',
        'select_input_id',
        'select_input_choice_id',
        'selectInputAnswers'
    ];

    public function selectInput() {
        return $this->belongsTo(SelectInput::class);
    }

    public function selectInputAnswers() {
        return $this->hasMany(SelectInputAnswer::class, 'select_choice_id');
    }
}
