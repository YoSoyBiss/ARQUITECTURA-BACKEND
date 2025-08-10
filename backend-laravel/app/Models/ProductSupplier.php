<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSupplier extends Model
{
    protected $table = 'product_supplier';

    protected $fillable = ['product_id', 'precio_proveedor'];

    protected $casts = [
        'precio_proveedor' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
