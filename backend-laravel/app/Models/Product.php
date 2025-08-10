<?php

// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
        protected $fillable = ['title','publisher_id','stock','price']; // <- quita 'author' y 'publisher' (string)


    public function authors() {
        return $this->belongsToMany(Author::class);
    }

    public function genres() {
        return $this->belongsToMany(Genre::class);
    }

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function mainImage() {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }
    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id'); 
    }
    // app/Models/Product.php
public function supplierCost()  // o supplierPrice
{
    return $this->hasOne(ProductSupplier::class);
}



}
