@extends('admin._layouts.index')

@push($title)
    active
@endpush

@section('content')
    <!--begin::Toolbar-->
    @component('admin._card.breadcrumb')
        @slot('header')
            {{ $title }}
        @endslot
        @slot('page')
            Form
        @endslot
    @endcomponent
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!--begin::Tables Widget 10-->
            <div class="card mb-5 mb-xl-8">

                <!--begin::Header-->
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Form {{ isset($data->id) ? 'Edit' : 'Input' }} Absensi</span>
                    </h3>
                </div>
                <!--end::Header-->

                <!--begin::Body-->
                <div class="card-body pt-3">

                    <div class="row mt-5">
                        <!--begin:Form-->
                        <form id="kt_modal_new_target_form" class="form" action="#">
                            <input name="_method" type="hidden" id="methodId"
                                value="{{ isset($data->id) ? 'PUT' : 'POST' }}">
                            <input type="hidden" name="id" id="formId" value="{{ $data->id ?? null }}">
                            @csrf

                            <!--begin::Input group-->
                            <div class="row g-9 mb-8">
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Kelas</label>
                                    <select class="form-control" name="course_id" id="course_id" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                {{ isset($data->course_id) && $data->course_id == $course->id ? 'selected' : '' }}>
                                                {{ $course->code }} - {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Mahasiswa</label>
                                    <select class="form-control" name="student_id" id="student_id" required>
                                        <option value="">Pilih Mahasiswa</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}"
                                                {{ isset($data->student_id) && $data->student_id == $student->id ? 'selected' : '' }}>
                                                {{ $student->nim }} - {{ $student->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Waktu Absensi</label>
                                    <input type="datetime-local" class="form-control" name="attendance_time" id="attendance_time"
                                        value="{{ isset($data->attendance_time) ? $data->attendance_time->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}" required />
                                </div>

                                <div class="col-md-3 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Latitude</label>
                                    <input type="number" step="any" class="form-control" placeholder="Latitude" name="latitude" id="latitude"
                                        value="{{ $data->latitude ?? '' }}" required />
                                </div>

                                <div class="col-md-3 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Longitude</label>
                                    <input type="number" step="any" class="form-control" placeholder="Longitude" name="longitude" id="longitude"
                                        value="{{ $data->longitude ?? '' }}" required />
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Status Validasi</label>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="is_valid" id="is_valid"
                                            value="1" {{ isset($data->is_valid) && $data->is_valid ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_valid">
                                            Tandai sebagai Valid
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-12 fv-row">
                                    <label class="fs-12 fw-semibold mb-2">Pesan Validasi</label>
                                    <textarea class="form-control" placeholder="Pesan validasi" name="validation_message" id="validation_message" rows="3">{{ $data->validation_message ?? '' }}</textarea>
                                </div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Actions-->
                            <div class="d-flex justify-content-end">
                                <a href="{{ route($title . '.index') }}">
                                    <button type="button" id="kt_modal_new_target_cancel" class="btn btn-secondary me-3"
                                        data-bs-dismiss="modal">Batal</button>
                                </a>
                                @if (isset($data->id))
                                    <button type="submit" id="kt_modal_new_target_update" class="btn btn-primary">
                                        <span class="indicator-label">Update</span>
                                        <span class="indicator-progress">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                @else
                                    <button type="submit" id="kt_modal_new_target_save" class="btn btn-primary">
                                        <span class="indicator-label">Simpan</span>
                                        <span class="indicator-progress">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                @endif
                            </div>
                            <!--end::Actions-->

                        </form>
                        <!--end:Form-->
                    </div>

                </div>
                <!--begin::Body-->
            </div>
            <!--end::Tables Widget 10-->

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('jsScriptForm')
    <script type="text/javascript">
        // Define form element
        const form = document.getElementById('kt_modal_new_target_form');

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        var validator = FormValidation.formValidation(
            form, {
            fields: {
                'course_id': {
                    validators: {
                        notEmpty: {
                            message: 'Kelas harus dipilih'
                        }
                    }
                },
                'student_id': {
                    validators: {
                        notEmpty: {
                            message: 'Mahasiswa harus dipilih'
                        }
                    }
                },
                'attendance_time': {
                    validators: {
                        notEmpty: {
                            message: 'Waktu absensi harus diisi'
                        }
                    }
                },
                'latitude': {
                    validators: {
                        notEmpty: {
                            message: 'Latitude harus diisi'
                        },
                        numeric: {
                            message: 'Latitude harus berupa angka'
                        }
                    }
                },
                'longitude': {
                    validators: {
                        notEmpty: {
                            message: 'Longitude harus diisi'
                        },
                        numeric: {
                            message: 'Longitude harus berupa angka'
                        }
                    }
                },
            },

            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: '',
                    eleValidClass: ''
                })
            },

        }
        );

        // Get current location button
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        $('#latitude').val(position.coords.latitude);
                        $('#longitude').val(position.coords.longitude);
                        toastr.success('Lokasi berhasil didapatkan');
                    },
                    function(error) {
                        toastr.error('Gagal mendapatkan lokasi: ' + error.message);
                    }
                );
            } else {
                toastr.error('Browser tidak mendukung geolocation');
            }
        }

        // Add get location button
        $(document).ready(function() {
            $('.col-md-3.fv-row:first').append(`
                <button type="button" class="btn btn-sm btn-light mt-2" onclick="getCurrentLocation()">
                    <i class="ki-duotone ki-geolocation fs-2 me-1"></i>
                    Ambil Lokasi Sekarang
                </button>
            `);
        });
    </script>

    @if (isset($data->id))
        @include('admin._card._updateAjax')
    @else
        @include('admin._card._createAjax')
    @endif

@endpush