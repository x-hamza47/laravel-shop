<?php

use App\Models\Category;

function getCategories() {
   return Category::orderBy('name','ASC')
        ->with('sub_category')
        ->withCount('products')
        ->orderBy('id','DESC')
        ->where('status','1')
        ->where('show','Yes')
        ->get();
}

?>