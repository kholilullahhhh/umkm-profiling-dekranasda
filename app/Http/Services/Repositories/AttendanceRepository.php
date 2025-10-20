<?php

namespace App\Http\Services\Repositories;

use App\Http\Services\Repositories\BaseRepository;
use App\Http\Services\Repositories\Contracts\AttendanceContract;
use App\Models\Attendance;
use App\Models\Course;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceRepository extends BaseRepository implements AttendanceContract
{
	protected $model;

	public function __construct(Attendance $model)
	{
		$this->model = $model;
	}

	/**
	 * Ambil data attendance dengan pagination
	 */
	public function paginated(array $criteria)
	{
		$perPage = $criteria['per_page'] ?? 10;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';

		return $this->model
			->with(['course', 'student'])
			->orderBy($field, $sortOrder)
			->paginate($perPage);
	}

	/**
	 * Store data attendance
	 */
	// public function store(array $data)
	// {
	// 	return $this->model->create($data);
	// }

	/**
	 * Update data attendance
	 */
	public function update(array $data, $id)
	{
		$attendance = $this->model->findOrFail($id);
		$attendance->update($data);
		return $attendance;
	}

	/**
	 * Find attendance by ID
	 */
	// public function find($id)
	// {
	// 	return $this->model->with(['course', 'student'])->findOrFail($id);
	// }

	/**
	 * Delete attendance
	 */
	public function delete($id)
	{
		$attendance = $this->model->findOrFail($id);
		$attendance->delete();
		return $attendance;
	}

	/**
	 * Validasi absensi berdasarkan lokasi dan waktu
	 */
	public function validateAttendance(array $data)
	{
		$course = Course::find($data['course_id']);

		if (!$course) {
			return [
				'is_valid' => false,
				'validation_message' => 'Kelas tidak ditemukan.',
				'distance' => null
			];
		}

		// Validasi lokasi menggunakan Haversine Formula
		$distance = $this->calculateDistance(
			$data['latitude'],
			$data['longitude'],
			$course->allowed_latitude,
			$course->allowed_longitude
		);

		$isValidLocation = $distance <= $course->allowed_radius;
		$isValidTime = $this->isWithinClassTime($course, $data['attendance_time']);

		if (!$isValidLocation) {
			$validationMessage = 'Lokasi tidak sesuai dengan kelas. Jarak: ' . round($distance, 2) . ' m';
		} elseif (!$isValidTime) {
			$validationMessage = 'Waktu absensi tidak sesuai dengan jadwal kelas.';
		} else {
			$validationMessage = 'Absensi valid.';
		}

		return [
			'is_valid' => $isValidLocation && $isValidTime,
			'validation_message' => $validationMessage,
			'distance' => $distance
		];
	}

	/**
	 * Hitung jarak antar dua titik koordinat menggunakan Haversine formula
	 */
	private function calculateDistance($lat1, $lon1, $lat2, $lon2)
	{
		$earthRadius = 6371000; // meters

		$latFrom = deg2rad($lat1);
		$lonFrom = deg2rad($lon1);
		$latTo = deg2rad($lat2);
		$lonTo = deg2rad($lon2);

		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;

		$angle = 2 * asin(
			sqrt(
				pow(sin($latDelta / 2), 2) +
				cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
			)
		);

		return $angle * $earthRadius;
	}

	/**
	 * Cek apakah waktu absensi masih dalam rentang waktu kelas
	 */
	private function isWithinClassTime($course, $attendanceTime)
	{
		$attendanceTime = Carbon::parse($attendanceTime);
		$classStart = Carbon::parse($course->start_time);
		$classEnd = Carbon::parse($course->end_time);

		return $attendanceTime->between($classStart, $classEnd);
	}

	/**
	 * Generate QR Code data untuk absensi
	 */
	public function generateQRCode($courseId)
	{
		$course = Course::find($courseId);

		if (!$course) {
			return [
				'success' => false,
				'error' => 'Kelas tidak ditemukan.'
			];
		}

		// Data QR Code
		$qrData = [
			'course_id' => $courseId,
			'timestamp' => now()->timestamp,
			'code' => Str::random(32),
			'type' => 'attendance'
		];

		$qrJson = json_encode($qrData);
		$qrBase64 = base64_encode($qrJson);

		// Generate Gambar QR Code (Base64 PNG)
		try {
			$qrImage = base64_encode(
				QrCode::format('png')
					->size(300)
					->margin(2)
					->errorCorrection('H')
					->generate($qrBase64)
			);

			$qrImageUrl = 'data:image/png;base64,' . $qrImage;

			// Simpan QR code ke database untuk validasi
			$course->update([
				'current_qr_code' => $qrBase64,
				'qr_expires_at' => now()->addMinutes(15)
			]);

			return [
				'success' => true,
				'qr_code' => $qrBase64,
				'qr_image' => $qrImageUrl,
				'expires_at' => now()->addMinutes(15)->format('Y-m-d H:i:s'),
				'course' => [
					'id' => $course->id,
					'name' => $course->name,
					'code' => $course->code
				]
			];

		} catch (\Exception $e) {
			return [
				'success' => false,
				'error' => 'Gagal generate QR Code: ' . $e->getMessage()
			];
		}
	}

	/**
	 * Validasi QR Code dan proses absensi
	 */
	public function processQRCode($qrCode, $studentId, $latitude, $longitude)
	{
		try {
			// Decode QR code
			$qrData = json_decode(base64_decode($qrCode), true);

			if (!$qrData || !isset($qrData['course_id'])) {
				return [
					'success' => false,
					'message' => 'QR Code tidak valid'
				];
			}

			$course = Course::find($qrData['course_id']);

			if (!$course) {
				return [
					'success' => false,
					'message' => 'Kelas tidak ditemukan'
				];
			}

			// Validasi QR code masih berlaku
			if (now()->timestamp - $qrData['timestamp'] > 900) { // 15 menit
				return [
					'success' => false,
					'message' => 'QR Code sudah kedaluwarsa'
				];
			}

			// Cek apakah QR code sesuai dengan yang tersimpan
			if ($course->current_qr_code !== $qrCode) {
				return [
					'success' => false,
					'message' => 'QR Code tidak valid untuk kelas ini'
				];
			}

			// Cek apakah sudah absen
			$existingAttendance = $this->model
				->where('course_id', $course->id)
				->where('student_id', $studentId)
				->whereDate('attendance_time', today())
				->first();

			if ($existingAttendance) {
				return [
					'success' => false,
					'message' => 'Anda sudah melakukan absensi untuk kelas ini hari ini'
				];
			}

			// Data absensi
			$attendanceData = [
				'course_id' => $course->id,
				'student_id' => $studentId,
				'qr_code' => $qrCode,
				'latitude' => $latitude,
				'longitude' => $longitude,
				'attendance_time' => now(),
				'location_name' => $this->getLocationName($latitude, $longitude)
			];

			// Validasi LBS
			$validation = $this->validateAttendance($attendanceData);

			$attendanceData['is_valid'] = $validation['is_valid'];
			$attendanceData['validation_message'] = $validation['validation_message'];

			// Simpan absensi
			$attendance = $this->model->create($attendanceData);

			return [
				'success' => true,
				'message' => 'Absensi berhasil dicatat',
				'attendance' => $attendance,
				'validation' => $validation,
				'course' => $course
			];

		} catch (\Exception $e) {
			return [
				'success' => false,
				'message' => 'Terjadi kesalahan: ' . $e->getMessage()
			];
		}
	}

	/**
	 * Get location name from coordinates (simple version)
	 */
	private function getLocationName($latitude, $longitude)
	{
		return "Lat: $latitude, Long: $longitude";
	}

	/**
	 * Get active QR Code for course
	 */
	public function getActiveQRCode($courseId)
	{
		$course = Course::find($courseId);

		if (!$course || !$course->current_qr_code || $course->qr_expires_at < now()) {
			return [
				'success' => false,
				'message' => 'Tidak ada QR Code aktif'
			];
		}

		return [
			'success' => true,
			'qr_code' => $course->current_qr_code,
			'expires_at' => $course->qr_expires_at,
			'course' => $course
		];
	}

	/**
	 * Get report data
	 */
	public function getReportData(array $criteria)
	{
		// Implementasi report data sesuai kebutuhan
		return $this->model
			->with(['course', 'student'])
			->whereBetween('attendance_time', [
				$criteria['start_date'] ?? now()->startOfMonth(),
				$criteria['end_date'] ?? now()->endOfMonth()
			])
			->get();
	}
}