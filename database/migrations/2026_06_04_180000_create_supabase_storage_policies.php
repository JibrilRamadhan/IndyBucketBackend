<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 2. Create policy for Select (Read) - allow public select (anyone can read)
        DB::statement("
            CREATE POLICY \"Allow public select access to products\"
            ON storage.objects FOR SELECT
            USING (bucket_id = 'products');
        ");

        // 3. Create policy for Insert (Upload) - allow authenticated users
        DB::statement("
            CREATE POLICY \"Allow auth insert access to products\"
            ON storage.objects FOR INSERT
            TO authenticated
            WITH CHECK (bucket_id = 'products');
        ");

        // 4. Create policy for Update (Replace) - allow authenticated users
        DB::statement("
            CREATE POLICY \"Allow auth update access to products\"
            ON storage.objects FOR UPDATE
            TO authenticated
            USING (bucket_id = 'products')
            WITH CHECK (bucket_id = 'products');
        ");

        // 5. Create policy for Delete (Remove) - allow authenticated users
        DB::statement("
            CREATE POLICY \"Allow auth delete access to products\"
            ON storage.objects FOR DELETE
            TO authenticated
            USING (bucket_id = 'products');
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP POLICY IF EXISTS "Allow public select access to products" ON storage.objects;');
        DB::statement('DROP POLICY IF EXISTS "Allow auth insert access to products" ON storage.objects;');
        DB::statement('DROP POLICY IF EXISTS "Allow auth update access to products" ON storage.objects;');
        DB::statement('DROP POLICY IF EXISTS "Allow auth delete access to products" ON storage.objects;');
    }
};
