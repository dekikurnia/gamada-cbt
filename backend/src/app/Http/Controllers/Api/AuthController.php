<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // bisa email atau username
            'password' => 'required|string',
        ]);
        
        // Cek apakah input adalah email atau username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        // Attempt login dengan kolom yang sesuai
        if (!Auth::attempt([$loginField => $request->login, 'password' => $request->password])) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }
        
        $user = $request->user();
        
        // Cegah login jika siswa masih punya sesi ujian aktif
        if ($user->examStatuses()->where('is_active', true)->exists()) {
            return response()->json([
                'message' => 'Kamu masih aktif ujian. Hubungi pengawas untuk reset akun.'
            ], 403);
        }
        
        // 🔐 Hapus token lama biar tidak dobel login
        $user->tokens()->delete();
        
        // 🔑 Buat token baru
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
        ]);
    }
    
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }
            
            return response()->json([
                'message' => 'Logout berhasil. Token telah dihapus.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat logout.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    
}
