{{-- Staff 360 Gate Pass Tab Partial --}}
@php
    $totalPasses = $gatePasses->count();
    $activeOut = $gatePasses->where('status', 'issued')->count();
    $returnedCount = $gatePasses->where('status', 'returned')->count();
    $cancelledCount = $gatePasses->where('status', 'cancelled')->count();
@endphp

<div class="s360-card" style="padding: 20px;">
    
    <!-- Top Action Bar & Metrics -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h3 style="margin:0 0 4px 0; font-size:16px; font-weight:800; color:var(--t1, #0f172a); display:flex; align-items:center; gap:8px;">
                <i class="fa fa-ticket-alt" style="color:var(--accent, #2563eb);"></i> Staff Gate Pass Records
            </h3>
            <div style="font-size:12px; color:var(--text-muted, #64748b);">
                Track staff movement, official duties, early leaves, and gate outpasses.
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('school.staff.gate-passes.create', $staff->id) }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(var(--accent-rgb, 37,99,235), 0.25);">
                <i class="fa fa-plus-circle"></i> Generate Gate Pass
            </a>
        </div>
    </div>

    <!-- Metric Counter Pills -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 38px; height: 38px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                <i class="fa fa-ticket-alt"></i>
            </div>
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #0f172a;">{{ $totalPasses }}</div>
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Issued</div>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 38px; height: 38px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                <i class="fa fa-walking"></i>
            </div>
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #d97706;">{{ $activeOut }}</div>
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Currently Out</div>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 38px; height: 38px; border-radius: 8px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                <i class="fa fa-check-circle"></i>
            </div>
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #15803d;">{{ $returnedCount }}</div>
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Returned</div>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 38px; height: 38px; border-radius: 8px; background: #fee2e2; color: #b91c1c; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                <i class="fa fa-ban"></i>
            </div>
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #b91c1c;">{{ $cancelledCount }}</div>
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Cancelled</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
            <button class="btn btn-outline staff-gp-filter active" onclick="filterStaffGatePasses('all', this)" style="padding: 5px 12px; font-size: 11.5px; border-radius: 6px;">All</button>
            <button class="btn btn-outline staff-gp-filter" onclick="filterStaffGatePasses('issued', this)" style="padding: 5px 12px; font-size: 11.5px; border-radius: 6px;">Issued / Out</button>
            <button class="btn btn-outline staff-gp-filter" onclick="filterStaffGatePasses('returned', this)" style="padding: 5px 12px; font-size: 11.5px; border-radius: 6px;">Returned</button>
            <button class="btn btn-outline staff-gp-filter" onclick="filterStaffGatePasses('cancelled', this)" style="padding: 5px 12px; font-size: 11.5px; border-radius: 6px;">Cancelled</button>
        </div>

        <div style="position: relative; width: 220px;">
            <i class="fa fa-search" style="position: absolute; left: 10px; top: 10px; color: #94a3b8; font-size: 12px;"></i>
            <input type="text" id="staffGpSearchInput" onkeyup="searchStaffGatePassTable()" placeholder="Search pass no, reason..." style="width: 100%; padding: 6px 10px 6px 30px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 12px; box-sizing: border-box;">
        </div>
    </div>

    <!-- History Table -->
    <div style="overflow-x: auto;">
        <table id="staffGatePassTable" style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; text-align: left;">
                    <th style="padding: 10px 12px; font-weight: 700;">Ref No</th>
                    <th style="padding: 10px 12px; font-weight: 700;">Pass Date & Time</th>
                    <th style="padding: 10px 12px; font-weight: 700;">Reason for Exit</th>
                    <th style="padding: 10px 12px; font-weight: 700;">Expected Return</th>
                    <th style="padding: 10px 12px; font-weight: 700;">Status</th>
                    <th style="padding: 10px 12px; font-weight: 700;">Issued By</th>
                    <th style="padding: 10px 12px; font-weight: 700; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gatePasses as $gp)
                    <tr class="staff-gp-row" data-status="{{ $gp->status }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                        <td style="padding: 12px; font-weight: 800; color: #1d4ed8;">
                            {{ $gp->gate_pass_number }}
                            <div style="font-size: 10.5px; color: #94a3b8; font-weight: normal; text-transform: uppercase;">{{ $gp->template }} layout</div>
                        </td>
                        <td style="padding: 12px; color: #1e293b; font-weight: 600;">
                            {{ $gp->pass_date ? $gp->pass_date->format('d M Y') : 'N/A' }}
                            <div style="font-size: 11px; color: #64748b;">{{ $gp->pass_date ? $gp->pass_date->format('h:i A') : '' }}</div>
                        </td>
                        <td style="padding: 12px;">
                            <span style="font-weight: 600; color: #0f172a;">{{ $gp->reason }}</span>
                            @if($gp->remarks)
                                <div style="font-size: 11px; color: #64748b; font-style: italic;">{{ Str::limit($gp->remarks, 30) }}</div>
                            @endif
                        </td>
                        <td style="padding: 12px; color: #334155; font-size: 12.5px;">
                            {{ $gp->expected_return_time ?: '—' }}
                        </td>
                        <td style="padding: 12px;">
                            {!! $gp->status_badge !!}
                        </td>
                        <td style="padding: 12px; font-size: 12px; color: #475569;">
                            {{ $gp->issuedByUser?->name ?? 'Admin' }}
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <!-- View Modal -->
                                <button type="button" class="btn btn-outline" style="padding: 4px 8px; font-size: 11px; border-radius: 6px;" onclick="openStaffGatePassPreviewModal('{{ route('school.staff.gate-passes.pdf', [$staff->id, $gp->id]) }}', '{{ $gp->gate_pass_number }}')" title="View Pass">
                                    <i class="fa fa-eye"></i>
                                </button>

                                <!-- PDF Download -->
                                <a href="{{ route('school.staff.gate-passes.pdf', [$staff->id, $gp->id]) }}" target="_blank" class="btn btn-outline" style="padding: 4px 8px; font-size: 11px; border-radius: 6px; color: #dc2626;" title="PDF Download">
                                    <i class="fa fa-file-pdf"></i>
                                </a>

                                <!-- Print -->
                                <a href="{{ route('school.staff.gate-passes.print', [$staff->id, $gp->id]) }}" target="_blank" class="btn btn-outline" style="padding: 4px 8px; font-size: 11px; border-radius: 6px; color: #2563eb;" title="Direct Print">
                                    <i class="fa fa-print"></i>
                                </a>

                                <!-- Mark Returned Action -->
                                @if($gp->status === 'issued')
                                    <form action="{{ route('school.staff.gate-passes.status', [$staff->id, $gp->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark this gate pass as Returned?');">
                                        @csrf
                                        <input type="hidden" name="status" value="returned">
                                        <button type="submit" class="btn btn-outline" style="padding: 4px 8px; font-size: 11px; border-radius: 6px; color: #16a34a; background:#f0fdf4; border-color:#bbf7d0;" title="Mark Returned">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </form>

                                    <!-- Cancel Action -->
                                    <form action="{{ route('school.staff.gate-passes.status', [$staff->id, $gp->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to Cancel this gate pass?');">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-outline" style="padding: 4px 8px; font-size: 11px; border-radius: 6px; color: #dc2626; background:#fef2f2; border-color:#fecaca;" title="Cancel Pass">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 36px 12px; color: #94a3b8;">
                            <div style="font-size: 32px; margin-bottom: 8px;"><i class="fa fa-ticket-alt"></i></div>
                            <div style="font-size: 14px; font-weight: 700; color: #64748b;">No Gate Passes Issued Yet</div>
                            <div style="font-size: 12px; margin-top: 4px;">Click "Generate Gate Pass" above to issue an outpass for this staff member.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- In-Page Staff PDF Preview Modal -->
