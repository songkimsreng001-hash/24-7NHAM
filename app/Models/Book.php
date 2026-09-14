<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    protected $primaryKey = 'BookID';

    public $timestamps = true;

    protected $fillable = [
        'Title',
        'Author',
        'ISBN',
        'PublishYear',
    ];
}
