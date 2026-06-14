@extends('layouts.app')

@section('title', 'Admit New Student')

@section('content')
<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-family: 'Syne', sans-serif;">Student Admission Wizard</h2>
        <a href="{{ route('school.students.index') }}" class="btn-accent" style="background-color: #4B5563;">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if ($errors->any())
        <div style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid var(--danger); color: var(--danger); padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
            <strong style="display: block; margin-bottom: 0.5rem;"><i class="fa fa-exclamation-triangle"></i> Correct the following errors:</strong>
            <ul style="list-style-position: inside;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('school.students.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- STEP 1: Student Information -->
        <h3 style="font-family: 'Syne', sans-serif; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--accent);">
            1. Student General Information
        </h3>
        
        <div style="display: flex; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <!-- Circular Avatar Upload -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem; min-width: 150px;">
                <div id="avatarPreview" style="width: 120px; height: 120px; border-radius: 50%; border: 2px dashed var(--border); display: flex; align-items: center; justify-content: center; background-position: center; background-size: cover; overflow: hidden; color: var(--text-muted);">
                    <i class="fa fa-user" style="font-size: 3rem;"></i>
                </div>
                <label class="btn-accent" style="padding: 0.5rem 1rem; font-size: 0.85rem; cursor: pointer; background-color: #4B5563;">
                    <i class="fa fa-camera"></i> Upload Photo
                    <input type="file" name="photo" id="photoInput" style="display: none;" accept="image/*">
                </label>
            </div>

            <!-- Main fields -->
            <div style="flex: 1; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">First Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="first_name" class="form-input" value="{{ old('first_name') }}" required>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Last Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="last_name" class="form-input" value="{{ old('last_name') }}" required>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Date of Birth <span style="color: var(--danger);">*</span></label>
                    <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth') }}" required>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Gender <span style="color: var(--danger);">*</span></label>
                    <select name="gender" class="form-input" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Blood Group</label>
                    <input type="text" name="blood_group" class="form-input" value="{{ old('blood_group') }}" placeholder="e.g. O+">
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Religion</label>
                    <input type="text" name="religion" class="form-input" value="{{ old('religion') }}">
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Caste</label>
                    <input type="text" name="caste" class="form-input" value="{{ old('caste') }}">
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Category</label>
                    <select name="category_id" class="form-input">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Student House</label>
                    <select name="house_id" class="form-input">
                        <option value="">Select House</option>
                        @foreach($houses as $h)
                            <option value="{{ $h->id }}" {{ old('house_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- STEP 2: Guardian Details -->
        <h3 style="font-family: 'Syne', sans-serif; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--accent); margin-top: 3rem;">
            2. Guardian Details
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Guardian Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="guardian_name" class="form-input" value="{{ old('guardian_name') }}" required>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Guardian Phone <span style="color: var(--danger);">*</span></label>
                <input type="text" name="guardian_phone" class="form-input" value="{{ old('guardian_phone') }}" required placeholder="10-digit number">
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Guardian Email</label>
                <input type="email" name="guardian_email" class="form-input" value="{{ old('guardian_email') }}" placeholder="Creates parent login account">
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Relationship <span style="color: var(--danger);">*</span></label>
                <select name="guardian_relationship" class="form-input" required>
                    <option value="">Select Relation</option>
                    <option value="father" {{ old('guardian_relationship') === 'father' ? 'selected' : '' }}>Father</option>
                    <option value="mother" {{ old('guardian_relationship') === 'mother' ? 'selected' : '' }}>Mother</option>
                    <option value="guardian" {{ old('guardian_relationship') === 'guardian' ? 'selected' : '' }}>Other Guardian</option>
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div style="grid-column: span 2;">
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Address <span style="color: var(--danger);">*</span></label>
                <input type="text" name="address" class="form-input" value="{{ old('address') }}" required>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">City <span style="color: var(--danger);">*</span></label>
                <input type="text" name="city" class="form-input" value="{{ old('city') }}" required>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">State <span style="color: var(--danger);">*</span></label>
                <input type="text" name="state" class="form-input" value="{{ old('state') }}" required>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Pincode <span style="color: var(--danger);">*</span></label>
                <input type="text" name="pincode" class="form-input" value="{{ old('pincode') }}" required>
            </div>
        </div>

        <!-- STEP 3: Academic Mapping -->
        <h3 style="font-family: 'Syne', sans-serif; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--accent); margin-top: 3rem;">
            3. Academic Mapping & Finance
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Class <span style="color: var(--danger);">*</span></label>
                <select name="class_id" class="form-input" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ old('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Section <span style="color: var(--danger);">*</span></label>
                <select name="section_id" class="form-input" required>
                    <option value="">Select Section (Select Class first)</option>
                </select>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Academic Year <span style="color: var(--danger);">*</span></label>
                <select name="academic_session_id" class="form-input" required>
                    <option value="">Select Session</option>
                    @foreach($academicSessions as $ses)
                        <option value="{{ $ses->id }}" {{ old('academic_session_id', $ses->is_current ? $ses->id : '') == $ses->id ? 'selected' : '' }}>{{ $ses->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Admission Date <span style="color: var(--danger);">*</span></label>
                <input type="date" name="admission_date" class="form-input" value="{{ old('admission_date', date('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Roll Number</label>
                <input type="text" name="roll_number" class="form-input" value="{{ old('roll_number') }}" placeholder="Auto-generated if blank">
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Opening Due Balance ($)</label>
                <input type="number" step="0.01" name="opening_due_balance" class="form-input" value="{{ old('opening_due_balance', 0.00) }}">
            </div>
        </div>

        <button type="submit" class="btn-accent" style="width: 100%; justify-content: center; padding: 1rem; font-size: 1.1rem;">
            <i class="fa fa-save"></i> Submit Student Admission
        </button>
    </form>
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

    // Client-side instant dynamic section loading without extra requests
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
