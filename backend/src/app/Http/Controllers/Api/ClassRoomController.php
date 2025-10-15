<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ClassRoom::with('department')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
        ]);

        $classRoom = ClassRoom::create($request->only('name', 'department_id'));

        return response()->json(['message' => 'Kelas berhasil dibuat', 'data' => $classRoom], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($classRoom->load('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'department_id' => 'sometimes|required|exists:departments,id',
        ]);

        $classRoom->update($request->only('name', 'department_id'));

        return response()->json(['message' => 'Kelas berhasil diperbarui', 'data' => $classRoom]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $classRoom->delete();
        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}
