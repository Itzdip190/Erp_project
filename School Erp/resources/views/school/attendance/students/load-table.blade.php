<style>
    /* Premium visual overrides and styles for student attendance register */
    .status-group {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
    }
    
    .status-btn {
        position: relative;
        cursor: pointer;
        user-select: none;
    }
    
    .status-btn input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .status-btn span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid #cbd5e1;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        background-color: #fff;
        transition: all 0.2s ease;
    }
    
    .status-btn span:hover {
        border-color: #94a3b8;
        background-color: #f8fafc;
    }
    
    /* Checked States */
    .status-btn.btn-p input:checked + span {
        background-color: #10b981;
        border-color: #10b981;
        color: #fff;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
    }
    
    .status-btn.btn-hd input:checked + span {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: #fff;
        box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3);
    }
    
    .status-btn.btn-a input:checked + span {
        background-color: #ef4444;
        border-color: #ef4444;
        color: #fff;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
    }
    
    .status-btn.btn-l input:checked + span {
        background-color: #d97706;
        border-color: #d97706;
        color: #fff;
        box-shadow: 0 2px 6px rgba(217, 119, 6, 0.3);
    }
    
    .status-btn.btn-dl input:checked + span {
        background-color: #ec4899;
        border-color: #ec4899;
        color: #fff;
        box-shadow: 0 2px 6px rgba(236, 72, 153, 0.3);
    }
    
    /* View mode badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }
    .badge-status.present { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .badge-status.absent { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .badge-status.half_day { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .badge-status.leave { background-color: rgba(217, 119, 6, 0.1); color: #d97706; }
    .badge-status.duty_leave { background-color: rgba(236, 72, 153, 0.1); color: #ec4899; }
    .badge-status.not_marked { background-color: rgba(156, 163, 175, 0.1); color: #6b7280; }

    /* Header set all buttons */
    .btn-set-all {
        border: 1px solid rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 9px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-set-all:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }
</style>

<div class="edit-only-block" style="display: none; text-align: right; margin-bottom: 12px; padding: 0 16px;">
    <button type="button" class="btn-clear-all" onclick="clearAllAttendance()" style="background: transparent; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 12px; font-size: 12px; font-weight: 700; color: #ef4444; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
        <i class="fas fa-times"></i> CLEAR ALL ATTENDANCE
    </button>
</div>