<div id="staffGpPreviewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(5px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#ffffff; border-radius:14px; width:90%; max-width:850px; height:88vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.3); overflow:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; background:#0f172a; color:#ffffff;">
            <div style="font-size:15px; font-weight:800; display:flex; align-items:center; gap:8px;">
                <i class="fa fa-ticket-alt" style="color:#60a5fa;"></i> Staff Gate Pass: <span id="staffGpPreviewModalTitle" style="color:#93c5fd;"></span>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <button onclick="closeStaffGpPreviewModal()" style="background:transparent; border:none; color:#ffffff; font-size:18px; cursor:pointer;"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div style="flex-grow:1; background:#f1f5f9;">
            <iframe id="staffGpPreviewIframe" src="" style="width:100%; height:100%; border:none;"></iframe>
        </div>
    </div>
</div>

<script>
    function openStaffGatePassPreviewModal(pdfUrl, passNo) {
        document.getElementById('staffGpPreviewModalTitle').textContent = passNo;
        document.getElementById('staffGpPreviewIframe').src = pdfUrl;
        document.getElementById('staffGpPreviewModal').style.display = 'flex';
    }

    function closeStaffGpPreviewModal() {
        document.getElementById('staffGpPreviewIframe').src = '';
        document.getElementById('staffGpPreviewModal').style.display = 'none';
    }

    function filterStaffGatePasses(status, btn) {
        document.querySelectorAll('.staff-gp-filter').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const rows = document.querySelectorAll('.staff-gp-row');
        rows.forEach(row => {
            if (status === 'all' || row.getAttribute('data-status') === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function searchStaffGatePassTable() {
        const query = document.getElementById('staffGpSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.staff-gp-row');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }
</script>
