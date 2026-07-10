@extends('layouts.app')

@section('page-title', 'Event & Holiday Management')

@section('content')
<style>
    /* Premium Blue & White Theme Scoped Styles */
    .events-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding: 4px 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }
    .stat-card-custom {
        background: #ffffff;
        border: 1px solid rgba(59, 130, 246, 0.15);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(59, 130, 246, 0.06);
    }
    .stat-icon-custom {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .stat-icon-custom.blue {
        background: rgba(59, 130, 246, 0.08);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.15);
    }
    .stat-icon-custom.indigo {
        background: rgba(99, 102, 241, 0.08);
        color: #4f46e5;
        border: 1px solid rgba(99, 102, 241, 0.15);
    }
    .stat-icon-custom.red {
        background: rgba(239, 68, 68, 0.08);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.15);
    }
    .stat-info-custom h4 {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 2px 0;
    }
    .stat-info-custom p {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Redesigned Grid split */
    .content-split-grid {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 24px;
    }
    @media (max-width: 1024px) {
        .content-split-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Cards styling */
    .card-custom {
        background: #ffffff;
        border: 1px solid rgba(59, 130, 246, 0.12);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .card-hdr-custom {
        padding: 18px 20px;
        border-bottom: 1px solid rgba(59, 130, 246, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.02), rgba(59, 130, 246, 0));
    }
    .card-hdr-custom h3 {
        font-size: 15px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .card-body-custom {
        padding: 20px;
    }

    /* Forms */
    .form-group-custom {
        margin-bottom: 16px;
    }
    .form-group-custom label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-control-custom {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        font-family: inherit;
        color: #1e293b;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control-custom:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }
    .form-control-custom::placeholder {
        color: #94a3b8;
    }

    /* Buttons */
    .btn-custom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        outline: none;
    }
    .btn-custom-blue {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .btn-custom-blue:hover {
        opacity: 0.95;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
    }
    .btn-custom-outline-blue {
        background: transparent;
        border: 1.5px solid #2563eb;
        color: #2563eb;
    }
    .btn-custom-outline-blue:hover {
        background: rgba(37, 99, 235, 0.05);
    }
    .btn-custom-danger-light {
        background: rgba(239, 68, 68, 0.08);
        color: #dc2626;
    }
    .btn-custom-danger-light:hover {
        background: rgba(239, 68, 68, 0.15);
    }
    .btn-custom-sm {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 8px;
    }

    /* Bulk Upload Box styling */
    .bulk-box {
        background: rgba(59, 130, 246, 0.03);
        border: 2px dashed rgba(59, 130, 246, 0.25);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        transition: border-color 0.2s;
    }
    .bulk-box:hover {
        border-color: rgba(59, 130, 246, 0.5);
    }

    /* Filters bar */
    .filters-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
        padding: 0 20px;
    }
    .filter-pills {
        display: flex;
        gap: 8px;
    }
    .filter-pill {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .filter-pill:hover {
        background: #e2e8f0;
    }
    .filter-pill.active {
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
        border-color: rgba(37, 99, 235, 0.2);
    }
    .search-wrapper-custom {
        position: relative;
        width: 280px;
    }
    .search-wrapper-custom i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
    }
    .search-control-custom {
        width: 100%;
        padding: 7px 12px 7px 32px;
        border: 1.5px solid #cbd5e1;
        border-radius: 20px;
        font-size: 12.5px;
        outline: none;
        transition: border-color 0.2s;
    }
    .search-control-custom:focus {
        border-color: #3b82f6;
    }

    /* Table redesign */
    .tbl-custom {
        width: 100%;
        border-collapse: collapse;
    }
    .tbl-custom th {
        background: #f8fafc;
        padding: 14px 20px;
        font-size: 12px;
        font-weight: 800;
        color: #475569;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .tbl-custom td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #334155;
    }
    .tbl-custom tbody tr {
        transition: background 0.15s;
    }
    .tbl-custom tbody tr:hover {
        background: #f8fafc;
    }

    /* Badges */
    .badge-custom {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }
    .badge-custom.blue {
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
        border: 1px solid rgba(37, 99, 235, 0.15);
    }
    .badge-custom.red {
        background: rgba(239, 68, 68, 0.08);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.15);
    }

    /* Actions buttons */
    .actions-wrap {
        display: flex;
        gap: 6px;
    }
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        transition: all 0.2s;
    }
    .btn-action-icon:hover {
        color: #2563eb;
        border-color: rgba(37, 99, 235, 0.3);
        background: rgba(37, 99, 235, 0.05);
    }
    .btn-action-icon.delete:hover {
        color: #dc2626;
        border-color: rgba(239, 68, 68, 0.3);
        background: rgba(239, 68, 68, 0.05);
    }

    /* Modals styling */
    .modal-backdrop-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.3);
        backdrop-filter: blur(4px);
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
    .modal-backdrop-custom.show {
        opacity: 1;
        pointer-events: auto;
    }
    .modal-content-custom {
        background: #ffffff;
        border-radius: 16px;
        width: 480px;
        max-width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: scale(0.9);
        transition: transform 0.25s ease;
        overflow: hidden;
    }
    .modal-backdrop-custom.show .modal-content-custom {
        transform: scale(1);
    }
    .modal-hdr-custom {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-hdr-custom h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #1e293b;
    }
    .modal-close-custom {
        background: none;
        border: none;
        font-size: 16px;
        color: #94a3b8;
        cursor: pointer;
        transition: color 0.15s;
    }
    .modal-close-custom:hover {
        color: #475569;
    }
    .modal-body-custom {
        padding: 20px;
    }
