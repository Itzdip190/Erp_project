<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Materials — Teacher Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:#f8f7f4;color:#111827;display:flex;min-height:100vh;}
        .sidebar{width:250px;background:#1a1f3c;color:#fff;padding:20px 14px;display:flex;flex-direction:column;}
        .main{flex:1;padding:28px;}
        .hdr{display:flex;align-items:center;justify-style:space-between;margin-bottom:24px;}
        .card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,.05);}
        .tbl{width:100%;border-collapse:collapse;margin-top:16px;}
        .tbl th,.tbl td{padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:13.5px;}
        .tbl th{background:#f8fafc;font-weight:700;color:#475569;}
        .btn{padding:8px 16px;border-radius:10px;text-decoration:none;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:6px;cursor:pointer;border:none;}
        .btn-blue{background:#2563eb;color:#fff;}
        .btn-red{background:#ef4444;color:#fff;}
        .badge{padding:4px 8px;border-radius:12px;font-size:11px;font-weight:700;}
        .badge-green{background:#ecfdf5;color:#059669;}
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 style="font-family:'Plus Jakarta Sans';margin-bottom:20px;color:#f59e0b;"><i class="fas fa-shield-halved"></i> Teacher Portal</h3>
        <a href="{{ route('teacher.dashboard') }}" style="color:#fff;text-decoration:none;padding:10px;margin-bottom:8px;display:block;font-weight:600;"><i class="fas fa-arrow-left me-2"></i> Dashboard</a>
        <a href="{{ route('teacher.assignments.index') }}" style="color:rgba(255,255,255,.8);text-decoration:none;padding:10px;display:block;font-weight:600;"><i class="fas fa-tasks me-2"></i> Class Assignments</a>
        <a href="{{ route('teacher.study-materials.index') }}" style="color:#f59e0b;text-decoration:none;padding:10px;display:block;font-weight:700;background:rgba(255,255,255,.08);border-radius:8px;margin-top:4px;"><i class="fas fa-book-open me-2"></i> Study Materials</a>
    </div>
    <div class="main">
        <div class="hdr">
            <div>
                <h2 style="font-family:'Plus Jakarta Sans';font-size:22px;font-weight:800;">Study Materials</h2>
                <p style="color:#6b7280;font-size:13px;">Upload class notes, slides, and study guides for your students</p>
            </div>
            <a href="{{ route('teacher.study-materials.create') }}" class="btn btn-blue"><i class="fas fa-upload"></i> Upload Study Material</a>
        </div>

        @if(session('success'))
            <div style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;padding:12px 18px;border-radius:10px;margin-bottom:20px;">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card">
            @if($materials->count() > 0)
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Resource Title</th>
                            <th>Class & Section</th>
                            <th>Subject</th>
                            <th>Uploaded Date</th>
                            <th>Download Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $m)
                            <tr>
                                <td style="font-weight:700;">{{ $m->title }}</td>
                                <td><span class="badge badge-green">{{ $m->schoolClass?->name }} - {{ $m->section?->name }}</span></td>
                                <td>{{ $m->subject?->name ?? 'General' }}</td>
                                <td>{{ date('M d, Y', strtotime($m->created_at)) }}</td>
                                <td>
                                    @if($m->file_path)
                                        <a href="{{ Storage::disk('public')->url($m->file_path) }}" target="_blank" style="color:#2563eb;font-weight:600;text-decoration:none;"><i class="fas fa-file-download"></i> Download File</a>
                                    @else
                                        <span style="color:#9ca3af;">None</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('teacher.study-materials.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this study material?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-red" style="padding:6px 12px;font-size:12px;"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top:16px;">
                    {{ $materials->links() }}
                </div>
            @else
                <div style="text-align:center;padding:40px;color:#6b7280;">
                    <i class="fas fa-folder-open" style="font-size:40px;color:#d1d5db;margin-bottom:12px;"></i>
                    <h4>No Study Materials Uploaded Yet</h4>
                    <p style="font-size:13px;margin-top:4px;">Click the upload button above to share lecture notes and syllabus resources with your students.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
