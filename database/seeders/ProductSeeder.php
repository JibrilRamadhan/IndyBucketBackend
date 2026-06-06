<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Data dummy produk untuk pengujian tampilan awal.
     * Gambar menggunakan URL dari Supabase atau path publik.
     * Ganti URL gambar dengan URL Supabase Storage Anda yang sebenarnya.
     */
    public function run(): void
    {
        $products = [
            // ─── Koleksi Signature Kami (discover) ────────────────────────
            [
                'name'         => 'Bouquet Buah Premium',
                'description'  => 'Stroberi, anggur, dan nanas pilihan disusun layaknya mahkota bunga — segar, manis, tak terlupakan.',
                'price'        => 185000,
                'stock_status' => 'available',
                'label'        => 'best_seller',
                'home_section' => 'discover',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1587132137056-bfbf0166836e?w=600', 'primary' => true],
                ],
            ],
            [
                'name'         => 'Satin Rose Bouquet',
                'description'  => 'Setiap kelopak dijahit tangan dari satin sutra premium — kenangan yang tak pernah layu.',
                'price'        => 220000,
                'stock_status' => 'available',
                'label'        => 'new',
                'home_section' => 'discover',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1490750967868-88df5691cc5a?w=600', 'primary' => true],
                ],
            ],
            [
                'name'         => 'Blackpink Bouquet',
                'description'  => 'Rangkaian bunga hitam dan pink yang elegan — cocok untuk hadiah spesial fans K-Pop.',
                'price'        => 195000,
                'stock_status' => 'available',
                'label'        => 'new',
                'home_section' => 'discover',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1519378058457-4c29a0a2efac?w=600', 'primary' => true],
                ],
            ],
            [
                'name'         => 'Kupu-Kupu Bouquet',
                'description'  => 'Rangkaian bunga cantik bertema kupu-kupu yang menawan dan penuh keajaiban.',
                'price'        => 175000,
                'stock_status' => 'available',
                'label'        => 'none',
                'home_section' => 'discover',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1468327768560-75b778cbb551?w=600', 'primary' => true],
                ],
            ],

            // ─── Hadiah untuk Setiap Gaya (fresh) ─────────────────────────
            [
                'name'         => 'Money & Bear Bouquet',
                'description'  => 'Buket dari lembaran uang asli & teddy bear pelukan — hadiah yang berbicara dua bahasa.',
                'price'        => 250000,
                'stock_status' => 'available',
                'label'        => 'best_seller',
                'home_section' => 'fresh',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1561181286-d3fee7d55364?w=600', 'primary' => true],
                ],
            ],
            [
                'name'         => 'Snack Gift Bouquet',
                'description'  => 'Perpaduan camilan favorit dalam satu rangkaian cantik — kejutan manis yang bikin senyum.',
                'price'        => 150000,
                'stock_status' => 'available',
                'label'        => 'new',
                'home_section' => 'fresh',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1548848221-0c2e497ed557?w=600', 'primary' => true],
                ],
            ],
            [
                'name'         => 'Perhiasan Bouquet',
                'description'  => 'Cincin dan gelang mewah dirangkai dalam bouquet eksklusif untuk hari istimewa.',
                'price'        => 350000,
                'stock_status' => 'available',
                'label'        => 'none',
                'home_section' => 'fresh',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=600', 'primary' => true],
                ],
            ],

            // ─── Produk Terlaris (bestseller) ──────────────────────────────
            [
                'name'         => 'Peacock Gold Bouquet',
                'description'  => 'Rangkaian bulu merak emas dan bunga biru — mewah dan tak tertandingi.',
                'price'        => 280000,
                'stock_status' => 'available',
                'label'        => 'best_seller',
                'home_section' => 'bestseller',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1478548022029-ca12f37ab7c4?w=600', 'primary' => true],
                ],
            ],
            [
                'name'         => 'Rokok & Uang Bouquet',
                'description'  => 'Buket unik kombinasi rokok dan lembaran uang — pilihan hadiah buat para pria.',
                'price'        => 200000,
                'stock_status' => 'available',
                'label'        => 'best_seller',
                'home_section' => 'bestseller',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1534536281715-e28d76689b4d?w=600', 'primary' => true],
                ],
            ],
            [
                'name'         => 'Bulan Unik Bouquet',
                'description'  => 'Rangkaian buket berbentuk bulan — hadiah yang romantis dan tak terlupakan.',
                'price'        => 165000,
                'stock_status' => 'available',
                'label'        => 'best_seller',
                'home_section' => 'bestseller',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?w=600', 'primary' => true],
                ],
            ],
            [
                'name'         => 'Pita Satin Bouquet',
                'description'  => 'Buket pita satin premium dengan desain elegan yang cocok untuk wisuda dan pernikahan.',
                'price'        => 240000,
                'stock_status' => 'available',
                'label'        => 'best_seller',
                'home_section' => 'bestseller',
                'images'       => [
                    ['url' => 'https://images.unsplash.com/photo-1526047932273-341f2a7631f9?w=600', 'primary' => true],
                ],
            ],
        ];

        foreach ($products as $data) {
            $images = $data['images'];
            unset($data['images']);

            $product = Product::create($data);

            foreach ($images as $i => $img) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $img['url'],
                    'is_primary' => $img['primary'] ?? ($i === 0),
                    'sort_order' => $i,
                ]);
            }
        }

        $this->command->info('✓ ' . count($products) . ' dummy products seeded!');
    }
}
