<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\DataStatistik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KecamatanController extends Controller
{
    /**
     * Menampilkan daftar kecamatan beserta data statistik terbaru.
     * Mendukung fitur Search (Pencarian) dan Sort (Pengurutan).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'nama');
        $sortOrder = $request->query('sort_order', 'asc');

        // Query kecamatan dengan relasi data statistik terbaru
        $query = Kecamatan::with('statistik');

        // Fitur Find / Search (Berdasarkan nama kecamatan)
        if (!empty($search)) {
            $query->where('nama', 'LIKE', '%' . $search . '%');
        }

        // Fitur Sort (Pengurutan kolom)
        $allowedSorts = ['nama', 'latitude', 'longitude', 'jumlah_penduduk', 'laju_pertumbuhan', 'jumlah_desa'];
        if (in_array($sortBy, ['jumlah_penduduk', 'laju_pertumbuhan', 'jumlah_desa'])) {
            // Sort berdasarkan relasi data statistik
            $query->join('data_statistiks', 'kecamatans.id', '=', 'data_statistiks.kecamatan_id')
                  ->orderBy('data_statistiks.' . $sortBy, $sortOrder)
                  ->select('kecamatans.*');
        } elseif (in_array($sortBy, ['nama', 'latitude', 'longitude'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('nama', 'asc');
        }

        $kecamatans = $query->paginate(15)->withQueryString();

        return view('kecamatan.index', compact('kecamatans', 'search', 'sortBy', 'sortOrder'));
    }

    /**
     * Menampilkan form untuk menambah kecamatan baru.
     */
    public function create()
    {
        return view('kecamatan.create');
    }

    /**
     * Menyimpan data kecamatan baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:100|unique:kecamatans,nama',
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
            'warna_polygon'    => 'nullable|string|max:20',
            // Input data statistik opsional saat pembuatan kecamatan
            'jumlah_penduduk'  => 'nullable|integer|min:0',
            'laju_pertumbuhan' => 'nullable|numeric|between:-100,100',
            'jumlah_desa'      => 'nullable|integer|min:0',
            'tahun'            => 'nullable|integer|digits:4',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $kecamatan = Kecamatan::create([
                'nama'          => $validated['nama'],
                'latitude'      => $validated['latitude'],
                'longitude'     => $validated['longitude'],
                'warna_polygon' => $validated['warna_polygon'] ?? null,
            ]);

            // Jika ada data statistik yang diisi, buat data statistiknya
            if ($request->filled('jumlah_penduduk')) {
                DataStatistik::create([
                    'kecamatan_id'     => $kecamatan->id,
                    'jumlah_penduduk'  => $validated['jumlah_penduduk'],
                    'laju_pertumbuhan' => $validated['laju_pertumbuhan'] ?? 0,
                    'jumlah_desa'      => $validated['jumlah_desa'] ?? 0,
                    'tahun'            => $validated['tahun'] ?? 2024,
                ]);
            }
        });

        return redirect()->route('kecamatan.index')
            ->with('success', 'Data Kecamatan ' . $validated['nama'] . ' berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail satu kecamatan.
     */
    public function show(Kecamatan $kecamatan)
    {
        $kecamatan->load(['statistik', 'semuaStatistik' => function ($q) {
            $q->orderBy('tahun', 'desc');
        }]);

        return view('kecamatan.show', compact('kecamatan'));
    }

    /**
     * Menampilkan form edit data kecamatan.
     */
    public function edit(Kecamatan $kecamatan)
    {
        $kecamatan->load('statistik');
        return view('kecamatan.edit', compact('kecamatan'));
    }

    /**
     * Memperbarui data kecamatan yang ada di database.
     */
    public function update(Request $request, Kecamatan $kecamatan)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:100|unique:kecamatans,nama,' . $kecamatan->id,
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
            'warna_polygon'    => 'nullable|string|max:20',
            // Update data statistik yang terhubung
            'jumlah_penduduk'  => 'nullable|integer|min:0',
            'laju_pertumbuhan' => 'nullable|numeric|between:-100,100',
            'jumlah_desa'      => 'nullable|integer|min:0',
            'tahun'            => 'nullable|integer|digits:4',
        ]);

        DB::transaction(function () use ($validated, $kecamatan, $request) {
            $kecamatan->update([
                'nama'          => $validated['nama'],
                'latitude'      => $validated['latitude'],
                'longitude'     => $validated['longitude'],
                'warna_polygon' => $validated['warna_polygon'] ?? null,
            ]);

            // Perbarui atau buat data statistik jika diinput
            if ($request->filled('jumlah_penduduk')) {
                DataStatistik::updateOrCreate(
                    [
                        'kecamatan_id' => $kecamatan->id,
                        'tahun'        => $validated['tahun'] ?? 2024,
                    ],
                    [
                        'jumlah_penduduk'  => $validated['jumlah_penduduk'],
                        'laju_pertumbuhan' => $validated['laju_pertumbuhan'] ?? 0,
                        'jumlah_desa'      => $validated['jumlah_desa'] ?? 0,
                    ]
                );
            }
        });

        return redirect()->route('kecamatan.index')
            ->with('success', 'Data Kecamatan ' . $kecamatan->nama . ' berhasil diperbarui!');
    }

    /**
     * Menghapus kecamatan beserta data statistiknya (cascade delete).
     */
    public function destroy(Kecamatan $kecamatan)
    {
        $nama = $kecamatan->nama;
        $kecamatan->delete();

        return redirect()->route('kecamatan.index')
            ->with('success', 'Data Kecamatan ' . $nama . ' berhasil dihapus!');
    }
}
