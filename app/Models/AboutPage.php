<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'title_en',
        'content_en',
    ];
}
