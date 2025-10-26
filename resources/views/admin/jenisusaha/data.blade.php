@foreach ($data as $key => $v)
    <tr class="align-middle text-gray-700 fw-semibold fs-7">
        <td>
            <span class="badge bg-light-primary text-primary fw-bold">
                {{ ++$i }}
            </span>
        </td>

        <td>
            <div class="d-flex align-items-center">
                <i class="bi {{ $v->icon ?? 'bi-stack' }} fs-4 me-2 text-primary"></i>
                <span>{{ $v->nama_jenis }}</span>
            </div>
        </td>

        <td>
            <span class="text-muted">{{ Str::limit($v->deskripsi, 50) }}</span>
        </td>


        <td class="text-end">
            {!! Helper::btnAction($v->id, $title) !!}
        </td>
    </tr>
@endforeach