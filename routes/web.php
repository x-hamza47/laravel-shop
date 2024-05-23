<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandsController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function(){

    // ---- Guest Routes
    Route::middleware('admin.guest')->group(function(){

        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');

    });

    // ---- Admin Routes
    Route::middleware('admin.auth')->group(function(){
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');

            #------ Category Routes
        Route::controller(CategoryController::class)->group(function(){
            Route::prefix('categories')->group(function(){

                Route::get('/', 'index')->name('categories.index');
                Route::get('/create', 'create')->name('categories.create');
                Route::post('/', 'store')->name('categories.store');
                Route::get('/{category}/edit', 'edit')->name('categories.edit');
                Route::put('/{category}', 'update')->name('categories.update');
                Route::delete('/{category}', 'destroy')->name('categories.delete');

            });
        });

        //--------- Sub Category Routes
        Route::controller(SubCategoryController::class)->group(function(){
            Route::prefix('sub-categories')->group(function(){

                Route::get('/', 'index')->name('sub-categories.index');
                Route::get('/create', 'create')->name('sub-categories.create');
                Route::post('/', 'store')->name('sub-categories.store');
                Route::get('/{subCategory}/edit', 'edit')->name('sub-categories.edit');
                Route::put('/{subCategory}', 'update')->name('sub-categories.update');
                Route::delete('/{subCategory}', 'destroy')->name('sub-categories.delete');

            });

        });

        //---------- Brands Routes
        Route::controller(BrandsController::class)->group(function(){
            Route::prefix('brands')->group(function(){

                Route::get('/', 'index')->name('brands.index');
                Route::get('/create', 'create')->name('brands.create');
                Route::post('/', 'store')->name('brands.store');
                Route::get('/{brand}/edit', 'edit')->name('brands.edit');
                Route::put('/{brand}', 'update')->name('brands.update');
                Route::delete('/{brands}', 'destroy')->name('brands.delete');

            });
        });

        //---------- Product Routes
        Route::controller(ProductController::class)->group(function(){
            Route::prefix('products')->group(function(){

                Route::get('/', 'index')->name('products.index');
                Route::get('/create', 'create')->name('products.create');
                Route::get('/pro-sub', 'productSubIndex')->name('product-sub-category.index');
                Route::post('/', 'store')->name('products.store');
                Route::get('/{product}/edit', 'edit')->name('products.edit');
                Route::put('/{product}', 'update')->name('products.update');
                Route::delete('/{product}', 'destroy')->name('products.delete');

            });
        });

             // product-images-route
        Route::post('/product-images/update', [ProductImageController::class, 'update'])->name('product-images.update');
        Route::delete('/product-images', [ProductImageController::class, 'destroy'])->name('product-images.destroy');
        
        
        // temp-images-route
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');

        Route::get('/getSLug', function(Request $request){
            $slug = '';
            if (!empty($request->title)) {
               $slug = Str::slug($request->title);
            }

            return response()->json([
                'status'=> true,
                'slug' => $slug
            ]);

        })->name('getSlug');

    });

});

