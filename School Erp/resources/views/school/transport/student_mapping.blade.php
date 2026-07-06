@extends('layouts.app')

@section('page-title', 'Student Route Mapping')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-user-tag" style="color:var(--gold);margin-right:8px;"></i>Student Route Mapping</h1>
        <p>Assign transport routes, monthly schedules, pick-up stops, and drop-off vehicles to students</p>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-body" style="padding: 20px;">
        <form method="GET" action="{{ route('school.transport.student-mapping') }}" style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end;">
            <div class="form-group" style="margin:0; flex:1; min-width:180px;">
                <label class="form-label" style="margin-bottom:6px;">Filter by Class</label>
                <select name="class_id" class="form-control">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="margin:0; flex:1; min-width:180px;">
                <label class="form-label" style="margin-bottom:6px;">Filter by Section</label>
                <select name="section_id" class="form-control">
                    <option value="">All Sections</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->id }}" {{ request('section_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin:0; flex:1.5; min-width:220px;">
                <label class="form-label" style="margin-bottom:6px;">Search Student</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, roll, or admission ID..." value="{{ request('search') }}">
            </div>

            <button type="submit" class="btn btn-gold" style="padding:10px 20px;">
                <i class="fa fa-filter"></i> Apply Filters
            </button>
            <a href="{{ route('school.transport.student-mapping') }}" class="btn btn-outline" style="padding:10px 20px;">
                Reset
            </a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Students Transport Registry</h3>
        <span style="font-size:12.5px; font-weight:700; color:#8b5cf6; background:#f5f3ff; padding:4px 10px; border-radius:12px;">Showing {{ $students->count() }} of {{ $students->total() }} Students</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="fee-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding:12px 18px;">Adm No.</th>
                        <th style="padding:12px 18px;">Student Name</th>
                        <th style="padding:12px 18px;">Class/Sec</th>
                        <th style="padding:12px 18px;">Transport Month</th>
                        <th style="padding:12px 18px;">Route & Stop Details</th>
                        <th style="padding:12px 18px;">Buses (Pick / Drop)</th>
                        <th style="padding:12px 18px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $stud)
                        @php
                            $hasTransport = !empty($stud->transport_route) || !empty($stud->transport_vehicle_code) || !empty($stud->transport_stop) || !empty($stud->transport_drop_vehicle_code);
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:14px 18px; font-weight:700; color:#64748b;">{{ $stud->admission_number }}</td>
                            <td style="padding:14px 18px;">
                                <div style="font-weight:700; color:#1e293b;">{{ $stud->full_name }}</div>
                                <small style="color:#64748b;">Roll: {{ $stud->roll_number ?: '—' }}</small>
                            </td>
                            <td style="padding:14px 18px; font-weight:600; color:#475569;">{{ $stud->class?->name }} - {{ $stud->section?->name }}</td>
                            <td style="padding:14px 18px;">{{ $stud->transport_month ?: '—' }}</td>
                            <td style="padding:14px 18px;">
                                @if($hasTransport)
                                    <div style="font-weight:600; color:#15803d;"><i class="fas fa-route" style="margin-right:4px;"></i> {{ $stud->transport_route ?: '—' }}</div>
                                    <small style="color:#64748b;"><i class="fas fa-map-marker-alt" style="margin-right:4px;"></i> Stop: {{ $stud->transport_stop ?: '—' }}</small>
                                @else
                                    <span style="color:#94a3b8; font-size:12.5px; font-style:italic;">No transport opted</span>
                                @endif
                            </td>
                            <td style="padding:14px 18px;">
                                @if($hasTransport)
                                    <div><small style="background:#e0f2fe; color:#0369a1; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">PICK</small> <span style="font-weight:600; color:#334155;">{{ $stud->transport_vehicle_code ?: '—' }}</span></div>
                                    <div style="margin-top:4px;"><small style="background:#fef3c7; color:#d97706; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">DROP</small> <span style="font-weight:600; color:#334155;">{{ $stud->transport_drop_vehicle_code ?: '—' }}</span></div>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                            <td style="padding:14px 18px; text-align:center;">
                                <button class="btn btn-outline" style="padding:6px 12px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:4px;" onclick="openMappingModal({{ json_encode($stud) }})">
                                    <i class="fas fa-bus"></i> Assign Route
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:30px; text-align:center; color:#64748b;">
                                <i class="fas fa-user-friends" style="font-size:24px; color:#cbd5e1; margin-bottom:10px; display:block;"></i>
                                No students found. Adjust your filters or search terms.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 20px;">
            {{ $students->links() }}
        </div>
    </div>
</div>

<!-- Modal Overlay for student transport assign -->
<div id="mappingModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px);">
    <div class="card" style="max-width:500px; width:100%; border-radius:12px; overflow:hidden; background:#ffffff; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #e2e8f0;">
            <h3 id="modalTitle" style="margin:0; font-size:15px; font-weight:700;">Assign Route to Student</h3>
            <button type="button" onclick="closeMappingModal()" style="background:transparent; border:none; font-size:16px; cursor:pointer; color:#64748b;"><i class="fa fa-times"></i></button>
        </div>
        <div class="card-body" style="padding:20px;">
            <form method="POST" action="{{ route('school.transport.student-mapping') }}" id="mappingForm">
                @csrf
                <input type="hidden" name="student_id" id="modalStudentId">

                <div class="form-group">
                    <label class="form-label">Transport Month</label>
                    <input type="text" name="transport_month" id="modalMonth" class="form-control" placeholder="e.g. July 2026">
                </div>

                <div class="form-group">
                    <label class="form-label">Select Route</label>
                    <select name="transport_route" id="modalRoute" class="form-control">
                        <option value="">None (Disable Transport)</option>
                        @foreach($routes as $r)
                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Stop</label>
                    <select name="transport_stop" id="modalStop" class="form-control">
                        <option value="">None</option>
                        @foreach($stops as $s)
                            <option value="{{ $s->name }}">{{ $s->name }} (Monthly Fare: ₹{{ number_format($s->fare, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Pick Up Vehicle Code</label>
                        <select name="transport_vehicle_code" id="modalPickVehicle" class="form-control">
                            <option value="">None</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->vehicle_no }}">{{ $v->vehicle_no }} ({{ $v->driver_name ?: 'No Driver' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Drop Vehicle Code</label>
                        <select name="transport_drop_vehicle_code" id="modalDropVehicle" class="form-control">
                            <option value="">None</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->vehicle_no }}">{{ $v->vehicle_no }} ({{ $v->driver_name ?: 'No Driver' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
                    <button type="button" class="btn btn-outline" onclick="closeMappingModal()">Cancel</button>
                    <button type="submit" class="btn btn-gold"><i class="fa fa-save"></i> Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openMappingModal(student) {
        document.getElementById('modalTitle').innerText = 'Assign Route - ' + student.first_name + ' ' + (student.last_name || '');
        document.getElementById('modalStudentId').value = student.id;
        document.getElementById('modalMonth').value = student.transport_month || '';
        document.getElementById('modalRoute').value = student.transport_route || '';
        document.getElementById('modalStop').value = student.transport_stop || '';
        document.getElementById('modalPickVehicle').value = student.transport_vehicle_code || '';
        document.getElementById('modalDropVehicle').value = student.transport_drop_vehicle_code || '';
        document.getElementById('mappingModal').style.display = 'flex';
    }

    function closeMappingModal() {
        document.getElementById('mappingModal').style.display = 'none';
        document.getElementById('mappingForm').reset();
    }
</script>
@endsection
