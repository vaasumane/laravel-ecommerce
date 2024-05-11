<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Storage;


Route::get('/', [ProductsController::class, 'getAllProducts'])->name('home');
Route::get('/add-products', [ProductsController::class, 'addProducts']);
Route::post('/save-products', [ProductsController::class, 'SaveProducts']);
Route::get('/get-products', [ProductsController::class, 'getProduct']);
Route::post('/add-to-cart', [CartController::class, 'addTocart'])->name('cart');
Route::get('/get-cart-count', [CartController::class, 'getCartCount'])->name('cart');
Route::get('/cart', [CartController::class, 'getCart']);
Route::get('/get-cart-items', [CartController::class, 'getCartItems']);
Route::post('/update-cart', [CartController::class, 'Updatecart']);
Route::post('/delete-cart', [CartController::class, 'deletecart']);
