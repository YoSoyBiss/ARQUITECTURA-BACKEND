<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSupplier extends Model
{
    protected $table = 'product_supplier';

    protected $fillable = [
        'product_id',
        'supplier_mongo_id',
        'purchase_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
