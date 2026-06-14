<div class="table-responsive">
    <table class="custom-table" style="margin-top: 0;">
        <thead>
            <tr>
                <th style="width: 15%;">Roll No</th>
                <th style="width: 15%;">Admission No</th>
                <th style="width: 25%;">Student Name</th>
                <th style="width: 30%; text-align: center;">Attendance Status</th>
                <th style="width: 15%;">Remark / Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                @php
                    $attendance = $attendances->get($student->id);
                    $currentStatus = $attendance ? $attendance->status : 'present'; // default present
                @endphp
                <tr>
                    <td>{{ $student->roll_number }}</td>
                    <td style="font-weight: 700;">{{ $student->admission_number }}</td>
                    <td style="font-weight: 600;">{{ $student->full_name }}</td>
                    <td>
                        <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                        
                        <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                            <!-- Present -->
                            <label style="cursor: pointer; padding: 0.35rem 0.6rem; border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 700;" class="status-label-container">
                                <input type="radio" name="attendance[{{ $index }}][status]" value="present" class="status-present" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                <span style="color: var(--success);">P</span>
                            </label>
                            
                            <!-- Late -->
                            <label style="cursor: pointer; padding: 0.35rem 0.6rem; border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 700;" class="status-label-container">
                                <input type="radio" name="attendance[{{ $index }}][status]" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                                <span style="color: var(--warning);">L</span>
                            </label>

                            <!-- Absent -->
                            <label style="cursor: pointer; padding: 0.35rem 0.6rem; border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 700;" class="status-label-container">
                                <input type="radio" name="attendance[{{ $index }}][status]" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                <span style="color: var(--danger);">A</span>
                            </label>

                            <!-- Leave -->
                            <label style="cursor: pointer; padding: 0.35rem 0.6rem; border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 700;" class="status-label-container">
                                <input type="radio" name="attendance[{{ $index }}][status]" value="leave" {{ $currentStatus === 'leave' ? 'checked' : '' }}>
                                <span style="color: #60A5FA;">LV</span>
                            </label>
                            
                            <!-- Half Day -->
                            <label style="cursor: pointer; padding: 0.35rem 0.6rem; border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 700;" class="status-label-container">
                                <input type="radio" name="attendance[{{ $index }}][status]" value="half_day" {{ $currentStatus === 'half_day' ? 'checked' : '' }}>
                                <span style="color: #F472B6;">HD</span>
                            </label>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="attendance[{{ $index }}][remark]" class="form-input" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;" value="{{ $attendance?->remark }}" placeholder="Note...">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
