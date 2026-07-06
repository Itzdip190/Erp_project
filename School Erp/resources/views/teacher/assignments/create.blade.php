<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment — Teacher Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:#f8f7f4;color:#111827;display:flex;min-height:100vh;}
        .sidebar{width:250px;background:#1a1f3c;color:#fff;padding:20px 14px;display:flex;flex-direction:column;}
        .main{flex:1;padding:28px;}
        .card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,.05);max-width:720px;}
        .form-group{margin-bottom:20px;}
        .form-label{display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:#374151;}
        .form-control{width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:14px;outline:none;}
        .form-control:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15);}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .btn{padding:10px 20px;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;display:inline-flex;align-items:center;gap:6px;cursor:pointer;border:none;}
        .btn-blue{background:#2563eb;color:#fff;}
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 style="font-family:'Plus Jakarta Sans';margin-bottom:20px;color:#f59e0b;"><i class="fas fa-shield-halved"></i> Teacher Portal</h3>
        <a href="{{ route('teacher.assignments.index') }}" style="color:#fff;text-decoration:none;padding:10px;display:block;font-weight:600;"><i class="fas fa-arrow-left me-2"></i> Back to Assignments</a>
    </div>
    <div class="main">
        <h2 style="font-family:'Plus Jakarta Sans';font-size:22px;font-weight:800;margin-bottom:6px;">Create New Assignment</h2>
        <p style="color:#6b7280;font-size:13px;margin-bottom:20px;">Only your allocated classes, sections, and subjects are available for assignment publishing.</p>

        @if($errors->any())
            <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:12px 18px;border-radius:10px;margin-bottom:20px;">
                <ul>@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        <div class="card">
            <form action="{{ route('teacher.assignments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Target Class</label>
                        <select name="class_id" id="classSelect" class="form-control" required>
                            <option value="">Select Allocated Class</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Allocated Section</label>
                        <select name="section_id" id="sectionSelect" class="form-control" required>
                            <option value="">Select Section</option>
                            @foreach($sections as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Allocated Subject</label>
                        <select name="subject_id" id="subjectSelect" class="form-control">
                            <option value="">Select Allocated Subject</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Submission Due Date</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Assignment Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Chapter 4 Algebra Worksheet" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Detailed Instructions / Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Enter assignment instructions for students..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Attachment File (PDF, Word, Image - Max 10MB)</label>
                    <input type="file" name="attachment" class="form-control">
                </div>

                <div style="margin-top:24px;">
                    <button type="submit" class="btn btn-blue"><i class="fas fa-paper-plane"></i> Publish Assignment to Class Students</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
