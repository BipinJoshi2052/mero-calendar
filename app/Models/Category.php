<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['title','type','updated_at'];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
