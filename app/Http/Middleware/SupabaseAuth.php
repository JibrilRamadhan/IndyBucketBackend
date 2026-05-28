<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class SupabaseAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized. Token not provided.'], 401);
        }

        try {
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseKey = env('SUPABASE_KEY');
            
            if (!$supabaseUrl || !$supabaseKey) {
                throw new Exception('Supabase URL or Key is not configured in .env');
            }

            $requestHttp = \Illuminate\Support\Facades\Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $token,
            ]);

            // Bypass SSL HANYA untuk di localhost (development). Di server asli, SSL wajib aktif!
            if (app()->environment('local')) {
                $requestHttp = $requestHttp->withoutVerifying();
            }

            $response = $requestHttp->get("{$supabaseUrl}/auth/v1/user");

            if ($response->failed()) {
                throw new Exception('Token dari frontend tidak valid.');
            }

            $user = $response->json();
            
            // Simpan data user ke dalam request
            $request->attributes->set('supabase_user', (object) $user);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unauthorized. Invalid token.',
                'error' => $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}
