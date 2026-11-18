<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shuttle;
use Illuminate\Http\Request;

class ShuttleApiController extends Controller
{
    /**
     * Menampilkan seluruh data shuttle (Get All)
     * sudah tidak perlu diubah karena sudah sesuai dengan standar RESTful API
     */
    public function ok()
    {
        $data = Shuttle::latest()->get();
        
        return response()->json([
            'message' => 'Berhasil mengambil semua data shuttle',
            'data' => $data
        ], 200);
    }

    /**
     * Menampilkan data shuttle spesifik berdasarkan ID (Get By ID)
     */
    public function show($id)
    {
        $data = Shuttle::find($id);

        // Validasi jika data tidak ditemukan (Status 404)
        if (!$data) {
            return response()->json([
                'message' => 'Data shuttle tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Detail data shuttle ditemukan',
            'data' => $data
        ], 200);
    }

    /**
     * Menambahkan data shuttle baru (Create)
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama_armada'      => 'required|string|max:255',
            'kapasitas'        => 'required|numeric|min:10|max:60',
            'rute_operasional' => 'required|string',
        ]);

        $data = Shuttle::create($validatedData);

        return response()->json([
            'message' => 'Berhasil menambahkan data shuttle baru',
            'data' => $data
        ], 201);
    }

    /**
     * Memperbarui data shuttle berdasarkan ID (Update)
     */
    public function update(Request $request, $id)
    {
        $data = Shuttle::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data shuttle tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Validasi input (menggunakan 'sometimes' agar fleksibel untuk update parsial/PATCH)
        $validatedData = $request->validate([
            'nama_armada'      => 'sometimes|required|string|max:255',
            'kapasitas'        => 'sometimes|required|numeric|min:10|max:60',
            'rute_operasional' => 'sometimes|required|string',
        ]);

        $data->update($validatedData);

        return response()->json([
            'message' => 'Berhasil memperbarui data shuttle',
            'data' => $data
        ], 200);
    }

    /**
     * Menghapus data shuttle berdasarkan ID (Delete)
     */
    public function destroy($id)
    {
        $data = Shuttle::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data shuttle tidak ditemukan',
                'data' => null
            ], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data shuttle',
            'data' => null
        ], 200);
    }
}