</style>

<div class="events-container">
    <!-- Page title -->
    <div class="page-hdr" style="margin-bottom:0;">
        <div class="page-hdr-left">
            <h1><i class="fas fa-calendar-alt" style="color:#2563eb;margin-right:8px;"></i>Event & Holiday Management</h1>
            <p>Schedule academic events, register national/state holidays, and post school calendars.</p>
        </div>
    </div>

    <!-- Quick Stats Cards Row -->
    <div class="stats-grid">
        @php
            $totalEvents = $events->count();
            $holidaysCount = $events->where('is_holiday', true)->count();
            $activeEventsCount = $totalEvents - $holidaysCount;
        @endphp
        <div class="stat-card-custom">
            <div class="stat-icon-custom blue">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info-custom">
                <h4>{{ $totalEvents }}</h4>
                <p>Total Scheduled Items</p>
            </div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-icon-custom indigo">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="stat-info-custom">
                <h4>{{ $activeEventsCount }}</h4>
                <p>Active Academic Events</p>
            </div>
        </div>
        <div class="stat-card-custom">
            <div class="stat-icon-custom red">
                <i class="fas fa-umbrella-beach"></i>
            </div>
            <div class="stat-info-custom">
                <h4>{{ $holidaysCount }}</h4>
                <p>Official School Holidays</p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="content-split-grid">
        <!-- Sidebar forms (Left) -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            <!-- Creator Form -->
            <div class="card-custom">
                <div class="card-hdr-custom">
                    <h3><i class="fas fa-plus-circle" style="color:#2563eb;"></i>Add Event / Holiday</h3>
                </div>
                <div class="card-body-custom">
                    <form method="POST" action="{{ route('school.events.index') }}">
                        @csrf
                        <div class="form-group-custom">
                            <label>Event Title *</label>
                            <input type="text" name="title" class="form-control-custom" placeholder="e.g. Independence Day, Annual Meet" required>
                        </div>
                        <div class="form-group-custom">
                            <label>Description</label>
                            <textarea name="description" class="form-control-custom" style="height:76px; resize:none;" placeholder="Brief details about the event..."></textarea>
                        </div>
                        <div class="form-group-custom">
                            <label>Start Date *</label>
                            <input type="date" name="start_date" class="form-control-custom" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group-custom">
                            <label>End Date *</label>
                            <input type="date" name="end_date" class="form-control-custom" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group-custom" style="display:flex; align-items:center; gap:8px; margin-top:12px; margin-bottom:20px;">
                            <input type="checkbox" name="is_holiday" value="1" id="isHolidayCheck" style="width:16px; height:16px; accent-color:#2563eb; cursor:pointer;">
                            <label for="isHolidayCheck" style="margin-bottom:0; cursor:pointer; font-weight:600; font-size:12.5px;">Official School Holiday (School Closed)</label>
                        </div>

                        <button type="submit" class="btn-custom btn-custom-blue" style="width:100%;">
                            <i class="fas fa-calendar-plus"></i> Save Event
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bulk upload block -->
            <div class="card-custom">
                <div class="card-hdr-custom">
                    <h3><i class="fas fa-file-excel" style="color:#10b981;"></i>Bulk Event Import</h3>
                </div>
                <div class="card-body-custom">
                    <p style="font-size:12px; color:#64748b; margin-bottom:14px; line-height:1.45;">Upload a CSV or Excel template to import multiple academic events in seconds.</p>
                    
                    <form method="POST" action="{{ route('school.events.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="bulk-box" onclick="document.getElementById('import_file').click();" style="cursor:pointer;">
                            <i class="fas fa-cloud-arrow-up" style="font-size:24px; color:#2563eb; margin-bottom:8px;"></i>
                            <div style="font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Click to choose spreadsheet</div>
                            <div style="font-size:10px; color:#94a3b8; margin-bottom:10px;">Supports CSV, XLSX, XLS</div>
                            <input type="file" id="import_file" name="import_file" accept=".csv, .xlsx, .xls" required style="display:none;" onchange="document.getElementById('file-chosen-name').textContent = this.files[0] ? this.files[0].name : '';">
                            <div id="file-chosen-name" style="font-size:11px; font-weight:700; color:#2563eb;"></div>
                        </div>

                        <div style="display:flex; flex-direction:column; gap:8px; margin-top:14px;">
                            <button type="submit" class="btn-custom btn-custom-blue btn-custom-sm">
                                <i class="fas fa-upload"></i> Process Import
                            </button>
                            <a href="{{ route('school.events.template') }}" class="btn-custom btn-custom-outline-blue btn-custom-sm" style="text-decoration:none;">
                                <i class="fas fa-download"></i> Download Demo CSV
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scheduled Directory (Right) -->
        <div class="card-custom" style="align-self:flex-start;">
            <div class="card-hdr-custom" style="padding-bottom:18px;">
                <h3><i class="fas fa-calendar-alt" style="color:#2563eb;"></i>Scheduled Events & Holidays</h3>
            </div>

            <!-- Client-side filters & Search bar -->
            <div class="filters-bar" style="margin-top: 18px;">
                <div class="filter-pills">
                    <span class="filter-pill active" onclick="filterEvents('all', this)">All Items</span>
                    <span class="filter-pill" onclick="filterEvents('event', this)">Events</span>
                    <span class="filter-pill" onclick="filterEvents('holiday', this)">Holidays</span>
                </div>
                <div class="search-wrapper-custom">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="text" id="eventSearch" class="search-control-custom" placeholder="Search event title..." onkeyup="searchTable()">
                </div>
            </div>

            <div style="padding:0; overflow-x:auto;">
                <table class="tbl-custom" id="eventsTable">
                    <thead>
                        <tr>
                            <th>Title & Description</th>
                            <th>Dates Duration</th>
                            <th>Type</th>
                            <th style="width: 100px; text-align: right; padding-right:24px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr class="event-row" data-type="{{ $event->is_holiday ? 'holiday' : 'event' }}">
                            <td>
                                <strong style="color:#1e293b; display:block; margin-bottom:2px;" class="event-title-cell">{{ $event->title }}</strong>
                                <span style="color:#64748b; font-size:12px; display:block;" class="event-desc-cell">{{ $event->description ?? 'No description provided.' }}</span>
                            </td>
                            <td>
                                <span style="font-family:monospace; font-size:11.5px; font-weight:600; color:#334155;">
                                    {{ $event->start_date }}
                                    @if($event->start_date !== $event->end_date)
                                        to {{ $event->end_date }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($event->is_holiday)
                                    <span class="badge-custom red"><i class="fas fa-umbrella-beach"></i> Holiday</span>
                                @else
                                    <span class="badge-custom blue"><i class="fas fa-graduation-cap"></i> Event</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions-wrap" style="justify-content: flex-end; padding-right:4px;">
                                    <button class="btn-action-icon" title="Edit Event" onclick="openEditModal({{ json_encode($event) }})">
                                        <i class="fas fa-pencil"></i>
                                    </button>
                                    
                                    <form method="POST" action="{{ route('school.events.delete', $event->id) }}" onsubmit="return confirm('Are you sure you want to delete this event?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon delete" title="Delete Event">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:30px; color:#94a3b8; font-weight:600;">
                                <i class="fas fa-calendar-times" style="font-size:32px; display:block; margin-bottom:10px; color:#cbd5e1;"></i>
                                No scheduled events or holidays found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Premium Edit Modal -->
<div class="modal-backdrop-custom" id="editEventModal">
    <div class="modal-content-custom">
        <div class="modal-hdr-custom">
            <h3><i class="fas fa-calendar-check" style="color:#2563eb; margin-right:6px;"></i>Edit Event / Holiday</h3>
            <button class="modal-close-custom" onclick="closeEditModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <form id="editEventForm" method="POST" action="">
            @csrf
            <div class="modal-body-custom">
                <div class="form-group-custom">
                    <label>Event Title *</label>
                    <input type="text" name="title" id="edit_title" class="form-control-custom" required>
                </div>
                <div class="form-group-custom">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" class="form-control-custom" style="height:76px; resize:none;"></textarea>
                </div>
                <div class="form-group-custom">
                    <label>Start Date *</label>
                    <input type="date" name="start_date" id="edit_start_date" class="form-control-custom" required>
                </div>
                <div class="form-group-custom">
                    <label>End Date *</label>
                    <input type="date" name="end_date" id="edit_end_date" class="form-control-custom" required>
                </div>
                <div class="form-group-custom" style="display:flex; align-items:center; gap:8px; margin-top:12px; margin-bottom:20px;">
                    <input type="checkbox" name="is_holiday" value="1" id="edit_is_holiday" style="width:16px; height:16px; accent-color:#2563eb; cursor:pointer;">
                    <label for="edit_is_holiday" style="margin-bottom:0; cursor:pointer; font-weight:600; font-size:12.5px;">Official School Holiday (School Closed)</label>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn-custom btn-custom-danger-light" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-custom btn-custom-blue">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Client-side quick filtering
    function filterEvents(type, element) {
        // Update active class on pills
        document.querySelectorAll('.filter-pill').forEach(pill => pill.classList.remove('active'));
        element.classList.add('active');

        // Show/hide rows
        document.querySelectorAll('.event-row').forEach(row => {
            const rowType = row.getAttribute('data-type');
            if (type === 'all' || rowType === type) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Client-side search
    function searchTable() {
        const query = document.getElementById('eventSearch').value.toLowerCase();
        document.querySelectorAll('.event-row').forEach(row => {
            const title = row.querySelector('.event-title-cell').textContent.toLowerCase();
            const desc = row.querySelector('.event-desc-cell').textContent.toLowerCase();
            
            if (title.includes(query) || desc.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Open Edit Modal & Populate Form
    function openEditModal(event) {
        const modal = document.getElementById('editEventModal');
        const form = document.getElementById('editEventForm');
        
        // Construct Action URL
        form.action = `/school/events/${event.id}/update`;
        
        // Pre-fill values
        document.getElementById('edit_title').value = event.title;
        document.getElementById('edit_description').value = event.description || '';
        document.getElementById('edit_start_date').value = event.start_date;
        document.getElementById('edit_end_date').value = event.end_date;
        document.getElementById('edit_is_holiday').checked = !!event.is_holiday;

        modal.classList.add('show');
    }

    // Close Modal
    function closeEditModal() {
        document.getElementById('editEventModal').classList.remove('show');
    }

    // Close on backdrop click
    document.getElementById('editEventModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
</script>
@endsection
