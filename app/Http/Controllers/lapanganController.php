<?php

namespace App\Http\Controllers;

use App\Models\LapanganModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LapanganController extends Controller
{
    public function tambah(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'ukuran' => 'required',
            'harga' => 'required',
            'status' => 'required',
            'tipe' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $fileName = null;

        // Handle upload gambar
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('gambar_lapangan'), $fileName);
        }

        $lapangan = LapanganModel::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'ukuran' => $request->ukuran,
            'harga_per_jam' => $request->harga,
            'status' => $request->status,
            'tipe' => $request->tipe,
            'gambar' => $fileName ? 'gambar_lapangan/' . $fileName : null
        ]);

        if ($lapangan) {
            return redirect()->route('lapangan')->with('berhasil_tambah', true);
        } else {
            return redirect()->route('lapangan')->with('gagal_tambah', true);
        }
    }

    public function hapus(Request $request, $id)
    {
        $lapangan = LapanganModel::findOrFail($id);

        // Hapus gambar jika ada
        if ($lapangan->gambar && file_exists(public_path($lapangan->gambar))) {
            unlink(public_path($lapangan->gambar));
        }

        $lapangan->delete();

        return redirect()->route('lapangan')->with('berhasil_hapus', true);
    }

    public function edit(Request $request, $id)
    {
        // Validasi input edit
        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'ukuran' => 'required',
            'harga' => 'required',
            'status' => 'required',
            'tipe' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $lapangan = LapanganModel::findOrFail($id);

        $lapangan->nama = $request->nama;
        $lapangan->deskripsi = $request->deskripsi;
        $lapangan->ukuran = $request->ukuran;
        $lapangan->harga_per_jam = $request->harga;
        $lapangan->status = $request->status;
        $lapangan->tipe = $request->tipe;

        // Update gambar jika ada upload baru
        if ($request->hasFile('gambar')) {
            if ($lapangan->gambar && file_exists(public_path($lapangan->gambar))) {
                unlink(public_path($lapangan->gambar));
            }

            $file = $request->file('gambar');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('gambar_lapangan'), $fileName);

            $lapangan->gambar = 'gambar_lapangan/' . $fileName;
        }

        $lapangan->save();

        return redirect()->route('lapangan')->with('berhasil_edit', true);
    }
}
