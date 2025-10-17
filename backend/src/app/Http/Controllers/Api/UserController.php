<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Mengambil data user yang sedang login.
     * Termasuk relasi kelas & jurusan untuk siswa.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load([
            'classroom.department' // load kelas dan jurusan
        ]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'classroom' => $user->classroom ? [
                'id' => $user->classroom->id,
                'name' => $user->classroom->name,
                'department' => $user->classroom->department ? [
                    'id' => $user->classroom->department->id,
                    'name' => $user->classroom->department->name,
                    'code' => $user->classroom->department->code,
                ] : null,
            ] : null,
        ]);
    }
}
