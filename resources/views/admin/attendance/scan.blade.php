@extends('admin._layouts.index')

@push('css')
    <style>
        #scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        #interactive {
            width: 100%;
            height: 400px;
            border: 2px solid #009ef7;
            border-radius: 8px;
        }
        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .scanner-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: #009ef7;
            animation: scan 2s infinite linear;
        }
        @keyframes scan {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
        }
    </style>
@endpush

@push('attendance')
    active
@endpush

@section('content')
    <!--begin::Toolbar-->
    @component('admin._card.breadcrumb')
        @slot('header')
            Scan QR Code Absensi
        @endslot
        @slot('page')
            Scan
        @endslot
    @endcomponent
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Card-->
            <div class="card card-flush">
                <!--begin::Card header-->
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <h2 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-2 mb-1">Scan QR Code Absensi</span>
                        <span class="text-muted mt-1 fw-semibold fs-6">Arahkan kamera ke QR Code untuk absensi</span>
                    </h2>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::QR Scanner-->
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="qr-scanner text-center">
                                <div class="alert alert-info mb-4">
                                    <i class="ki-duotone ki-information fs-2 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Pastikan Anda mengizinkan akses kamera dan lokasi untuk proses absensi
                                </div>

                                <!-- Scanner Container -->
                                <div id="scanner-container">
                                    <div id="interactive" class="viewport"></div>
                                    <div class="scanner-overlay">
                                        <div class="scanner-line"></div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <select id="camera-select" class="form-select form-select-solid mb-3" style="max-width: 300px; margin: 0 auto;">
                                        <option value="">Pilih Kamera...</option>
                                    </select>
                                </div>

                                <div id="scanned-result" class="mb-4"></div>

                                <div class="mt-4">
                                    <button type="button" id="start-scanner" class="btn btn-primary">
                                        <i class="ki-duotone ki-play fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Mulai Scanner
                                    </button>
                                    <button type="button" id="stop-scanner" class="btn btn-danger" style="display: none;">
                                        <i class="ki-duotone ki-stop fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Stop Scanner
                                    </button>
                                    <button type="button" id="switch-camera" class="btn btn-light-primary" style="display: none;">
                                        <i class="ki-duotone ki-repeat fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Ganti Kamera
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::QR Scanner-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('jsScript')
    <!-- Gunakan library QuaggaJS untuk QR Code scanning -->
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script>
        let currentCameraId = null;
        let cameras = [];
        let isScannerRunning = false;

        // Initialize cameras list
        async function initializeCameras() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                cameras = devices.filter(device => device.kind === 'videoinput');
                
                const cameraSelect = document.getElementById('camera-select');
                cameraSelect.innerHTML = '<option value="">Pilih Kamera...</option>';
                
                cameras.forEach((camera, index) => {
                    const option = document.createElement('option');
                    option.value = camera.deviceId;
                    option.text = camera.label || `Kamera ${index + 1}`;
                    cameraSelect.appendChild(option);
                });

                if (cameras.length > 0) {
                    currentCameraId = cameras[0].deviceId;
                    cameraSelect.value = currentCameraId;
                }
            } catch (error) {
                console.error('Error getting cameras:', error);
                showError('Tidak dapat mengakses daftar kamera');
            }
        }

        // Start QR Scanner
        function startScanner(cameraId = null) {
            if (isScannerRunning) return;

            const config = {
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#interactive'),
                    constraints: {
                        width: 640,
                        height: 480,
                        facingMode: "environment", // Gunakan kamera belakang
                        deviceId: cameraId ? { exact: cameraId } : undefined
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: 2,
                decoder: {
                    readers: ["qrcode_reader"]
                },
                locate: true
            };

            Quagga.init(config, function(err) {
                if (err) {
                    console.error('Error initializing Quagga:', err);
                    showError('Gagal menginisialisasi scanner: ' + err.message);
                    return;
                }
                
                Quagga.start();
                isScannerRunning = true;
                $('#start-scanner').hide();
                $('#stop-scanner').show();
                $('#switch-camera').show();
                $('#scanned-result').html('<div class="alert alert-info">Scanner aktif - Arahkan kamera ke QR Code</div>');
                
                console.log('Quagga scanner started successfully');
            });

            Quagga.onDetected(function(result) {
                console.log('QR Code detected:', result);
                onScanSuccess(result.codeResult.code);
            });
        }

        // Stop scanner
        function stopScanner() {
            if (isScannerRunning) {
                Quagga.stop();
                isScannerRunning = false;
                $('#start-scanner').show();
                $('#stop-scanner').hide();
                $('#switch-camera').hide();
                $('#scanned-result').html('<div class="alert alert-warning">Scanner dihentikan</div>');
            }
        }

        // Handle scan success
        function onScanSuccess(decodedText) {
            console.log('QR Code scanned:', decodedText);
            
            stopScanner();
            
            $('#scanned-result').html(`
                <div class="alert alert-warning text-center">
                    <div class="spinner-border text-primary me-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Memproses absensi...
                </div>
            `);

            // Get user location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        processAttendance(decodedText, position.coords.latitude, position.coords.longitude);
                    },
                    function(error) {
                        handleLocationError(error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            } else {
                showError('Browser tidak mendukung geolocation');
            }
        }

        // Process attendance
        function processAttendance(qrCode, latitude, longitude) {
            const studentId = {{ auth()->check() && auth()->user()->student ? auth()->user()->student->id : '1' }};

            $.ajax({
                url: '{{ route("attendance.process") }}',
                method: 'POST',
                data: {
                    qr_code: qrCode,
                    latitude: latitude,
                    longitude: longitude,
                    student_id: studentId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#scanned-result').html(`
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-check fs-2hx text-success me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="text-start">
                                        <h4 class="text-success">Absensi Berhasil!</h4>
                                        <p class="mb-1">${response.validation.validation_message}</p>
                                        <p class="mb-1">Jarak: ${response.validation.distance ? response.validation.distance.toFixed(2) : 0} meter</p>
                                        <p class="mb-0">Waktu: ${new Date().toLocaleString('id-ID')}</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-primary" onclick="startScanner()">Scan Lagi</button>
                                </div>
                            </div>
                        `);
                    } else {
                        $('#scanned-result').html(`
                            <div class="alert alert-danger">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-cross fs-2hx text-danger me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="text-start">
                                        <h4 class="text-danger">Absensi Gagal!</h4>
                                        <p class="mb-0">${response.message}</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-primary" onclick="startScanner()">Coba Lagi</button>
                                </div>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $('#scanned-result').html(`
                        <div class="alert alert-danger">
                            <h4>Error Server!</h4>
                            <p>Terjadi kesalahan pada server. Silakan coba lagi.</p>
                            <p>Detail: ${xhr.responseJSON?.message || error}</p>
                            <button class="btn btn-primary mt-2" onclick="startScanner()">Coba Lagi</button>
                        </div>
                    `);
                }
            });
        }

        // Handle location errors
        function handleLocationError(error) {
            let errorMessage = 'Gagal mendapatkan lokasi: ';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage += "User menolak akses lokasi.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage += "Informasi lokasi tidak tersedia.";
                    break;
                case error.TIMEOUT:
                    errorMessage += "Request lokasi timeout.";
                    break;
                case error.UNKNOWN_ERROR:
                    errorMessage += "Error tidak diketahui.";
                    break;
            }
            $('#scanned-result').html(`
                <div class="alert alert-danger">
                    <h4>Error Lokasi!</h4>
                    <p>${errorMessage}</p>
                    <button class="btn btn-primary mt-2" onclick="startScanner()">Coba Lagi</button>
                </div>
            `);
        }

        // Show error message
        function showError(message) {
            $('#scanned-result').html(`
                <div class="alert alert-danger">
                    <h4>Error!</h4>
                    <p>${message}</p>
                </div>
            `);
        }

        // Initialize when page loads
        $(document).ready(function() {
            initializeCameras();

            // Start scanner button
            $('#start-scanner').on('click', function() {
                const cameraId = $('#camera-select').val();
                startScanner(cameraId || null);
            });

            // Stop scanner button
            $('#stop-scanner').on('click', function() {
                stopScanner();
            });

            // Switch camera
            $('#switch-camera').on('click', function() {
                stopScanner();
                setTimeout(() => {
                    const cameraId = $('#camera-select').val();
                    startScanner(cameraId || null);
                }, 500);
            });

            // Camera select change
            $('#camera-select').on('change', function() {
                if (isScannerRunning) {
                    stopScanner();
                    setTimeout(() => {
                        startScanner(this.value || null);
                    }, 500);
                }
            });
        });

        // Cleanup on page unload
        $(window).on('beforeunload', function() {
            if (isScannerRunning) {
                Quagga.stop();
            }
        });
    </script>
@endpush