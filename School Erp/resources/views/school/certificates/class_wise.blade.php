@extends('layouts.app')

@section('page-title', 'Class-wise Student Certificate')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-users-cog" style="color:var(--gold);margin-right:8px;"></i>Class-wise Student Certificate</h1>
        <p>Bulk generate certificates for all students enrolled in a specific class grade</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Select Class Grade</h3>
        <form method="GET" action="{{ route('school.certificates.class-wise') }}" style="display:flex; gap:8px;">
            <select name="class_id" class="form-control" onchange="this.form.submit()" required>
                <option value="">Select Class</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body" style="padding:0;">
        @if($selectedClassId)
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Student Details</th>
                            <th>Class & Section</th>
                            <th>Choose Template</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $st)
                        <tr>
                            <td>
                                <strong style="color:var(--navy);">{{ $st->full_name }}</strong>
                                <small style="display:block; color:var(--t3);">{{ $st->admission_id }}</small>
                            </td>
                            <td>{{ optional($st->class)->name ?? 'N/A' }} - {{ optional($st->section)->name ?? 'N/A' }}</td>
                            <td>
                                <select id="bulkCertTpl_{{ $st->id }}" class="form-control" style="width:auto; padding:4px 8px; font-size:12px;">
                                    @foreach($templates as $tpl)
                                        <option value="{{ $tpl->id }}">{{ $tpl->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-gold" style="padding:4px 10px; font-size:11px;" onclick="generateSingleBulkCert('{{ $st->id }}', '{{ $st->full_name }}')">
                                    <i class="fas fa-stamp"></i> Issue Certificate
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px; color:var(--t3);">No students registered in this class.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding:40px; text-align:center; color:var(--t3);">
                <i class="fas fa-school" style="font-size:48px; margin-bottom:12px; opacity:0.3;"></i>
                <p>Please select a class grade from the dropdown to load the student registry.</p>
            </div>
        @endif
    </div>
</div>

<script>
function generateSingleBulkCert(studentId, studentName) {
    const tplId = document.getElementById('bulkCertTpl_' + studentId).value;
    
    // Perform ajax request to issue
    $.ajax({
        url: "{{ route('school.certificates.manage') }}",
        method: "POST",
        data: {
            student_id: studentId,
            certificate_template_id: tplId,
            issue_date: "{{ date('Y-m-d') }}",
            _token: "{{ csrf_token() }}"
        },
        success: function() {
            showToast('Certificate issued successfully for ' + studentName + '!');
        },
        error: function() {
            showToast('Unable to issue certificate.');
        }
    });
}
</script>
@endsection
