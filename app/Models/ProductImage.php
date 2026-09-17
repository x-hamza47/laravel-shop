<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $table = 'product_images';

    public function getImageUrlAttribute() {
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
        }

        return asset('uploads/products/small/' . $this->image);
    }
}
