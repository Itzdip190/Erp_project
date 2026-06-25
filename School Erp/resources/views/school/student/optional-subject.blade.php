@extends('layouts.app')

@section('title', 'Student Optional Subject Allocation')

@section('content')
<div class="page-hdr" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div class="page-hdr-left">
        <h1 style="font-size:24px; font-weight:800; color:var(--navy); margin:0; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-book-open" style="color:var(--gold);"></i> Student Optional Subject Allocation
        </h1>
        <p style="color:var(--t2); font-size:14px; margin:4px 0 0 0;">Student Management</p>
    </div>
</div>

<div class="card" style="background:var(--white); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow); margin-bottom:20px; padding:20px;">
    <form method="GET" action="{{ route('school.student-mgmt.optional-subject') }}" id="filter-form">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:20px; align-items:end;">
            
            <!-- Academic Year Dropdown -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:600; color:var(--t1); margin-bottom:6px; display:block;">Academic Year *</label>
                <select name="academic_session_id" class="form-control" required style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border); padding:0 12px;" onchange="this.form.submit()">
                    @foreach($academicSessions as $session)
                        <option value="{{ $session->id }}" {{ $sessionId == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Class Dropdown -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:600; color:var(--t1); margin-bottom:6px; display:block;">Select Class</label>
                <select name="class_id" id="class-select" class="form-control" required style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border); padding:0 12px;">
                    <option value="">Select Class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Section Dropdown -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:600; color:var(--t1); margin-bottom:6px; display:block;">Select Section</label>
                <select name="section_id" id="section-select" class="form-control" required style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border); padding:0 12px;" onchange="this.form.submit()">
                    <option value="">Select Section</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($classId)
            <!-- Filter by subjects (Multi-select) -->
            <div class="form-group" style="margin-top:20px; margin-bottom:0; position:relative;">
                <label class="form-label" style="font-weight:600; color:var(--t1); margin-bottom:6px; display:block;">Filter by subjects</label>
                <div id="subject-multiselect-container" style="position:relative; width:100%;">
                    <div id="subject-tags-container" style="display:flex; flex-wrap:wrap; gap:6px; min-height:38px; padding:6px 12px; border:1px solid var(--border); border-radius:6px; background:var(--white); cursor:pointer; align-items:center;">
                        <span class="placeholder-text" style="color:var(--t3);">Select Subjects</span>
                        <!-- Tags will be dynamically inserted here -->
                        <i class="fas fa-chevron-down" style="margin-left:auto; color:var(--t2); font-size:12px;"></i>
                    </div>
                    <div id="subject-dropdown-panel" style="display:none; position:absolute; top:100%; left:0; right:0; background:var(--white); border:1px solid var(--border); border-radius:6px; box-shadow:var(--shadow-lg); z-index:100; max-height:220px; overflow-y:auto; margin-top:4px; padding:8px;">
                        @forelse($subjects as $sub)
                            <div class="dropdown-item" style="padding:8px 12px; display:flex; align-items:center; gap:10px; cursor:pointer; border-radius:4px; transition:background 0.2s;" onmouseover="this.style.background='var(--page)'" onmouseout="this.style.background='transparent'" onclick="toggleSubjectOption({{ $sub->id }}, '{{ $sub->name }}')">
                                <input type="checkbox" id="chk-opt-{{ $sub->id }}" value="{{ $sub->id }}" {{ in_array($sub->id, $selectedSubjectIds) ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;" onclick="event.stopPropagation(); toggleSubjectOption({{ $sub->id }}, '{{ $sub->name }}');">
                                <span style="font-size:14px; color:var(--t1); font-weight:500;">{{ $sub->name }} ({{ $sub->code }})</span>
                            </div>
                        @empty
                            <div style="padding:10px; text-align:center; color:var(--t3); font-size:14px;">No subjects allocated to this class.</div>
                        @endforelse
                    </div>
                </div>
                <!-- Hidden inputs for form submission -->
                <input type="hidden" name="subject_ids" id="hidden-subject-ids" value="{{ implode(',', $selectedSubjectIds) }}">
            </div>
        @endif
    </form>
</div>

@if($classId && $sectionId)
    @php
        $selectedClassObj = $classes->firstWhere('id', $classId);
        $selectedSectionObj = $sections->firstWhere('id', $sectionId);
        $subjectsToDisplay = empty($selectedSubjectIds) ? $subjects : $subjects->whereIn('id', $selectedSubjectIds);
    @endphp

    <!-- Subject Summary Cards -->
    @if($subjectsToDisplay->isNotEmpty())
        <div style="display:flex; flex-wrap:wrap; gap:16px; margin: 24px 0;">
            @foreach($subjectsToDisplay as $sub)
                @php
                    $allocatedCount = $students->filter(function($st) use ($sub) {
                        return $st->optionalSubjects->contains($sub->id);
                    })->count();
                @endphp
                <div style="display:flex; border-radius:10px; overflow:hidden; box-shadow:var(--shadow); background:var(--white); border:1px solid var(--border); transition:transform 0.2s; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <div style="background:var(--purple); color:var(--white); padding:18px 22px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-users" style="font-size:22px;"></i>
                    </div>
                    <div style="padding:10px 25px; display:flex; flex-direction:column; justify-content:center; min-width:150px; background:var(--navy); color:var(--white);">
                        <span style="font-size:26px; font-weight:800; line-height:1; color:var(--white);" id="summary-count-{{ $sub->id }}">{{ $allocatedCount }}</span>
                        <span style="font-size:12px; font-weight:600; opacity:0.9; margin-top:2px;">{{ $sub->name }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card" style="background:var(--white); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow); padding:0; overflow:hidden;">
        <form method="POST" action="{{ route('school.student-mgmt.optional-subject') }}">
            @csrf
            <!-- Scoping fields -->
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="section_id" value="{{ $sectionId }}">
            <input type="hidden" name="academic_session_id" value="{{ $sessionId }}">

            <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center; padding:18px 24px; border-bottom:1px solid var(--border); background:#fff;">
                <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--navy);">
                    Subjects for {{ $selectedClassObj->name ?? '' }} - {{ $selectedSectionObj->name ?? '' }}
                </h3>
                <div style="display:flex; gap:10px;">
                    <button type="button" class="btn" style="border:1px solid var(--gold); color:var(--gold); background:var(--white); font-weight:600; padding:8px 16px; border-radius:6px; display:flex; align-items:center; gap:6px;" onclick="exportToCSV()">
                        <i class="fas fa-download"></i> DOWNLOAD
                    </button>
                    <button type="submit" class="btn btn-gold" style="font-weight:600; padding:8px 16px; border-radius:6px; display:flex; align-items:center; gap:6px;">
                        <i class="fas fa-save"></i> UPDATE
                    </button>
                </div>
            </div>

            <div class="card-body" style="padding:0; overflow-x:auto;">
                <table class="tbl" id="allocation-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:rgba(26,31,60,0.02); border-bottom:1px solid var(--border);">
                            <th style="width:100px; padding:12px 24px; text-align:left; font-weight:600; color:var(--t1);">Roll No.</th>
                            <th style="width:180px; padding:12px 24px; text-align:left; font-weight:600; color:var(--t1);">Admission ID</th>
                            <th style="padding:12px 24px; text-align:left; font-weight:600; color:var(--t1);">Name</th>
                            @if($subjectsToDisplay->isNotEmpty())
                                <th colspan="{{ $subjectsToDisplay->count() }}" style="padding:12px 24px; text-align:center; font-weight:600; color:var(--t1); border-left:1px solid var(--border);">Custom Optional Subjects</th>
                            @else
                                <th style="padding:12px 24px; text-align:center; font-weight:600; color:var(--t1); border-left:1px solid var(--border);">Custom Optional Subjects</th>
                            @endif
                        </tr>
                        @if($subjectsToDisplay->isNotEmpty())
                            <tr style="background:rgba(26,31,60,0.01); border-bottom:1px solid var(--border);">
                                <th style="border:none;"></th>
                                <th style="border:none;"></th>
                                <th style="border:none;"></th>
                                @foreach($subjectsToDisplay as $sub)
                                    <th style="padding:10px 24px; text-align:left; font-weight:600; color:var(--t2); border-left:1px solid var(--border); background:var(--white);">
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; margin:0; font-size:13px; color:var(--navy);">
                                            <input type="checkbox" class="select-all-subject" data-subject-id="{{ $sub->id }}" style="width:16px; height:16px; accent-color:var(--gold); cursor:pointer;">
                                            {{ $sub->name }}
                                        </label>
                                    </th>
                                @endforeach
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @forelse($students as $st)
                            <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.01)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:14px 24px; font-size:14px; color:var(--t2);">{{ $st->roll_number ?? sprintf('%02d', $loop->iteration) }}</td>
                                <td style="padding:14px 24px;">
                                    <span class="badge" style="background:rgba(59,130,246,0.1); color:var(--blue); font-weight:600; padding:4px 8px; border-radius:4px; font-size:12px;">
                                        {{ $st->admission_number }}
                                    </span>
                                </td>
                                <td style="padding:14px 24px; font-weight:700; color:var(--navy); font-size:14px;">{{ $st->full_name }}</td>
                                @forelse($subjectsToDisplay as $sub)
                                    @php
                                        $isAllocated = $st->optionalSubjects->contains($sub->id);
                                    @endphp
                                    <td style="padding:14px 24px; border-left:1px solid var(--border);">
                                        <input type="checkbox" 
                                               name="optional_subjects[{{ $st->id }}][{{ $sub->id }}]" 
                                               value="1" 
                                               {{ $isAllocated ? 'checked' : '' }} 
                                               class="subject-chk-{{ $sub->id }} student-checkbox" 
                                               data-student-id="{{ $st->id }}" 
                                               data-subject-id="{{ $sub->id }}"
                                               style="width:18px; height:18px; accent-color:var(--gold); cursor:pointer;">
                                    </td>
                                @empty
                                    <td style="padding:14px 24px; border-left:1px solid var(--border); color:var(--t3); text-align:center;">No subjects available</td>
                                @endforelse
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + max(1, $subjectsToDisplay->count()) }}" style="text-align:center; padding:40px; color:var(--t3); font-size:14px;">
                                    <i class="fas fa-circle-info" style="font-size:20px; color:var(--t3); margin-bottom:8px; display:block;"></i>
                                    No students registered in this class and section.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($students->isNotEmpty() && $subjectsToDisplay->isNotEmpty())
                        <tfoot>
                            <tr style="background:rgba(26,31,60,0.02); font-weight:bold; border-top:1px solid var(--border);">
                                <td colspan="3" style="padding:16px 24px; font-size:14px; color:var(--navy); font-weight:700;">Total Students</td>
                                @foreach($subjectsToDisplay as $sub)
                                    <td style="padding:16px 24px; border-left:1px solid var(--border); font-size:15px; color:var(--navy); font-weight:800;" id="total-count-{{ $sub->id }}">0</td>
                                @endforeach
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </form>
    </div>
