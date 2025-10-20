@extends('admin._layouts.index')

@push('attendance')
    active
@endpush

@section('content')
    <!--begin::Toolbar-->
    @component('admin._card.breadcrumb')
        @slot('header')
            Generate QR Code Absensi
        @endslot
        @slot('page')
            Generate QR
        @endslot
    @endcomponent
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card card-flush">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <h2 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-2 mb-1">Generate QR Code Absensi</span>
                    </h2>
                </div>

                <div class="card-body pt-0">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Pilih Kelas</label>
                                <select id="course-select" class="form-select form-select-solid">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="text-center">
                                <button id="generate-btn" class="btn btn-primary btn-lg">
                                    <i class="ki-duotone ki-qr-code fs-2 me-2"></i>
                                    Generate QR Code
                                </button>
                            </div>

                            <div id="qr-result" class="mt-4 text-center" style="display: none;">
                                <div class="alert alert-info">
                                    <p>QR Code berlaku hingga: <span id="expiry-time"></span></p>
                                </div>
                                <div id="qr-image-container" class="mb-3"></div>
                                <button id="download-btn" class="btn btn-success me-2">
                                    <i class="ki-duotone ki-download fs-2 me-2"></i>
                                    Download QR
                                </button>
                                <button id="refresh-btn" class="btn btn-warning">
                                    <i class="ki-duotone ki-refresh fs-2 me-2"></i>
                                    Refresh QR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('jsScript')
<script>
$(document).ready(function() {
    $('#generate-btn').on('click', function() {
        const courseId = $('#course-select').val();
        
        if (!courseId) {
            toastr.error('Pilih kelas terlebih dahulu');
            return;
        }

        $.ajax({
            url: '{{ route("attendance.generate-qr") }}/' + courseId,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#qr-image-container').html(`
                        <img src="${response.qr_image}" alt="QR Code" class="img-fluid border rounded p-2" style="max-width: 300px;">
                    `);
                    $('#expiry-time').text(response.expires_at);
                    $('#qr-result').show();
                    toastr.success('QR Code berhasil digenerate');
                } else {
                    toastr.error(response.error);
                }
            },
            error: function() {
                toastr.error('Gagal generate QR Code');
            }
        });
    });

    // Download QR Code
    $('#download-btn').on('click', function() {
        const img = $('#qr-image-container img')[0];
        const link = document.createElement('a');
        link.download = 'qr-code-attendance.png';
        link.href = img.src;
        link.click();
    });

    // Refresh QR Code
    $('#refresh-btn').on('click', function() {
        $('#generate-btn').click();
    });
});
</script>
@endpush