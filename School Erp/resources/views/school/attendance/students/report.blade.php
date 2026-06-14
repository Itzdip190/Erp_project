@extends('layouts.app')

@section('title', 'Monthly Attendance Register')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="font-family: 'Syne', sans-serif;">Classroom Attendance Sheet</h2>
    <a href="{{ route('school.attendance.students.index') }}" class="btn-accent" style="background-color: #4B5563;">
        <i class="fa fa-arrow-left"></i> Back to Marking
    </a>
</div>

<!-- Filters -->
<div class="glass-card">
    <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;">Select Register Parameters</h3>
    <form action="{{ route('school.attendance.students.report') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; align-items: end;">
        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Class</label>
            <select name="class_id" id="class_id" class="form-input" required>
                <option value="">Select Class</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" {{ $classId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Section</label>
            <select name="section_id" id="section_id" class="form-input" required>
                <option value="">Select Section</option>
            </select>
        </div>

        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Month</label>
            <select name="month" class="form-input" required>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
        </div>

        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Year</label>
            <select name="year" class="form-input" required>
                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div>
            <button type="submit" class="btn-accent" style="width: 100%; justify-content: center;">
                <i class="fa fa-sync"></i> Load Register Sheet
            </button>
        </div>
    </form>
</div>

<!-- Register Output -->
@if($classId && $sectionId)
    <div class="glass-card" style="margin-top: 2rem; padding: 1rem;">
        <div class="table-responsive">
            <table class="custom-table" style="font-size: 0.85rem; width: 100%; min-width: 1000px;">
                <thead>
                    <tr>
                        <th style="width: 150px; position: sticky; left: 0; background: #0B0F19; z-index: 10;">Student Name</th>
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            <th style="text-align: center; padding: 0.5rem 0.25rem;">{{ $day }}</th>
                        @endfor
                        <th style="text-align: center; width: 60px;">P</th>
                        <th style="text-align: center; width: 60px;">A</th>
                        <th style="text-align: center; width: 60px;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td style="font-weight: 700; position: sticky; left: 0; background: #111827; z-index: 10; border-right: 1px solid var(--border);">
                                {{ $student->full_name }}
                            </td>
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $status = $student->attendance_summary['days'][$day] ?? null;
                                @endphp
                                <td style="text-align: center; padding: 0.5rem 0.1rem;">
                                    @if($status === 'present')
                                        <span style="color: var(--success); font-weight: 900;">✔</span>
                                    @elseif($status === 'absent')
                                        <span style="color: var(--danger); font-weight: 900;">✘</span>
                                    @elseif($status === 'late')
                                        <span style="color: var(--warning); font-weight: 900;">L</span>
                                    @elseif($status === 'leave')
                                        <span style="color: #60A5FA; font-weight: 900;">LV</span>
                                    @elseif($status === 'half_day')
                                        <span style="color: #F472B6; font-weight: 900;">H</span>
                                    @else
                                        <span style="color: var(--text-muted);">-</span>
                                    @endif
                                </td>
                            @endfor
                            <td style="text-align: center; font-weight: 700; color: var(--success);">
                                {{ $student->attendance_summary['present'] }}
                            </td>
                            <td style="text-align: center; font-weight: 700; color: var(--danger);">
                                {{ $student->attendance_summary['absent'] }}
                            </td>
                            <td style="text-align: center; font-weight: 700;">
                                <span class="badge {{ $student->attendance_summary['percentage'] >= 75 ? 'badge-success' : 'badge-danger' }}">
                                    {{ $student->attendance_summary['percentage'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 4 }}" style="text-align: center; padding: 2rem;">No students found in this section.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    const allSections = @json($sections);
    const initialClassId = "{{ $classId }}";
    const initialSectionId = "{{ $sectionId }}";

    function filterSections(classId, selectedSectionId = null) {
        let sectionSelect = $('#section_id');
        sectionSelect.empty().append('<option value="">Select Section</option>');

        if (classId) {
            let filtered = allSections.filter(s => s.class_id == classId);
            filtered.forEach(function(sec) {
                let isSelected = selectedSectionId == sec.id ? 'selected' : '';
                sectionSelect.append('<option value="' + sec.id + '" ' + isSelected + '>' + sec.name + '</option>');
            });
        }
    }

    $('#class_id').on('change', function() {
        filterSections($(this).val());
    });

    if (initialClassId) {
        filterSections(initialClassId, initialSectionId);
    }
</script>
@endsection
