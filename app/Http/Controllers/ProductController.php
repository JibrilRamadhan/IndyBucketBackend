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
     * Query param: ?section=discover|fresh|bestseller
     */
    public function index(Request $request)
    {
        $query = Product::with('images')->orderBy('created_at', 'desc');

        // Filter berdasarkan home_section jika ada query param
        if ($request->has('section') && in_array($request->section, ['discover', 'fresh', 'bestseller'])) {
            $query->where('home_section', $request->section);
        }

        $products = $query->get();

        return response()->json([
            'products' => $products->map(fn($p) => $this->formatProduct($p)),
        ]);
    }

    /**
     * Detail satu produk (Publik).
     */
    public function show(Product $product)
    {
        $product->load('images');
        return response()->json(['product' => $this->formatProduct($product)]);
    }

    /**
     * Tambah produk baru (Admin only).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock_status' => 'required|in:available,out_of_stock',
            'label'        => 'required|in:none,best_seller,new',
            'home_section' => 'nullable|in:discover,fresh,bestseller',
            'image_urls'   => 'nullable|array|max:10',
            'image_urls.*' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'name'         => $request->name,
            'description'  => $request->description,
            'price'        => $request->price,
            'stock_status' => $request->stock_status,
            'label'        => $request->label,
            'home_section' => $request->home_section ?: null,
        ]);

        if ($request->has('image_urls')) {
            foreach ($request->input('image_urls') as $index => $url) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $url,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        $product->load('images');

        return response()->json([
            'message' => 'Produk berhasil ditambahkan!',
            'product' => $this->formatProduct($product),
        ], 201);
    }

    /**
     * Update data produk (Admin only).
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock_status' => 'required|in:available,out_of_stock',
            'label'        => 'required|in:none,best_seller,new',
            'home_section' => 'nullable|in:discover,fresh,bestseller',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update([
            'name'         => $request->name,
            'description'  => $request->description,
            'price'        => $request->price,
            'stock_status' => $request->stock_status,
            'label'        => $request->label,
            'home_section' => $request->home_section ?: null,
        ]);

        $product->load('images');

        return response()->json([
            'message' => 'Produk berhasil diperbarui!',
            'product' => $this->formatProduct($product),
        ]);
    }

    /**
     * Hapus produk beserta semua gambarnya (Admin only).
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Produk berhasil dihapus!']);
    }

    /**
     * Tambah gambar ke produk (Admin only).
     */
    public function storeImage(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'image_urls'   => 'required|array|max:10',
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
                'id'         => $productImage->id,
                'url'        => $productImage->image_path,
                'is_primary' => $productImage->is_primary,
                'sort_order' => $productImage->sort_order,
            ];
        }

        return response()->json([
            'message' => 'Gambar berhasil ditambahkan!',
            'images'  => $savedImages,
        ], 201);
    }

    /**
     * Hapus satu gambar produk (Admin only).
     */
    public function destroyImage(ProductImage $productImage)
    {
        $wasPrimary = $productImage->is_primary;
        $productId  = $productImage->product_id;

        $productImage->delete();

        if ($wasPrimary) {
            $nextImage = ProductImage::where('product_id', $productId)
                ->orderBy('sort_order')
                ->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json(['message' => 'Gambar berhasil dihapus!']);
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

        return response()->json(['message' => 'Gambar utama berhasil diubah!']);
    }

    // ─── Helper ────────────────────────────────────────────────────────────────

    private function formatProduct(Product $product): array
    {
        return [
            'id'           => $product->id,
            'name'         => $product->name,
            'description'  => $product->description,
            'price'        => $product->price,
            'stock_status' => $product->stock_status,
            'label'        => $product->label,
            'home_section' => $product->home_section,
            'images'       => $product->images->map(fn($img) => [
                'id'         => $img->id,
                'url'        => $img->image_path,
                'is_primary' => $img->is_primary,
                'sort_order' => $img->sort_order,
            ]),
            'created_at'   => $product->created_at,
            'updated_at'   => $product->updated_at,
        ];
    }
}
