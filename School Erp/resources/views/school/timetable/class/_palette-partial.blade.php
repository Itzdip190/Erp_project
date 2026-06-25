<div class="ctt-palette-wrap">
    <div class="ctt-palette-top">
        <span class="ctt-palette-info">Drag and drop subjects on the table to the below to build timetable</span>
        <button class="ctt-assign-btn" onclick="openBulkAssignModal()">
            <i class="fas fa-user-plus"></i> ASSIGN TEACHERS
        </button>
    </div>

    <div class="ctt-chips-row" id="subject-palette">
        @foreach($subjects as $sub)
            <div class="ctt-subject-chip {{ $sub->assigned_teacher ? '' : 'ctt-chip-unassigned' }}"
                 id="chip-{{ $sub->id }}"
                 data-subject-id="{{ $sub->id }}"
                 data-subject-name="{{ $sub->name }}"
                 draggable="true">
                <span class="ctt-chip-name">
                    {{ $sub->name }}
                    @if(!$sub->assigned_teacher)
                        <span class="ctt-chip-unassigned-mark"> *</span>
                    @endif
                </span>
                <button class="ctt-chip-assign-btn"
                        onclick="event.stopPropagation(); openQuickAssign({{ $sub->id }}, '{{ addslashes($sub->name) }}')"
                        title="{{ $sub->assigned_teacher ? 'Teacher: '.($sub->assigned_teacher->full_name ?? '') : 'Assign teacher' }}">
                    <i class="fas fa-user{{ $sub->assigned_teacher ? '' : '-plus' }}"></i>
                </button>
            </div>
        @endforeach

        @if($subjects->isEmpty())
            <div style="color:#94a3b8; font-size:13px; font-style:italic; padding:8px 0;">
                No subjects assigned to this class yet.
            </div>
        @endif
    </div>

    @php $unassignedCount = $subjects->filter(fn($s) => !$s->assigned_teacher)->count(); @endphp
    @if($unassignedCount > 0)
        <p class="ctt-unassigned-warning">
            * No teacher has been assigned for this subject. Kindly assign teacher first.
        </p>
    @endif
</div>
