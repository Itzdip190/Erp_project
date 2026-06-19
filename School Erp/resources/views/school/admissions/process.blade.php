@extends('layouts.app')

@section('page-title', 'Admission Process Flow')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-arrows-spin" style="color:var(--gold);margin-right:8px;"></i>Admission Process Pipeline</h1>
        <p>Define sequential checkpoints and interaction gates from enquiry to active class assignment</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>Configure Intake Workflow Phases</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.admissions.process') }}">
            @csrf
            <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:25px;">
                <div style="padding:12px; background:var(--page); border:1px solid var(--border); border-radius:9px; display:flex; align-items:center; gap:12px;">
                    <span style="width:24px; height:24px; border-radius:50%; background:var(--navy); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11px;">1</span>
                    <div><strong>Enquiry Lead Intake</strong><p style="font-size:11px; color:var(--t2);">Initial visitor form submission & follow-up logging</p></div>
                </div>
                <div style="padding:12px; background:var(--page); border:1px solid var(--border); border-radius:9px; display:flex; align-items:center; gap:12px;">
                    <span style="width:24px; height:24px; border-radius:50%; background:var(--navy); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11px;">2</span>
                    <div><strong>Application Review & Fee Payment</strong><p style="font-size:11px; color:var(--t2);">Collecting Domicile & Birth files, and application fee receipting</p></div>
                </div>
                <div style="padding:12px; background:var(--page); border:1px solid var(--border); border-radius:9px; display:flex; align-items:center; gap:12px;">
                    <span style="width:24px; height:24px; border-radius:50%; background:var(--navy); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11px;">3</span>
                    <div><strong>Interaction Interview & Exam Evaluation</strong><p style="font-size:11px; color:var(--t2);">Assessment test scheduling & reporting grades</p></div>
                </div>
                <div style="padding:12px; background:var(--page); border:1px solid var(--border); border-radius:9px; display:flex; align-items:center; gap:12px;">
                    <span style="width:24px; height:24px; border-radius:50%; background:var(--navy); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11px;">4</span>
                    <div><strong>Document Verification & Final Seat Booking</strong><p style="font-size:11px; color:var(--t2);">Generate Roll No. & convert to active Student Directory entry</p></div>
                </div>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                <i class="fas fa-save"></i> Save Workflow Pipeline Settings
            </button>
        </form>
    </div>
</div>
@endsection
