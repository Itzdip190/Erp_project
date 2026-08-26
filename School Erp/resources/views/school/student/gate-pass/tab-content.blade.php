{{-- Student 360 Gate Pass Management Tab Content --}}
<div id="tab-gatepass" class="tab-content glass-card" style="display: none;">
    
    <!-- Tab Header & Quick Action -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 1.5rem;">
        <div>
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.3rem; color: var(--accent); display: flex; align-items: center; gap: 8px; margin: 0;">
                <i class="fa fa-ticket-alt"></i> Gate Pass Management
            </h3>
            <p style="font-size: 12.5px; color: var(--text-muted); margin: 3px 0 0 0;">
                Issue and track early departure outpasses and visitor permissions.
            </p>
        </div>

        <a href="{{ route('school.students.gate-passes.create', $student->id) }}" class="btn-accent" style="background-color: var(--accent); color: white; padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(var(--accent-rgb), 0.25);">
            <i class="fa fa-plus-circle"></i> Issue New Gate Pass
        </a>
    </div>

    <!-- Metrics Cards Summary -->
    @php
        $totalPasses = $gatePasses->count();
        $activePasses = $gatePasses->where('status', 'issued')->count();
        $returnedPasses = $gatePasses->where('status', 'returned')->count();
        $cancelledPasses = $gatePasses->where('status', 'cancelled')->count();
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="info-tile" style="border-left: 4px solid var(--accent); display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(var(--accent-rgb), 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-ticket-alt"></i>
            </div>
            <div>
                <span class="info-label" style="margin-bottom: 2px;">Total Passes</span>
                <span class="info-value" style="font-size: 1.25rem;">{{ $totalPasses }}</span>
            </div>
        </div>

        <div class="info-tile" style="border-left: 4px solid #10b981; display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-door-open"></i>
            </div>
            <div>
                <span class="info-label" style="margin-bottom: 2px;">Currently Out</span>
                <span class="info-value" style="font-size: 1.25rem; color: #10b981;">{{ $activePasses }}</span>
            </div>
        </div>

        <div class="info-tile" style="border-left: 4px solid #0284c7; display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(2, 132, 199, 0.1); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-check-double"></i>
            </div>
            <div>
                <span class="info-label" style="margin-bottom: 2px;">Returned</span>
                <span class="info-value" style="font-size: 1.25rem; color: #0284c7;">{{ $returnedPasses }}</span>
            </div>
        </div>

        <div class="info-tile" style="border-left: 4px solid #ef4444; display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-ban"></i>
            </div>
            <div>
                <span class="info-label" style="margin-bottom: 2px;">Cancelled</span>
                <span class="info-value" style="font-size: 1.25rem; color: #ef4444;">{{ $cancelledPasses }}</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 1.2rem; background: rgba(0,0,0,0.015); padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button type="button" onclick="filterGatePasses('all')" class="gp-filter-btn active" id="gp-flt-all" style="padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid var(--border); background: var(--accent); color: white; cursor: pointer;">
                All ({{ $totalPasses }})
            </button>
            <button type="button" onclick="filterGatePasses('issued')" class="gp-filter-btn" id="gp-flt-issued" style="padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid var(--border); background: transparent; color: var(--t1); cursor: pointer;">
                Issued ({{ $activePasses }})
            </button>
            <button type="button" onclick="filterGatePasses('returned')" class="gp-filter-btn" id="gp-flt-returned" style="padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid var(--border); background: transparent; color: var(--t1); cursor: pointer;">
                Returned ({{ $returnedPasses }})
            </button>
            <button type="button" onclick="filterGatePasses('cancelled')" class="gp-filter-btn" id="gp-flt-cancelled" style="padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid var(--border); background: transparent; color: var(--t1); cursor: pointer;">
                Cancelled ({{ $cancelledPasses }})
            </button>
        </div>

        <div style="position: relative; min-width: 220px;">
            <i class="fa fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 12px;"></i>
            <input type="text" id="gpSearchInput" onkeyup="searchGatePasses(this.value)" placeholder="Search pass no, reason..." style="width: 100%; padding: 6px 10px 6px 28px; border-radius: 8px; border: 1px solid var(--border); font-size: 12.5px; background: var(--bg-card, #fff); color: var(--t1);">
        </div>
    </div>

    <!-- Gate Pass History Table -->
    @if($gatePasses->count() > 0)
        <div style="overflow-x: auto; border: 1px solid var(--border); border-radius: 12px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--border); color: var(--text-muted); font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 12px 16px;">Pass Number</th>
                        <th style="padding: 12px 16px;">Date & Time</th>
                        <th style="padding: 12px 16px;">Reason</th>
                        <th style="padding: 12px 16px;">Guardian / Relation</th>
                        <th style="padding: 12px 16px;">Status</th>
                        <th style="padding: 12px 16px;">Issued By</th>
                        <th style="padding: 12px 16px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="gpTableBody">
                    @foreach($gatePasses as $gp)
                        <tr class="gp-row gp-status-{{ $gp->status }}" style="border-bottom: 1px solid var(--border); transition: background 0.2s;">
                            <td style="padding: 12px 16px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(29, 78, 216, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0;">
                                        <i class="fa fa-ticket-alt"></i>
                                    </div>
                                    <div>
                                        <strong style="color: var(--t1); font-family: monospace; font-size: 13.5px;">{{ $gp->gate_pass_number }}</strong>
                                        <div style="font-size: 10.5px; color: var(--text-muted); text-transform: capitalize;">{{ $gp->template }} template</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 12px 16px; color: var(--t1); white-space: nowrap;">
                                <div style="font-weight: 600;">{{ $gp->pass_date ? $gp->pass_date->format('d M Y') : 'N/A' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $gp->pass_date ? $gp->pass_date->format('h:i A') : '' }}</div>
                            </td>
                            <td style="padding: 12px 16px; max-width: 220px;">
                                <div style="font-weight: 600; color: var(--t1); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $gp->reason }}">
                                    {{ $gp->reason }}
                                </div>
                                @if($gp->expected_return_time)
                                    <div style="font-size: 11px; color: var(--accent);">
                                        <i class="fa fa-clock" style="font-size: 10px;"></i> Return: {{ $gp->expected_return_time }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 12px 16px;">
                                <div style="font-weight: 600; color: var(--t1);">{{ $gp->guardian_name }}</div>
                                <span class="badge" style="background: rgba(0,0,0,0.05); color: var(--text-muted); font-size: 10.5px; padding: 1px 6px; border-radius: 4px;">
                                    {{ $gp->guardian_relation }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px;" id="gp-status-badge-{{ $gp->id }}">
                                {!! $gp->status_badge !!}
                            </td>
                            <td style="padding: 12px 16px; color: var(--text-muted); font-size: 12px;">
                                {{ $gp->issuedByUser?->name ?? 'Admin' }}
                            </td>
                            <td style="padding: 12px 16px; text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    
                                    <!-- View Modal -->
                                    <button type="button" onclick="openGatePassModal('{{ route('school.students.gate-passes.pdf', [$student->id, $gp->id]) }}', '{{ route('school.students.gate-passes.print', [$student->id, $gp->id]) }}', '{{ $gp->gate_pass_number }}')" class="btn" style="background: rgba(29, 78, 216, 0.1); color: var(--accent); padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; border: none; cursor: pointer;" title="Preview Pass">
                                        <i class="fa fa-eye"></i> View
                                    </button>

                                    <!-- Download PDF -->
                                    <a href="{{ route('school.students.gate-passes.pdf', [$student->id, $gp->id]) }}" class="btn" style="background: #10b981; color: white; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="Download PDF">
                                        <i class="fa fa-file-pdf"></i> PDF
                                    </a>

                                    <!-- Print -->
                                    <a href="{{ route('school.students.gate-passes.print', [$student->id, $gp->id]) }}" target="_blank" class="btn" style="background: #475569; color: white; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="Print Directly">
                                        <i class="fa fa-print"></i> Print
                                    </a>

                                    <!-- Status Update Dropdown / Quick Return -->
                                    @if($gp->status === 'issued')
                                        <button type="button" onclick="updateGatePassStatus('{{ route('school.students.gate-passes.status', [$student->id, $gp->id]) }}', 'returned', {{ $gp->id }})" class="btn" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; padding: 5px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700; border: none; cursor: pointer;" title="Mark Student Returned">
                                            <i class="fa fa-check"></i> Return
                                        </button>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Empty State -->
        <div style="text-align: center; padding: 3rem 1.5rem; background: rgba(0,0,0,0.015); border-radius: 12px; border: 1px dashed var(--border);">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(29, 78, 216, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 1rem;">
                <i class="fa fa-ticket-alt"></i>
            </div>
            <h4 style="font-weight: 700; color: var(--t1); margin-bottom: 0.5rem;">No Gate Passes Issued Yet</h4>
            <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1.2rem; max-width: 420px; margin-left: auto; margin-right: auto;">
                Create early departure gate passes for {{ $student->full_name }} with auto-filled parent details and instant printable PDF.
            </p>
            <a href="{{ route('school.students.gate-passes.create', $student->id) }}" class="btn-accent" style="background-color: var(--accent); color: white; padding: 9px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa fa-plus-circle"></i> Issue First Gate Pass
            </a>
        </div>
    @endif

</div>

<!-- GATE PASS PREVIEW MODAL -->
<div id="gatePassPreviewModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 99999; align-items: center; justify-content: center; padding: 1.25rem;">
    <div style="background: var(--bg-card, #ffffff); border: 1px solid var(--border, #e2e8f0); border-radius: 16px; width: 100%; max-width: 900px; height: 88vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); overflow: hidden;">
        
        <!-- Modal Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border, #e2e8f0); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(29, 78, 216, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa fa-ticket-alt"></i>
                </div>
                <div>
                    <h4 id="gpModalTitle" style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--t1);">Gate Pass Preview</h4>
                    <p style="margin: 2px 0 0 0; font-size: 0.8rem; color: var(--text-muted);">
                        Student: <strong>{{ $student->full_name }}</strong> &bull; Adm: <strong>{{ $student->admission_number }}</strong>
                    </p>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <a id="gpModalDownloadPdf" href="#" class="btn" style="background-color: #10b981; color: white; padding: 7px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa fa-download"></i> PDF
                </a>
                <a id="gpModalPrintBtn" href="#" target="_blank" class="btn" style="background-color: #475569; color: white; padding: 7px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa fa-print"></i> Print
                </a>
                <button type="button" onclick="closeGatePassModal()" style="background: rgba(0,0,0,0.06); border: none; width: 34px; height: 34px; border-radius: 8px; color: var(--t1); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body Iframe -->
        <div style="flex: 1; background: #e2e8f0; position: relative;">
            <iframe id="gpModalIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</div>

<script>
    function filterGatePasses(status) {
        $('.gp-filter-btn').css({ background: 'transparent', color: 'var(--t1)' }).removeClass('active');
        $('#gp-flt-' + status).css({ background: 'var(--accent)', color: 'white' }).addClass('active');

        if (status === 'all') {
            $('.gp-row').show();
        } else {
            $('.gp-row').hide();
            $('.gp-status-' + status).show();
        }
    }

    function searchGatePasses(query) {
        const q = (query || '').toLowerCase().trim();
        $('.gp-row').each(function() {
            const text = $(this).text().toLowerCase();
            if (text.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function openGatePassModal(pdfUrl, printUrl, passNo) {
        $('#gpModalTitle').text('Gate Pass #' + passNo);
        $('#gpModalDownloadPdf').attr('href', pdfUrl);
        $('#gpModalPrintBtn').attr('href', printUrl);
        $('#gpModalIframe').attr('src', pdfUrl);

        $('#gatePassPreviewModal').css('display', 'flex');
        $('body').css('overflow', 'hidden');
    }

    function closeGatePassModal() {
        $('#gatePassPreviewModal').hide();
        $('#gpModalIframe').attr('src', '');
        $('body').css('overflow', '');
    }

    function updateGatePassStatus(url, status, id) {
        if (!confirm('Are you sure you want to mark this gate pass as ' + status + '?')) return;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(res) {
                if (res.success) {
                    $('#gp-status-badge-' + id).html(res.badge);
                    alert(res.message);
                    window.location.reload();
                }
            },
            error: function(err) {
                alert('Failed to update status. Please try again.');
            }
        });
    }
</script>
