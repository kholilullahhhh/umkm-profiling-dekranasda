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
                        <span class="card-label fw-bold fs-3 mb-1">Form {{ isset($data->id) ? 'Edit' : 'Input' }} UMKM</span>
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
                                    <label class="fs-6 fw-semibold mb-2 required">Nama Usaha</label>
                                    <input type="text" class="form-control" placeholder="Nama usaha" name="nama_usaha" id="nama_usaha"
                                        value="{{ $data->nama_usaha ?? '' }}" />
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2 required">Pemilik</label>
                                    <input type="text" class="form-control" placeholder="Nama pemilik" name="pemilik" id="pemilik"
                                        value="{{ $data->pemilik ?? '' }}" />
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2 required">Jenis Usaha</label>
                                    <select class="form-select" name="jenis_usaha_id" id="jenis_usaha_id">
                                        <option value="">Pilih Jenis Usaha</option>
                                        @foreach($jenisUsaha as $jenis)
                                            <option value="{{ $jenis->id }}" {{ isset($data->jenis_usaha_id) && $data->jenis_usaha_id == $jenis->id ? 'selected' : '' }}>
                                                {{ $jenis->nama_jenis ?? $jenis->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2 required">Kabupaten</label>
                                    <input type="text" class="form-control" placeholder="Kabupaten" name="kabupaten" id="kabupaten"
                                        value="{{ $data->kabupaten ?? '' }}" />
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Alamat</label>
                                    <textarea class="form-control" placeholder="Alamat lengkap" name="alamat" id="alamat" rows="3">{{ $data->alamat ?? '' }}</textarea>
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Tahun Berdiri</label>
                                    <input type="number" class="form-control" placeholder="Tahun berdiri" name="tahun_berdiri" id="tahun_berdiri"
                                        min="1900" max="{{ date('Y') }}" value="{{ $data->tahun_berdiri ?? '' }}" />
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2 required">Skala Usaha</label>
                                    <select class="form-select" name="skala_usaha" id="skala_usaha">
                                        <option value="">Pilih Skala Usaha</option>
                                        <option value="mikro" {{ isset($data->skala_usaha) && $data->skala_usaha == 'mikro' ? 'selected' : '' }}>Mikro</option>
                                        <option value="kecil" {{ isset($data->skala_usaha) && $data->skala_usaha == 'kecil' ? 'selected' : '' }}>Kecil</option>
                                        <option value="menengah" {{ isset($data->skala_usaha) && $data->skala_usaha == 'menengah' ? 'selected' : '' }}>Menengah</option>
                                    </select>
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Omset per Tahun (Rp)</label>
                                    <input type="number" class="form-control" placeholder="Omset per tahun" name="omset_per_tahun" id="omset_per_tahun"
                                        step="0.01" min="0" value="{{ $data->omset_per_tahun ?? '' }}" />
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Kontak</label>
                                    <input type="text" class="form-control" placeholder="Nomor kontak" name="kontak" id="kontak"
                                        value="{{ $data->kontak ?? '' }}" />
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Status Binaan</label>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="status_binaan" id="status_binaan"
                                            value="1" {{ isset($data->status_binaan) && $data->status_binaan ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_binaan">
                                            Aktif sebagai binaan
                                        </label>
                                    </div>
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

        // Init form validation rules
        var validator = FormValidation.formValidation(
            form, {
            fields: {
                'nama_usaha': {
                    validators: {
                        notEmpty: {
                            message: 'Nama usaha is required'
                        }
                    }
                },
                'pemilik': {
                    validators: {
                        notEmpty: {
                            message: 'Nama pemilik is required'
                        }
                    }
                },
                'jenis_usaha_id': {
                    validators: {
                        notEmpty: {
                            message: 'Jenis usaha is required'
                        }
                    }
                },
                'kabupaten': {
                    validators: {
                        notEmpty: {
                            message: 'Kabupaten is required'
                        }
                    }
                },
                'skala_usaha': {
                    validators: {
                        notEmpty: {
                            message: 'Skala usaha is required'
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

        // Format currency for omset
        $('#omset_per_tahun').on('input', function() {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(formatRupiah(value));
            }
        });

        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }
    </script>

    @if (isset($data->id))
        @include('admin._card._updateAjax')
    @else
        @include('admin._card._createAjax')
    @endif

@endpush