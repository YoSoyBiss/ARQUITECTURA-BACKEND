<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = ['title', 'author', 'publisher', 'stock'];

        public function suppliers()
    {
        // Since suppliers are in Mongo, this returns the pivot info only
        return $this->belongsToMany(
            Supplier::class,         // assuming you create a Supplier model (optional)
            'product_supplier',      // pivot table name
            'product_id',            // foreign key on pivot table for this model
            'supplier_mongo_id',     // foreign key on pivot table for the other model (stored as string)
            'id',                   // local key on this model
            '_id'                   // local key on Supplier model (Mongo ObjectId)
        );

}
}