<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $nama_barang = $request->input('nama_barang');
        $tanggal_transaksi = $request->input('tanggal_transaksi');

        $query = Transaksi::query();

        if ($nama_barang) {
            $query->whereHas('barang', function ($q) use ($nama_barang) {
                $q->where('nama_barang', 'like', "%$nama_barang%");
            });
        }

        if ($tanggal_transaksi) {
            $query->whereDate('tanggal_transaksi', $tanggal_transaksi);
        }

        $transaksis = $query->get();

        return response()->json([
            'success' => true,
            'data' => $transaksis,
        ]);
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
        $validatedData = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah_terjual' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
        ]);

        $transaksi = Transaksi::create($validatedData);

        $barang = Barang::findOrFail($validatedData['barang_id']);
        $barang->stok -= $validatedData['jumlah_terjual'];
        $barang->save();

        return response()->json($transaksi, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        return response()->json([
            'status' => true,
            'message' => 'Data Barang',
            'data' => $transaksi
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaksi $transaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $oldQuantity = $transaksi->jumlah_terjual;

        $transaksi->update($request->all());

        // Menghitung selisih jumlah terjual baru dengan jumlah terjual lama
        $diff = $request->jumlah_terjual - $oldQuantity;

        // Memperbarui stok barang berdasarkan selisih jumlah terjual
        $barang = Barang::findOrFail($transaksi->barang_id);
        $barang->stok -= $diff; // Perubahan disesuaikan berdasarkan perbedaan jumlah terjual
        $barang->save();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi updated successfully',
            'data' => $transaksi
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // Mengembalikan jumlah terjual ke stok barang sebelum menghapus transaksi
        $barang = Barang::findOrFail($transaksi->barang_id);
        $barang->stok += $transaksi->jumlah_terjual;
        $barang->save();

        $transaksi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi deleted successfully',
        ]);
    }
}
