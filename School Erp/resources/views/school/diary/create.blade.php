@extends('layouts.app')

@section('page-title', 'Create Diary')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-book-open" style="color:var(--gold);margin-right:8px;"></i>Digital Class Diary</h1>
        <p>Post class assignments, homework, worksheets, and teacher notes to student/parent diaries</p>
    </div>
</div>

<div class="grid-3">
    <!-- Post Diary entry -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>New Diary Entry</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.diary.create') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Grade Class</label>
                    <select name="class_id" class="form-control" onchange="loadClassSections(this.value)" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Section</label>
                    <select name="section_id" id="diarySectionSel" class="form-control" required>
                        <option value="">Select Section</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Diary Date</label>
                    <input type="date" name="diary_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Title / Subject</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Science Homework - Chapter 3" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Diary Content & Homework Instructions</label>
                    <textarea name="content" class="form-control" style="height:120px;" placeholder="Write detailed instructions for the students..." required></textarea>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-paper-plane"></i> Publish Entry
                </button>
            </form>
        </div>
    </div>

    <!-- Active Diaries List -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Recent Digital Diary Logs</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Class & Section</th>
                            <th>Entry Details</th>
                            <th>Teacher Assigned</th>
                            <th>Diary Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($diaries as $diary)
                        <tr>
                            <td><strong>{{ $diary->class->name }}</strong> - {{ $diary->section->name }}</td>
                            <td>
                                <div style="font-weight:700; color:var(--navy);">{{ $diary->title }}</div>
                                <p style="color:var(--t2); font-size:12px; margin-top:4px;">{{ $diary->content }}</p>
                            </td>
                            <td>{{ optional($diary->teacher)->full_name ?? 'School Administration' }}</td>
                            <td><span style="font-family:monospace;">{{ $diary->diary_date }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px; color:var(--t3);">No digital diary logs published.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const classSections = {
    @foreach($classes as $c)
    "{{ $c->id }}": [
        @foreach($c->sections as $sec)
        { id: "{{ $sec->id }}", name: "{{ $sec->name }}" },
        @endforeach
    ],
    @endforeach
};

function loadClassSections(classId) {
    const sections = classSections[classId] || [];
    let html = '<option value="">Select Section</option>';
    sections.forEach(s => {
        html += `<option value="${s.id}">${s.name}</option>`;
    });
    document.getElementById('diarySectionSel').innerHTML = html;
}
</script>
@endsection
