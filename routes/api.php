<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// ─── Endpoint Publik ─────────────────────────────────────────────
Route::get('/public-data', function () {
    return response()->json(['message' => 'Ini data publik']);
});

// Produk - Publik (untuk halaman frontend)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// ─── Endpoint Admin (dilindungi Supabase Auth) ──────────────────
Route::middleware('supabase.auth')->group(function () {
    Route::get('/admin/dashboard', function (Request $request) {
        $user = $request->attributes->get('supabase_user');
        
        return response()->json([
            'message' => 'Selamat datang di Admin Dashboard!',
            'user' => $user
        ]);
    });

    // CRUD Produk
    Route::post('/admin/products', [ProductController::class, 'store']);
    Route::put('/admin/products/{product}', [ProductController::class, 'update']);
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']);

    // Manajemen Gambar Produk
    Route::post('/admin/products/{product}/images', [ProductController::class, 'storeImage']);
    Route::delete('/admin/product-images/{productImage}', [ProductController::class, 'destroyImage']);
    Route::put('/admin/products/{product}/images/{productImage}/primary', [ProductController::class, 'setPrimaryImage']);
});

