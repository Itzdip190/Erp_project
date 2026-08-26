@extends('layouts.app')

@section('title', 'Staff Gate Pass History')
@section('page-title', 'Staff Gate Pass History')

@section('content')

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px; background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; font-weight:600;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:20px; background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; border-radius:10px; padding:12px 16px; font-weight:600;">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<!-- Summary KPI Widgets -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa fa-ticket-alt"></i>
        </div>
        <div>
            <div style="font-size: 24px; font-weight: 800; color: #0f172a;">{{ number_format($totalCount) }}</div>
            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Staff Passes</div>
        </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa fa-walking"></i>
        </div>
        <div>
            <div style="font-size: 24px; font-weight: 800; color: #d97706;">{{ number_format($issuedCount) }}</div>
            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Currently Out</div>
        </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa fa-check-circle"></i>
        </div>
        <div>
            <div style="font-size: 24px; font-weight: 800; color: #15803d;">{{ number_format($returnedCount) }}</div>
            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Returned</div>
        </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: #fee2e2; color: #b91c1c; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa fa-ban"></i>
        </div>
        <div>
            <div style="font-size: 24px; font-weight: 800; color: #b91c1c;">{{ number_format($cancelledCount) }}</div>
            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Cancelled</div>
        </div>
    </div>

</div>

