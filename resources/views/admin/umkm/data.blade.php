@foreach ($data as $key => $v)
    <tr class="align-middle text-gray-700 fw-semibold fs-7">
        <td>
            <span class="badge bg-light-primary text-primary fw-bold">
                {{ ++$i }}
            </span>
        </td>

        <td>
            <div class="d-flex align-items-center">
                <i class="bi bi-shop fs-4 me-2 text-primary"></i>
                <span>{{ $v->nama_usaha }}</span>
            </div>
        </td>

        <td>
            <span class="text-muted">{{ $v->user->name ?? 'N/A' }}</span>
        </td>

        <td>
            <span class="text-muted">{{ $v->jenisUsaha->nama_jenis ?? $v->jenisUsaha->nama ?? 'N/A' }}</span>
        </td>

        <td>
            <span class="text-muted">{{ $v->kabupaten }}</span>
        </td>

        <td>
            @if($v->skala_usaha == 'mikro')
                <span class="badge bg-success">Mikro</span>
            @elseif($v->skala_usaha == 'kecil')
                <span class="badge bg-warning text-dark">Kecil</span>
            @else
                <span class="badge bg-primary">Menengah</span>
            @endif
        </td>

        <td>
            @if ($v->status_binaan)
                <span class="badge bg-success">Aktif</span>
            @else
                <span class="badge bg-secondary">Non-Aktif</span>
            @endif
        </td>

        <td class="text-end">
            {!! Helper::btnAction($v->id, $title) !!}
        </td>
    </tr>
@endforeach