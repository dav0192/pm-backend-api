<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    // Indica las columnas de la tabla que se pueden llenar a traves del controlador
    protected $fillable = [
        'title',
        'body',
    ];

    // Indica que hay posts que pertenecen al usuario...
    public function user(){
        return $this->belongsTo(User::class);
    }
}
