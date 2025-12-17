<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        // Validasi
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8'
        ]);

        // Ambil user
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "User tidak ditemukan!"
            ], 404);
        }

        // Update data
        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            "success" => true,
            "message" => "User berhasil diupdate!",
            "data" => $user
        ], 200);
    }

    public function destroy($id)
    {
        // Cari user berdasarkan ID
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "User tidak ditemukan!"
            ], 404);
        }

        // Delete user
        $user->delete();

        return response()->json([
            "success" => true,
            "message" => "User berhasil dihapus!"
        ], 200);
    }

}