@elseif($classId)
    <div class="card" style="background:var(--white); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow); padding:40px; text-align:center; color:var(--t2);">
        <i class="fas fa-people-group" style="font-size:32px; color:var(--t3); margin-bottom:12px; display:block;"></i>
        Please select a Section to load the student list.
    </div>
@else
    <div class="card" style="background:var(--white); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow); padding:40px; text-align:center; color:var(--t2);">
        <i class="fas fa-filter" style="font-size:32px; color:var(--t3); margin-bottom:12px; display:block;"></i>
        Please select a Class and Section above to find students and assign optional subjects.
    </div>
@endif

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Submit form on Class change
    $('#class-select').on('change', function() {
        $('#section-select').val('');
        $('#hidden-subject-ids').val('');
        $('#filter-form').submit();
    });

    @if($classId)
        // Multi-select for subjects logic
        const selectedSubjectIds = @json($selectedSubjectIds).map(String);
        const allSubjects = @json($subjects);

        function updateSubjectTags() {
            const container = $('#subject-tags-container');
            container.find('.tag-badge').remove();
            
            if (selectedSubjectIds.length === 0) {
                container.find('.placeholder-text').show();
            } else {
                container.find('.placeholder-text').hide();
                
                selectedSubjectIds.forEach(id => {
                    const subject = allSubjects.find(s => String(s.id) === String(id));
                    if (subject) {
                        const tagHtml = `
                            <div class="tag-badge" data-id="${id}" style="background:rgba(26,31,60,0.05); border:1px solid var(--border); border-radius:4px; padding:3px 8px; font-size:12px; display:flex; align-items:center; gap:6px; font-weight:600; color:var(--navy);">
                                <span>${subject.name}</span>
                                <span class="remove-tag" style="color:var(--red); cursor:pointer; font-weight:800; font-size:14px;">&times;</span>
                            </div>
                        `;
                        container.prepend(tagHtml);
                    }
                });
            }
            
            $('#hidden-subject-ids').val(selectedSubjectIds.join(','));
        }

        // Toggle dropdown display
        $('#subject-tags-container').on('click', function(e) {
            if ($(e.target).closest('.remove-tag').length > 0) return;
            $('#subject-dropdown-panel').toggle();
        });

        // Hide dropdown on click outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#subject-multiselect-container').length) {
                $('#subject-dropdown-panel').hide();
            }
        });

        // Toggle subject selection
        window.toggleSubjectOption = function(id, name) {
            const idStr = String(id);
            const index = selectedSubjectIds.indexOf(idStr);
            const chk = $('#chk-opt-' + id);
            
            if (index > -1) {
                selectedSubjectIds.splice(index, 1);
                chk.prop('checked', false);
            } else {
                selectedSubjectIds.push(idStr);
                chk.prop('checked', true);
            }
            
            updateSubjectTags();
            $('#filter-form').submit();
        };

        // Remove tag click handler
        $(document).on('click', '.remove-tag', function(e) {
            e.stopPropagation();
            const id = $(this).closest('.tag-badge').data('id');
            window.toggleSubjectOption(id, '');
        });

        // Initialize tags
        updateSubjectTags();
    @endif

    @if($classId && $sectionId && $subjectsToDisplay->isNotEmpty())
        // Recalculate checked counts
        function recalculateCounts() {
            @foreach($subjectsToDisplay as $sub)
                const subjectId = {{ $sub->id }};
                const count = $('.subject-chk-' + subjectId + ':checked').length;
                $('#total-count-' + subjectId).text(count);
                $('#summary-count-' + subjectId).text(count);
                
                // Update header check-all checkbox
                const totalCheckboxes = $('.subject-chk-' + subjectId).length;
                const selectAllChk = $('.select-all-subject[data-subject-id="' + subjectId + '"]');
                if (count === totalCheckboxes && totalCheckboxes > 0) {
                    selectAllChk.prop('checked', true);
                } else {
                    selectAllChk.prop('checked', false);
                }
            @endforeach
        }

        // Check/uncheck all students for a subject
        $('.select-all-subject').on('change', function() {
            const subjectId = $(this).data('subject-id');
            const isChecked = $(this).is(':checked');
            $('.subject-chk-' + subjectId).prop('checked', isChecked);
            recalculateCounts();
        });

        // Recalculate on individual checkbox click
        $(document).on('change', '.student-checkbox', function() {
            recalculateCounts();
        });

        // Run initially
        recalculateCounts();
    @endif
});

// CSV Export function
function exportToCSV() {
    let csv = [];
    const rows = document.querySelectorAll("#allocation-table tr");
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (let j = 0; j < cols.length; j++) {
            // Clean text content
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s+)/g, ' ').trim();
            // Escape double quotes
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        csv.push(row.join(","));        
    }
    
    // Download CSV
    const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    const downloadLink = document.createElement("a");
    downloadLink.download = "Optional_Subject_Allocations_{{ $selectedClassObj->name ?? '' }}_{{ $selectedSectionObj->name ?? '' }}.csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
@endsection
