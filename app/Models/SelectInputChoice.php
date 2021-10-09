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

    public function selectInput() {
        return $this->belongsTo(SelectInput::class);
    }
}
