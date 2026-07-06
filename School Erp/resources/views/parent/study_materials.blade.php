<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Study Materials — Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:#f8f7f4;color:#111827;display:flex;min-height:100vh;}
        .sidebar{width:220px;background:#1a1f3c;color:#fff;padding:20px 14px;display:flex;flex-direction:column;}
        .main{flex:1;padding:28px;}
        .card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,.05);}
        .tbl{width:100%;border-collapse:collapse;margin-top:16px;}
        .tbl th,.tbl td{padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:13.5px;}
        .tbl th{background:#f8fafc;font-weight:700;color:#475569;}
        .btn{padding:6px 14px;border-radius:8px;text-decoration:none;font-weight:700;font-size:12.5px;display:inline-flex;align-items:center;gap:6px;background:#059669;color:#fff;}
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 style="font-family:'Plus Jakarta Sans';margin-bottom:20px;color:#f59e0b;"><i class="fas fa-graduation-cap"></i> Student Portal</h3>
        <a href="{{ route('parent.dashboard') }}" style="color:#fff;text-decoration:none;padding:10px;margin-bottom:8px;display:block;font-weight:600;"><i class="fas fa-arrow-left me-2"></i> Dashboard</a>
        <a href="{{ route('parent.assignments.index') }}" style="color:rgba(255,255,255,.8);text-decoration:none;padding:10px;display:block;font-weight:600;"><i class="fas fa-tasks me-2"></i> Homework & Assignments</a>
        <a href="{{ route('parent.study-materials.index') }}" style="color:#f59e0b;text-decoration:none;padding:10px;display:block;font-weight:700;background:rgba(255,255,255,.08);border-radius:8px;margin-top:4px;"><i class="fas fa-book-open me-2"></i> Study Materials</a>
    </div>
    <div class="main">
        <h2 style="font-family:'Plus Jakarta Sans';font-size:22px;font-weight:800;margin-bottom:6px;">Class Study Materials</h2>
        <p style="color:#6b7280;font-size:13px;margin-bottom:24px;">Download chapter notes, slides, and syllabus documents uploaded by your teachers</p>

        <div class="card">
            @if($materials->count() > 0)
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Resource Title</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Uploaded Date</th>
                            <th>Download Resource</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $m)
                            <tr>
                                <td style="font-weight:700;">{{ $m->title }}</td>
                                <td>{{ $m->subject?->name ?? 'General' }}</td>
                                <td>{{ $m->teacher?->full_name }}</td>
                                <td>{{ date('M d, Y', strtotime($m->created_at)) }}</td>
                                <td>
                                    @if($m->file_path)
                                        <a href="{{ Storage::disk('public')->url($m->file_path) }}" target="_blank" class="btn"><i class="fas fa-download"></i> Download Document</a>
                                    @else
                                        <span style="color:#9ca3af;">No file</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align:center;padding:40px;color:#6b7280;">
                    <i class="fas fa-folder-open" style="font-size:40px;color:#d1d5db;margin-bottom:12px;"></i>
                    <h4>No Study Materials Shared Yet</h4>
                    <p style="font-size:13px;margin-top:4px;">No study materials or notes have been uploaded for your class yet.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