<div class="table-responsive">
    <table class="custom-table" style="margin-top: 0; width: 100%;">
        <thead>
            <tr style="background-color: #023e4f; color: #fff;">
                <th style="width: 5%; text-align: center; color: #fff;">#</th>
                <th style="width: 30%; color: #fff;">Student</th>
                <th style="width: 35%; text-align: center; color: #fff;">
                    Attendance status
                    <div class="edit-only-flex" style="display: none; justify-content: center; gap: 8px; margin-top: 8px;">
                        <button type="button" class="btn-set-all" onclick="setAllStatus('present')" title="Set all to Present" style="border-color: #10b981; color: #10b981; background: #fff;">P</button>
                        <button type="button" class="btn-set-all" onclick="setAllStatus('half_day')" title="Set all to Half Day" style="border-color: #d97706; color: #d97706; background: #fff;">HD</button>
                        <button type="button" class="btn-set-all" onclick="setAllStatus('absent')" title="Set all to Absent" style="border-color: #ef4444; color: #ef4444; background: #fff;">A</button>
                        <button type="button" class="btn-set-all" onclick="setAllStatus('leave')" title="Set all to Leave" style="border-color: #b45309; color: #b45309; background: #fff;">L</button>
                        <button type="button" class="btn-set-all" onclick="setAllStatus('duty_leave')" title="Set all to Duty Leave" style="border-color: #ec4899; color: #ec4899; background: #fff;">DL</button>
                    </div>
                </th>
                <th style="width: 20%; color: #fff;">Remarks</th>
                <th style="width: 10%; text-align: center; color: #fff;">
                    <span class="view-only-inline">Attendance %</span>
                    <span class="edit-only-inline" style="display: none;">Attachment</span>
                </th>
            </tr>
        </thead>
        <tbody id="attendanceTableBody">
            @forelse($students as $index => $student)
                @php
                    $attendance = $attendances->get($student->id);
                    $currentStatus = $attendance ? $attendance->status : 'not_marked';
                    $remark = $attendance ? $attendance->remark : '';
                @endphp
                <tr data-student-id="{{ $student->id }}" data-status="{{ $currentStatus }}">
                    <td style="text-align: center; font-weight: 600; color: #475569;">
                        {{ sprintf('%02d', $index + 1) }}.
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #475569;">
                                <i class="fas fa-user" style="font-size: 14px;"></i>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="student-name" style="font-weight: 700; color: #1e293b;">{{ $student->full_name }}</span>
                                    <span title="Roll: {{ $student->roll_number ?: 'N/A' }}, Admin: {{ $student->admission_number }}" style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border-radius: 50%; background: #475569; color: #fff; font-size: 9px; font-weight: 800;">i</span>
                                </div>
                                <div class="student-roll" style="font-size: 12px; color: #64748b; font-weight: 600;">• {{ $student->admission_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <!-- View Mode Status Badge -->
                        <div class="view-only-block">
                            @if($currentStatus === 'present')
                                <span class="badge-status present">Present</span>
                            @elseif($currentStatus === 'absent')
                                <span class="badge-status absent">Absent</span>
                            @elseif($currentStatus === 'half_day')
                                <span class="badge-status half_day">Half Day</span>
                            @elseif($currentStatus === 'leave')
                                <span class="badge-status leave">Leave</span>
                            @elseif($currentStatus === 'duty_leave')
                                <span class="badge-status duty_leave">Duty Leave</span>
                            @else
                                <span class="badge-status not_marked">Not Marked</span>
                            @endif
                        </div>
                        
                        <!-- Edit Mode Status Radios -->
                        <div class="edit-only-block" style="display: none;">
                            <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                            <div class="status-group">
                                <label class="status-btn btn-p" title="Present">
                                    <input type="radio" name="attendance[{{ $index }}][status]" value="present" class="status-radio" {{ $currentStatus === 'present' ? 'checked' : '' }} required>
                                    <span>P</span>
                                </label>
                                <label class="status-btn btn-hd" title="Half Day">
                                    <input type="radio" name="attendance[{{ $index }}][status]" value="half_day" class="status-radio" {{ $currentStatus === 'half_day' ? 'checked' : '' }}>
                                    <span>HD</span>
                                </label>
                                <label class="status-btn btn-a" title="Absent">
                                    <input type="radio" name="attendance[{{ $index }}][status]" value="absent" class="status-radio" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                    <span>A</span>
                                </label>
                                <label class="status-btn btn-l" title="Leave">
                                    <input type="radio" name="attendance[{{ $index }}][status]" value="leave" class="status-radio" {{ $currentStatus === 'leave' ? 'checked' : '' }}>
                                    <span>L</span>
                                </label>
                                <label class="status-btn btn-dl" title="Duty Leave">
                                    <input type="radio" name="attendance[{{ $index }}][status]" value="duty_leave" class="status-radio" {{ $currentStatus === 'duty_leave' ? 'checked' : '' }}>
                                    <span>DL</span>
                                </label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <!-- View Mode Remark -->
                        <div class="view-only-block" style="color: #475569; font-size: 13.5px; font-weight: 500;">
                            {{ $remark ?: '—' }}
                        </div>
                        <!-- Edit Mode Remark -->
                        <div class="edit-only-block" style="display: none;">
                            <input type="text" name="attendance[{{ $index }}][remark]" class="form-control remark-input" style="height: 36px; font-size: 13px; border-radius: 6px;" placeholder="Select or type a remark" value="{{ $remark }}">
                        </div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <!-- View Mode Attendance % -->
                        <div class="view-only-block" style="font-weight: 700; color: #1e293b;">
                            {{ $student->attendance_percentage !== null ? $student->attendance_percentage . '%' : '—' }}
                        </div>
                        <!-- Edit Mode Attachment Paperclip -->
                        <div class="edit-only-block" style="display: none;">
                            <label class="attachment-btn" style="cursor: pointer; color: #d97706; font-size: 16px; transition: color 0.2s;" title="Upload Attachment">
                                <input type="file" class="attachment-file-input" style="display: none;" onchange="updateAttachmentIcon(this)">
                                <i class="fas fa-paperclip"></i>
                            </label>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-users-slash" style="font-size: 24px; display: block; margin-bottom: 8px; color: #cbd5e1;"></i>
                        No students found for this class and section.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    // Update visual count states on table render
    updateCounts();
    filterTable();
</script>
