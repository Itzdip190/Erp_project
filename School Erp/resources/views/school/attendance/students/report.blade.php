@extends('layouts.app')

@section('page-title', 'Monthly Attendance Register')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-alt" style="color:var(--gold);margin-right:8px;"></i>Classroom Attendance Sheet</h1>
        <p>Generate and view monthly class-wise attendance register sheets</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.attendance.students.index') }}" class="btn btn-outline">
            <i class="fa fa-arrow-left"></i> Back to Marking
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-hdr">
        <h3>Select Register Parameters</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('school.attendance.students.report') }}" method="GET">
            <div class="grid-5" style="align-items:end; gap:15px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Class</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ $classId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Section</label>
                    <select name="section_id" id="section_id" class="form-control" required>
                        <option value="">Select Section</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-control" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-control" required>
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:9px 16px;">
                        <i class="fa fa-sync"></i> Load Register Sheet
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Register Output -->
@if($classId && $sectionId)
    <div class="card" style="margin-top:20px;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Monthly Attendance Sheet ({{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }})</h3>
            <span class="badge badge-purple">{{ count($students) }} Students</span>
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table class="tbl" style="font-size:12.5px; width:100%; min-width:1000px; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="width:180px; position:sticky; left:0; background:var(--page); z-index:10; border-right:1px solid var(--border);">Student Name</th>
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                <th style="text-align:center; padding:10px 4px; font-size:10px;">{{ sprintf('%02d', $day) }}</th>
                            @endfor
                            <th style="text-align:center; width:50px;">P</th>
                            <th style="text-align:center; width:50px;">A</th>
                            <th style="text-align:center; width:60px;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td style="font-weight:700; position:sticky; left:0; background:#fff; z-index:10; border-right:1px solid var(--border); color:var(--navy);">
                                    {{ $student->full_name }}
                                </td>
                                @for($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                        $status = $student->attendance_summary['days'][$day] ?? null;
                                    @endphp
                                    <td style="text-align:center; padding:10px 4px;">
                                        @if($status === 'present')
                                            <span style="color:var(--green); font-weight:900;"><i class="fas fa-check-circle"></i></span>
                                        @elseif($status === 'absent')
                                            <span style="color:var(--red); font-weight:900;"><i class="fas fa-times-circle"></i></span>
                                        @elseif($status === 'late')
                                            <span style="color:var(--gold); font-weight:900;">L</span>
                                        @elseif($status === 'leave')
                                            <span style="color:#3b82f6; font-weight:900;">LV</span>
                                        @elseif($status === 'half_day')
                                            <span style="color:#eab308; font-weight:900;">HD</span>
                                        @elseif($status === 'duty_leave')
                                            <span style="color:#ec4899; font-weight:900;">DL</span>
                                        @else
                                            <span style="color:var(--t3); font-weight:normal;">-</span>
                                        @endif
                                    </td>
                                @endfor
                                <td style="text-align:center; font-weight:700; color:var(--green);">
                                    {{ $student->attendance_summary['present'] }}
                                </td>
                                <td style="text-align:center; font-weight:700; color:var(--red);">
                                    {{ $student->attendance_summary['absent'] }}
                                </td>
                                <td style="text-align:center; font-weight:700;">
                                    <span class="badge {{ $student->attendance_summary['percentage'] >= 75 ? 'badge-green' : 'badge-red' }}">
                                        {{ $student->attendance_summary['percentage'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $daysInMonth + 4 }}" style="text-align:center; padding:30px; color:var(--t3);">No students found in this section.</td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
              </div>
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
