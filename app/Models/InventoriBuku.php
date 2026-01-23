<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoriBuku extends Model
{
    protected $fillable = ['title', 'author', 'isbn', 'quantity', 'description', 'publisher', 'year', 'edition', 'language', 'pages', 'category', 'location'];
}
