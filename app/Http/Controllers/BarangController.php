<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->input('q');

        $barang = Barang::where('nama_barang', 'like', "%$data%")->get();

        return response()->json([
            'status' => true,
            'message' => 'List Data Barang',
            'data' => $barang
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_barang' => 'required',
            'jenis_barang' => 'required',
            'stok' => 'required',
        ]);

        try {
            $barang = Barang::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Menambahkan Data Barang',
                'data' => $barang
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal Menambahkan Data Barang'
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        return response()->json([
            'status' => true,
            'message' => 'Data Barang',
            'data' => $barang
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'nama_barang' => 'string',
            'jenis_barang' => 'string',
            'stok' => 'string',
        ]);

        try {
            $barang->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Update Data Barang',
                'data' => $barang
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal Update Data Barang'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        $barang->delete();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Hapus Data Barang'
        ]);
    }
}
