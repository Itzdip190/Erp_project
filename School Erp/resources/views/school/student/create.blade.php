@extends('layouts.app')

@section('page-title', 'Student Admission')

@section('content')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-user-plus" style="color:var(--gold);margin-right:8px;"></i>Student Admission Wizard</h1>
        <p>Admit a new student and create parent account records</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.students.index') }}" class="btn btn-outline">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3><i class="fas fa-user-graduate" style="color:var(--gold);margin-right:6px;"></i>Admission Form</h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <div>
                    <strong><i class="fa fa-exclamation-triangle"></i> Correct the following errors:</strong>
                    <ul style="list-style-position: inside; margin-top: 6px; font-size: 12.5px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('school.students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- STEP 1: Student Information -->
            <h3 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;border-bottom:1px solid var(--border);padding-bottom:8px;margin-bottom:20px;color:var(--navy);margin-top:10px;">
                1. Student General Information
            </h3>
            
            <div style="display: flex; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap; align-items: flex-start;">
                <!-- Circular Avatar Upload -->
                <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; min-width: 150px; margin-top: 10px;">
                    <div id="avatarPreview" style="width: 120px; height: 120px; border-radius: 50%; border: 2px dashed var(--border); display: flex; align-items: center; justify-content: center; background-position: center; background-size: cover; overflow: hidden; color: var(--t3); background-color: var(--page);">
                        <i class="fa fa-user" style="font-size: 3rem; color: var(--t3);"></i>
                    </div>
                    <label class="btn btn-outline" style="font-size: 11px; padding: 6px 12px; cursor: pointer;">
                        <i class="fa fa-camera"></i> Upload Photo
                        <input type="file" name="photo" id="photoInput" style="display: none;" accept="image/*">
                    </label>
                </div>

                <!-- Main fields -->
                <div style="flex: 1;">
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">First Name <span>*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name <span>*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date of Birth <span>*</span></label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Gender <span>*</span></label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Blood Group</label>
                            <input type="text" name="blood_group" class="form-control" value="{{ old('blood_group') }}" placeholder="e.g. O+">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control" value="{{ old('religion') }}">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Caste</label>
                            <input type="text" name="caste" class="form-control" value="{{ old('caste') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Student House</label>
                            <select name="house_id" class="form-control">
                                <option value="">Select House</option>
                                @foreach($houses as $h)
                                    <option value="{{ $h->id }}" {{ old('house_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Guardian Details -->
            <h3 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;border-bottom:1px solid var(--border);padding-bottom:8px;margin-bottom:20px;color:var(--navy);margin-top:20px;">
                2. Guardian Details
            </h3>
            <div class="grid-4">
                <div class="form-group">
                    <label class="form-label">Guardian Name <span>*</span></label>
                    <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Guardian Phone <span>*</span></label>
                    <input type="text" name="guardian_phone" class="form-control" value="{{ old('guardian_phone') }}" required placeholder="10-digit number">
                </div>
                <div class="form-group">
                    <label class="form-label">Guardian Email</label>
                    <input type="email" name="guardian_email" class="form-control" value="{{ old('guardian_email') }}" placeholder="Creates parent login">
                </div>
                <div class="form-group">
                    <label class="form-label">Relationship <span>*</span></label>
                    <select name="guardian_relationship" class="form-control" required>
                        <option value="">Select Relation</option>
                        <option value="father" {{ old('guardian_relationship') === 'father' ? 'selected' : '' }}>Father</option>
                        <option value="mother" {{ old('guardian_relationship') === 'mother' ? 'selected' : '' }}>Mother</option>
                        <option value="guardian" {{ old('guardian_relationship') === 'guardian' ? 'selected' : '' }}>Other Guardian</option>
                    </select>
                </div>
            </div>
            
            <div class="grid-4">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Address <span>*</span></label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">City <span>*</span></label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">State <span>*</span></label>
                    <input type="text" name="state" class="form-control" value="{{ old('state') }}" required>
                </div>
            </div>
            <div class="grid-4">
                <div class="form-group">
                    <label class="form-label">Pincode <span>*</span></label>
                    <input type="text" name="pincode" class="form-control" value="{{ old('pincode') }}" required>
                </div>
            </div>

            <!-- STEP 3: Academic Mapping -->
            <h3 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;border-bottom:1px solid var(--border);padding-bottom:8px;margin-bottom:20px;color:var(--navy);margin-top:20px;">
                3. Academic Mapping & Finance
            </h3>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Class <span>*</span></label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ old('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Section <span>*</span></label>
                    <select name="section_id" class="form-control" required>
                        <option value="">Select Section (Select Class first)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Academic Year <span>*</span></label>
                    <select name="academic_session_id" class="form-control" required>
                        <option value="">Select Session</option>
                        @foreach($academicSessions as $ses)
                            <option value="{{ $ses->id }}" {{ old('academic_session_id', $ses->is_current ? $ses->id : '') == $ses->id ? 'selected' : '' }}>{{ $ses->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Admission Date <span>*</span></label>
                    <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Roll Number</label>
                    <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number') }}" placeholder="Auto-generated if blank">
                </div>
                <div class="form-group">
                    <label class="form-label">Opening Due Balance ($)</label>
                    <input type="number" step="0.01" name="opening_due_balance" class="form-control" value="{{ old('opening_due_balance', 0.00) }}">
                </div>
            </div>

            <div style="margin-top:30px;">
                <button type="submit" class="btn btn-gold" style="width: 100%; justify-content: center; padding: 12px; font-size: 13.5px;">
                    <i class="fa fa-save"></i> Submit Student Admission
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Preview avatar image
    $('#photoInput').on('change', function(e) {
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#avatarPreview').html('').css('background-image', 'url(' + e.target.result + ')');
        }
        reader.readAsDataURL(this.files[0]);
    });

    const allSections = @json($sections);
    const oldClassId = "{{ old('class_id') }}";
    const oldSectionId = "{{ old('section_id') }}";

    function filterSections(classId, selectedSectionId = null) {
        let sectionSelect = $('select[name="section_id"]');
        sectionSelect.empty().append('<option value="">Select Section</option>');

        if (classId) {
            let filtered = allSections.filter(s => s.class_id == classId);
            filtered.forEach(function(sec) {
                let isSelected = selectedSectionId == sec.id ? 'selected' : '';
                sectionSelect.append('<option value="' + sec.id + '" ' + isSelected + '>' + sec.name + '</option>');
            });
        }
    }

    $('select[name="class_id"]').on('change', function() {
        filterSections($(this).val());
    });

    // Run on page load for old input validation recovery
    if (oldClassId) {
        filterSections(oldClassId, oldSectionId);
    }
</script>
@endsection
