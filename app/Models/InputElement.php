<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputElement extends Model
{
    use HasFactory;
    protected $fillable = [
        'header',
        'description',
        'is_mandatory',
        'form_element_id'
    ];
}
