<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\DataStatistik;
use Illuminate\Http\Request;

class StatistikController extends Controller
{
    /**
     * Menampilkan daftar data statistik dengan filter tahun dan pencarian.
     */
    public function index(Request $request)
    {
        $tahun = $request->query('tahun', 2024);
        $search = $request->query('search');

        $query = DataStatistik::with('kecamatan')->where('tahun', $tahun);

        if (!empty($search)) {
            $query->whereHas('kecamatan', function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%');
            });
        }

        $statistiks = $query->paginate(15)->withQueryString();
        $daftarTahun = DataStatistik::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('statistik.index', compact('statistiks', 'tahun', 'search', 'daftarTahun'));
    }

    /**
     * Form tambah data statistik baru.
     */
    public function create()
    {
        $kecamatans = Kecamatan::orderBy('nama', 'asc')->get();
        return view('statistik.create', compact('kecamatans'));
    }

    /**
     * Simpan data statistik baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kecamatan_id'     => 'required|exists:kecamatans,id',
            'jumlah_penduduk'  => 'required|integer|min:0',
            'laju_pertumbuhan' => 'required|numeric|between:-100,100',
            'jumlah_desa'      => 'required|integer|min:0',
            'tahun'            => 'required|integer|digits:4',
        ]);

        // Cek duplikasi tahun untuk kecamatan yang sama
        $exists = DataStatistik::where('kecamatan_id', $validated['kecamatan_id'])
            ->where('tahun', $validated['tahun'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'tahun' => 'Data statistik untuk kecamatan ini pada tahun ' . $validated['tahun'] . ' sudah ada.',
            ]);
        }

        DataStatistik::create($validated);

        return redirect()->route('statistik.index')
            ->with('success', 'Data statistik berhasil ditambahkan!');
    }

    /**
     * Form edit data statistik.
     */
    public function edit(DataStatistik $statistik)
    {
        $kecamatans = Kecamatan::orderBy('nama', 'asc')->get();
        return view('statistik.edit', compact('statistik', 'kecamatans'));
    }

    /**
     * Update data statistik.
     */
    public function update(Request $request, DataStatistik $statistik)
    {
        $validated = $request->validate([
            'kecamatan_id'     => 'required|exists:kecamatans,id',
            'jumlah_penduduk'  => 'required|integer|min:0',
            'laju_pertumbuhan' => 'required|numeric|between:-100,100',
            'jumlah_desa'      => 'required|integer|min:0',
            'tahun'            => 'required|integer|digits:4',
        ]);

        $statistik->update($validated);

        return redirect()->route('statistik.index')
            ->with('success', 'Data statistik berhasil diperbarui!');
    }

    /**
     * Hapus data statistik.
     */
    public function destroy(DataStatistik $statistik)
    {
        $statistik->delete();

        return redirect()->route('statistik.index')
            ->with('success', 'Data statistik berhasil dihapus!');
    }
}
