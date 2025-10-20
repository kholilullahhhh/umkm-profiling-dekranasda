@foreach ($data as $key => $v)
    <tr class="align-middle text-gray-700 fw-semibold fs-7">
        <td>
            <span class="badge bg-light-primary text-primary fw-bold">
                {{ ++$i }}
            </span>
        </td>

        <td>
            <div class="d-flex align-items-center">
                <i class="bi bi-person-check fs-4 me-2 text-primary"></i>
                <span>{{ $v->student->name ?? 'N/A' }}</span>
            </div>
        </td>

        <td>
            <span class="text-muted">{{ $v->course->name ?? 'N/A' }}</span>
        </td>

        <td>
            <span class="text-muted">{{ $v->attendance_time->format('d M Y H:i') }}</span>
        </td>

        <td>
            @if ($v->is_valid)
                <span class="badge bg-success">Valid</span>
            @else
                <span class="badge bg-danger">Invalid</span>
            @endif
        </td>

        <td>
            <span class="text-muted">{{ Str::limit($v->validation_message, 30) }}</span>
        </td>

        <td class="text-end">
            {!! Helper::btnAction($v->id, $title) !!}
        </td>
    </tr>
@endforeach