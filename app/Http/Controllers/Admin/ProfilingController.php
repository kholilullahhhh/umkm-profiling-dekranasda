<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Repositories\Contracts\ProfilingContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Umkm;
use App\Models\User;

class ProfilingController extends Controller
{
    protected $title, $repo, $response;

    public function __construct(ProfilingContract $repo)
    {
        $this->title = 'profiling';
        $this->repo = $repo;
    }

    public function index()
    {
        try {
            $title = $this->title;
            return view('admin.' . $this->title . '.index', compact('title'));
        } catch (\Exception $e) {
            return view('errors.message', ['message' => $e->getMessage()]);
        }
    }

    public function data(Request $request)
    {
        try {
            $title = $this->title;
            $data = $this->repo->paginated($request->all());
            $perPage = $request->per_page == '' ? 5 : $request->per_page;
            $view = view('admin.' . $this->title . '.data', compact('data', 'title'))->with('i', ($request->input('page', 1) -
                1) * $perPage)->render();
            return response()->json([
                "total_page" => $data->lastpage(),
                "total_data" => $data->total(),
                "html" => $view,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        try {
            $title = $this->title;
            $umkm = Umkm::all(); // Ambil data jenis usaha untuk dropdown
            // $users = User::all(); // Ambil data Pengguna | pemilik untuk dropdown

            return view('admin.' . $this->title . '.form', compact('title', 'umkm'));
        } catch (\Exception $e) {
            return view('errors.message', ['message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi data
            $req = $request->all();

            // Format omset jika ada
            if ($request->has('omset_per_tahun') && $request->omset_per_tahun) {
                $req['omset_per_tahun'] = str_replace(['.', ','], ['', '.'], $request->omset_per_tahun);
            }

            // Set status binaan default true jika tidak diisi
            $req['status_binaan'] = $request->has('status_binaan') ? true : false;

            $data = $this->repo->store($req);
            return response()->json([
                'data' => $data,
                'success' => true,
                'message' => 'Data UMKM berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $title = $this->title;
            $data = $this->repo->find($id);
            $umkm = Umkm::all(); // Ambil data jenis usaha untuk dropdown
            // $users = User::all(); // Ambil data Pengguna | pemilik untuk dropdown

            if (!$data) {
                return redirect()->route($this->title . '.index')
                    ->with('error', 'Data UMKM tidak ditemukan');
            }

            return view('admin.' . $this->title . '.form', compact('title', 'data', 'umkm'));
        } catch (\Exception $e) {
            return view('errors.message', ['message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validasi data
            $req = $request->all();

            // // Format omset jika ada
            if ($request->has('omset_per_tahun') && $request->omset_per_tahun) {
                $req['omset_per_tahun'] = str_replace(['.', ','], ['', '.'], $request->omset_per_tahun);
            }

            // Set status binaan
            $req['status_binaan'] = $request->has('status_binaan') ? true : false;

            $data = $this->repo->update($req, $request->id);
            return response()->json([
                'data' => $data,
                'success' => true,
                'message' => 'Data UMKM berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $data = $this->repo->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Data UMKM berhasil dihapus',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method tambahan untuk mendapatkan data UMKM berdasarkan user
    public function getByUser($userId)
    {
        try {
            $data = $this->repo->getByUser($userId);
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk mengubah status binaan
    public function updateStatusBinaan(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:umkms,id',
                'status_binaan' => 'required|boolean'
            ]);

            $data = $this->repo->updateStatusBinaan($request->id, $request->status_binaan);
            return response()->json([
                'success' => true,
                'message' => 'Status binaan berhasil diupdate',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }
}