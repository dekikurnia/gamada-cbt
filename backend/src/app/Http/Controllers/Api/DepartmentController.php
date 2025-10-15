<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Department::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:departments,code',
        ]);

        $department = Department::create($request->only('name', 'code'));

        return response()->json(['message' => 'Jurusan berhasil dibuat', 'data' => $department], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($department);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
        ]);

        $department->update($request->only('name', 'code'));

        return response()->json(['message' => 'Jurusan berhasil diperbarui', 'data' => $department]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department->delete();
        return response()->json(['message' => 'Jurusan berhasil dihapus']);
    }
}
