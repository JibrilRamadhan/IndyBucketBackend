<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Daftar semua produk beserta gambarnya (Publik).
     */
    public function index()
    {
        $products = Product::with('images')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'stock_status' => $product->stock_status,
                    'label' => $product->label,
                    'images' => $product->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->image_path, // Sekarang langsung berupa URL dari Supabase
                            'is_primary' => $image->is_primary,
                            'sort_order' => $image->sort_order,
                        ];
                    }),
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            }),
        ]);
    }

    /**
     * Detail satu produk (Publik).
     */
    public function show(Product $product)
    {
        $product->load('images');

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock_status' => $product->stock_status,
                'label' => $product->label,
                'images' => $product->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->image_path,
                        'is_primary' => $image->is_primary,
                        'sort_order' => $image->sort_order,
                    ];
                }),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ],
        ]);
    }

    /**
     * Tambah produk baru (Admin only).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_status' => 'required|in:available,out_of_stock',
            'label' => 'required|in:none,best_seller,new',
            'image_urls' => 'nullable|array|max:10',
            'image_urls.*' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock_status' => $request->stock_status,
            'label' => $request->label,
        ]);

        // Simpan URL gambar jika ada
        if ($request->has('image_urls')) {
            foreach ($request->input('image_urls') as $index => $url) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $url,
                    'is_primary' => $index === 0, // Gambar pertama jadi primary
                    'sort_order' => $index,
                ]);
            }
        }

        $product->load('images');

        return response()->json([
            'message' => 'Produk berhasil ditambahkan!',
            'product' => $product,
        ], 201);
    }

    /**
     * Update data produk (Admin only).
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_status' => 'required|in:available,out_of_stock',
            'label' => 'required|in:none,best_seller,new',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock_status' => $request->stock_status,
            'label' => $request->label,
        ]);

        $product->load('images');

        return response()->json([
            'message' => 'Produk berhasil diperbarui!',
            'product' => $product,
        ]);
    }

    /**
     * Hapus produk beserta semua gambarnya (Admin only).
     */
    public function destroy(Product $product)
    {
        // Catatan: Penghapusan file fisik dari Supabase Storage 
        // akan diurus oleh frontend sebelum memanggil endpoint ini
        // Backend hanya menghapus record dari database.

        $product->delete(); // Cascade akan menghapus record product_images juga

        return response()->json([
            'message' => 'Produk berhasil dihapus!',
        ]);
    }

    /**
     * Tambah gambar ke produk (Admin only).
     */
    public function storeImage(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'image_urls' => 'required|array|max:10',
            'image_urls.*' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $currentMaxOrder = $product->images()->max('sort_order') ?? -1;
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $savedImages = [];

        foreach ($request->input('image_urls') as $index => $url) {
            $productImage = ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $url,
                'is_primary' => !$hasPrimary && $index === 0,
                'sort_order' => $currentMaxOrder + $index + 1,
            ]);

            $savedImages[] = [
                'id' => $productImage->id,
                'url' => $productImage->image_path,
                'is_primary' => $productImage->is_primary,
                'sort_order' => $productImage->sort_order,
            ];
        }

        return response()->json([
            'message' => 'Gambar berhasil ditambahkan!',
            'images' => $savedImages,
        ], 201);
    }

    /**
     * Hapus satu gambar produk (Admin only).
     */
    public function destroyImage(ProductImage $productImage)
    {
        $wasPrimary = $productImage->is_primary;
        $productId = $productImage->product_id;

        $productImage->delete();

        // Jika gambar yang dihapus adalah primary, jadikan gambar pertama sbg primary baru
        if ($wasPrimary) {
            $nextImage = ProductImage::where('product_id', $productId)
                ->orderBy('sort_order')
                ->first();

            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'message' => 'Gambar berhasil dihapus!',
        ]);
    }

    /**
     * Set gambar sebagai primary (Admin only).
     */
    public function setPrimaryImage(Product $product, ProductImage $productImage)
    {
        if ($productImage->product_id !== $product->id) {
            return response()->json(['message' => 'Gambar bukan milik produk ini.'], 403);
        }

        $product->images()->update(['is_primary' => false]);
        $productImage->update(['is_primary' => true]);

        return response()->json([
            'message' => 'Gambar utama berhasil diubah!',
        ]);
    }
}
