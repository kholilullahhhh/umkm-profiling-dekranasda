<?php

namespace App\Http\Controllers\Admin;
use App\Http\Services\Repositories\Contracts\AttendanceContract;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Student;

class AttendanceController extends Controller
{
    protected $title, $repo, $response;

    public function __construct(AttendanceContract $repo)
    {
        $this->title = 'attendance';
        $this->repo = $repo;
        $this->response = [
            'success' => false,
            'data' => null,
            'message' => null
        ];
    }

    public function index()
    {
        try {
            $title = $this->title;
            return view('admin.' . $title . '.index', compact('title'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function data(Request $request)
    {
        try {
            $title = $this->title;
            $data = $this->repo->paginated($request->all());
            $perPage = $request->per_page == '' ? 10 : $request->per_page;

            $view = view('admin.' . $title . '.data', compact('data', 'title'))
                ->with('i', ($request->input('page', 1) - 1) * $perPage)
                ->render();

            return response()->json([
                "total_page" => $data->lastPage(),
                "total_data" => $data->total(),
                "html" => $view,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        try {
            $title = $this->title;
            $courses = Course::where('is_active', true)->get();
            $students = Student::all();
            return view('admin.' . $title . '.form', compact('title', 'courses', 'students'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'student_id' => 'required|exists:students,id',
                'attendance_time' => 'required|date',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $data = $request->all();
            $data['qr_code'] = 'MANUAL-' . now()->timestamp;
            $data['location_name'] = $this->getLocationName($request->latitude, $request->longitude);

            // Validasi LBS
            $validation = $this->repo->validateAttendance($data);
            $data['is_valid'] = $validation['is_valid'];
            $data['validation_message'] = $validation['validation_message'];

            $attendance = $this->repo->store($data);

            return response()->json([
                'success' => true,
                'data' => $attendance,
                'message' => 'Data absensi berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $title = $this->title;
            $data = $this->repo->find($id);
            $courses = Course::where('is_active', true)->get();
            $students = Student::all();
            return view('admin.' . $title . '.form', compact('title', 'data', 'courses', 'students'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'student_id' => 'required|exists:students,id',
                'attendance_time' => 'required|date',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $data = $request->all();
            $data['location_name'] = $this->getLocationName($request->latitude, $request->longitude);

            // Validasi LBS untuk update
            $validation = $this->repo->validateAttendance($data);
            $data['is_valid'] = $validation['is_valid'];
            $data['validation_message'] = $validation['validation_message'];

            $attendance = $this->repo->update($data, $id);

            return response()->json([
                'success' => true,
                'data' => $attendance,
                'message' => 'Data absensi berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function scanQR()
    {
        try {
            $title = $this->title;
            return view('admin.' . $title . '.scan', compact('title'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function processAttendance(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'student_id' => 'required|exists:students,id'
            ]);

            $result = $this->repo->processQRCode(
                $request->qr_code,
                $request->student_id,
                $request->latitude,
                $request->longitude
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function generateQR($courseId)
    {
        try {
            $result = $this->repo->generateQRCode($courseId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getActiveQR($courseId)
    {
        try {
            $result = $this->repo->getActiveQRCode($courseId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function report()
    {
        try {
            $title = $this->title;
            return view('admin.' . $title . '.report', compact('title'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getReportData(Request $request)
    {
        try {
            $data = $this->repo->getReportData($request->all());
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

    private function getLocationName($latitude, $longitude)
    {
        // Implementasi reverse geocoding menggunakan Google Maps API atau service lainnya
        // Untuk sementara, return koordinat
        return "Lat: $latitude, Long: $longitude";
    }
}