<!-- Filter & Search Card -->
<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
    <form method="GET" action="{{ route('school.gate-passes.staff.index') }}" id="filterForm">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; align-items: end;">
            
            <!-- Search -->
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 5px;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pass No, Name, Emp ID..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
            </div>

            <!-- Status -->
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 5px;">Status</label>
                <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    <option value="">All Statuses</option>
                    <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Issued / Out</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Department -->
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 5px;">Department</label>
                <select name="department_id" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Designation -->
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 5px;">Designation</label>
                <select name="designation_id" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    <option value="">All Designations</option>
                    @foreach($designations as $desig)
                        <option value="{{ $desig->id }}" {{ request('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 5px;">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
            </div>

            <!-- Date To -->
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 5px;">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
            </div>

            <!-- Filter Buttons -->
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="padding: 9px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa fa-filter"></i> Filter
                </button>
                <a href="{{ route('school.gate-passes.staff.index') }}" class="btn btn-outline" style="padding: 9px 14px; font-size: 13px; border-radius: 8px;">
                    Reset
                </a>
            </div>

        </div>
    </form>
</div>

<!-- History Table Card -->
<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">
            <i class="fa fa-list" style="color: #2563eb;"></i> Staff Outpass History
        </h3>
        <span style="font-size: 12px; color: #64748b; font-weight: 600;">
            Showing {{ $gatePasses->firstItem() ?? 0 }} to {{ $gatePasses->lastItem() ?? 0 }} of {{ $gatePasses->total() }} entries
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; text-align: left;">
                    <th style="padding: 12px 14px; font-weight: 700;">Pass Ref No</th>
                    <th style="padding: 12px 14px; font-weight: 700;">Staff Member</th>
                    <th style="padding: 12px 14px; font-weight: 700;">Date & Time</th>
                    <th style="padding: 12px 14px; font-weight: 700;">Reason for Exit</th>
                    <th style="padding: 12px 14px; font-weight: 700;">Expected Return</th>
                    <th style="padding: 12px 14px; font-weight: 700;">Status</th>
                    <th style="padding: 12px 14px; font-weight: 700; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gatePasses as $gp)
                    @php $staffMember = $gp->staff; @endphp
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                        
                        <!-- Pass Ref No -->
                        <td style="padding: 12px 14px; font-weight: 800; color: #1d4ed8;">
                            {{ $gp->gate_pass_number }}
                            <div style="font-size: 11px; color: #94a3b8; font-weight: normal; text-transform: uppercase;">{{ $gp->template }}</div>
                        </td>

                        <!-- Staff Details -->
                        <td style="padding: 12px 14px;">
                            @if($staffMember)
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 38px; height: 38px; border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0; flex-shrink: 0; background: #f8fafc;">
                                        <img src="{{ $staffMember->photo_url }}" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <a href="{{ route('school.staff.show', $staffMember->id) }}?tab=gatepass" style="font-weight: 700; color: #0f172a; text-decoration: none;">
                                            {{ $staffMember->full_name }}
                                        </a>
                                        <div style="font-size: 11.5px; color: #64748b;">
                                            Emp ID: {{ $staffMember->employee_id }} &bull; {{ optional($staffMember->designation)->name ?? 'Staff' }} ({{ optional($staffMember->department)->name ?? 'General' }})
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span style="color: #94a3b8;">Staff Record Deleted</span>
                            @endif
                        </td>

                        <!-- Date & Time -->
                        <td style="padding: 12px 14px; color: #1e293b; font-weight: 600;">
                            {{ $gp->pass_date ? $gp->pass_date->format('d M Y') : 'N/A' }}
                            <div style="font-size: 11px; color: #64748b;">{{ $gp->pass_date ? $gp->pass_date->format('h:i A') : '' }}</div>
                        </td>

                        <!-- Reason -->
                        <td style="padding: 12px 14px;">
                            <span style="font-weight: 600; color: #0f172a;">{{ $gp->reason }}</span>
                            @if($gp->remarks)
                                <div style="font-size: 11px; color: #64748b; font-style: italic;">{{ Str::limit($gp->remarks, 35) }}</div>
                            @endif
                        </td>

                        <!-- Expected Return -->
                        <td style="padding: 12px 14px; color: #334155; font-size: 12.5px;">
                            {{ $gp->expected_return_time ?: '—' }}
                        </td>

                        <!-- Status Badge -->
                        <td style="padding: 12px 14px;">
                            {!! $gp->status_badge !!}
                        </td>

                        <!-- Actions -->
                        <td style="padding: 12px 14px; text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                
                                @if($staffMember)
                                    <!-- View Modal -->
                                    <button type="button" class="btn btn-outline" style="padding: 5px 9px; font-size: 11px; border-radius: 6px;" onclick="openHistoryPreviewModal('{{ route('school.staff.gate-passes.pdf', [$staffMember->id, $gp->id]) }}', '{{ $gp->gate_pass_number }}')" title="View Pass">
                                        <i class="fa fa-eye"></i>
                                    </button>

                                    <!-- PDF -->
                                    <a href="{{ route('school.staff.gate-passes.pdf', [$staffMember->id, $gp->id]) }}" target="_blank" class="btn btn-outline" style="padding: 5px 9px; font-size: 11px; border-radius: 6px; color: #dc2626;" title="Download PDF">
                                        <i class="fa fa-file-pdf"></i>
                                    </a>

                                    <!-- Print -->
                                    <a href="{{ route('school.staff.gate-passes.print', [$staffMember->id, $gp->id]) }}" target="_blank" class="btn btn-outline" style="padding: 5px 9px; font-size: 11px; border-radius: 6px; color: #2563eb;" title="Print Slip">
                                        <i class="fa fa-print"></i>
                                    </a>

                                    @if($gp->status === 'issued')
                                        <!-- Mark Returned -->
                                        <form action="{{ route('school.staff.gate-passes.status', [$staffMember->id, $gp->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark this gate pass as Returned?');">
                                            @csrf
                                            <input type="hidden" name="status" value="returned">
                                            <button type="submit" class="btn btn-outline" style="padding: 5px 9px; font-size: 11px; border-radius: 6px; color: #16a34a; background:#f0fdf4; border-color:#bbf7d0;" title="Mark Returned">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>

                                        <!-- Cancel Pass Modal Trigger -->
                                        <button type="button" class="btn btn-outline" style="padding: 5px 9px; font-size: 11px; border-radius: 6px; color: #dc2626; background:#fef2f2; border-color:#fecaca;" onclick="openCancelModal('staff', '{{ $gp->id }}', '{{ $gp->gate_pass_number }}')" title="Cancel Pass">
                                            <i class="fa fa-ban"></i> Cancel
                                        </button>
                                    @endif
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px 12px; color: #94a3b8;">
                            <div style="font-size: 36px; margin-bottom: 10px;"><i class="fa fa-ticket-alt"></i></div>
                            <div style="font-size: 15px; font-weight: 700; color: #64748b;">No Staff Gate Passes Found</div>
                            <div style="font-size: 12px; margin-top: 4px;">Try changing the filter options or generate a gate pass from Staff 360° Profile.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $gatePasses->links() }}
    </div>

</div>

<!-- Cancellation Reason Modal -->
<div id="cancelModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#ffffff; border-radius:14px; width:90%; max-width:480px; padding:24px; box-shadow:0 20px 50px rgba(0,0,0,0.25);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">
            <h3 style="margin:0; font-size:16px; font-weight:800; color:#dc2626;">
                <i class="fa fa-ban"></i> Cancel Staff Gate Pass #<span id="cancelModalPassNo"></span>
            </h3>
            <button onclick="closeCancelModal()" style="border:none; background:transparent; font-size:18px; color:#64748b; cursor:pointer;"><i class="fa fa-times"></i></button>
        </div>

        <form id="cancelPassForm" method="POST" action="">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:6px;">Cancellation Reason <span style="color:#ef4444;">*</span></label>
                <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Please specify why this staff gate pass is being cancelled..." required style="width:100%; padding:10px; border:1.5px solid #cbd5e1; border-radius:8px; font-size:13px; box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:14px;">
                <button type="button" class="btn btn-outline" onclick="closeCancelModal()">Go Back</button>
                <button type="submit" class="btn btn-primary" style="background:#dc2626; border-color:#dc2626;">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="historyPreviewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(5px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#ffffff; border-radius:14px; width:90%; max-width:850px; height:88vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.3); overflow:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; background:#0f172a; color:#ffffff;">
            <div style="font-size:15px; font-weight:800; display:flex; align-items:center; gap:8px;">
                <i class="fa fa-ticket-alt" style="color:#60a5fa;"></i> Staff Gate Pass: <span id="historyPreviewModalTitle" style="color:#93c5fd;"></span>
            </div>
            <button onclick="closeHistoryPreviewModal()" style="background:transparent; border:none; color:#ffffff; font-size:18px; cursor:pointer;"><i class="fa fa-times"></i></button>
        </div>
        <div style="flex-grow:1; background:#f1f5f9;">
            <iframe id="historyPreviewIframe" src="" style="width:100%; height:100%; border:none;"></iframe>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openCancelModal(type, id, passNo) {
        document.getElementById('cancelModalPassNo').textContent = passNo;
        const form = document.getElementById('cancelPassForm');
        form.action = `/school/gate-passes/staff/${id}/cancel`;
        document.getElementById('cancelModal').style.display = 'flex';
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }

    function openHistoryPreviewModal(pdfUrl, passNo) {
        document.getElementById('historyPreviewModalTitle').textContent = passNo;
        document.getElementById('historyPreviewIframe').src = pdfUrl;
        document.getElementById('historyPreviewModal').style.display = 'flex';
    }

    function closeHistoryPreviewModal() {
        document.getElementById('historyPreviewIframe').src = '';
        document.getElementById('historyPreviewModal').style.display = 'none';
    }
</script>
@endsection
