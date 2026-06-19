@extends('layouts.app')

@section('page-title', 'Assessment reports')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-chart-line" style="color:var(--gold);margin-right:8px;"></i>Assessment Analytics & Reports</h1>
        <p>Analyze performance distributions, pass/fail ratios, and subject averages across academic terms</p>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-hdr">
            <h3>Subject Performance Averages</h3>
        </div>
        <div class="card-body">
            <!-- Simulated performance bar charts -->
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div>
                    <div style="display:flex; justify-content:space-between; font-size:12.5px; font-weight:700; color:var(--navy); margin-bottom:5px;">
                        <span>Mathematics</span>
                        <span>82% average</span>
                    </div>
                    <div style="width:100%; height:12px; background:var(--border); border-radius:10px; overflow:hidden;">
                        <div style="width:82%; height:100%; background:var(--gold); border-radius:10px;"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex; justify-content:space-between; font-size:12.5px; font-weight:700; color:var(--navy); margin-bottom:5px;">
                        <span>Science</span>
                        <span>78% average</span>
                    </div>
                    <div style="width:100%; height:12px; background:var(--border); border-radius:10px; overflow:hidden;">
                        <div style="width:78%; height:100%; background:var(--navy); border-radius:10px;"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex; justify-content:space-between; font-size:12.5px; font-weight:700; color:var(--navy); margin-bottom:5px;">
                        <span>English Language</span>
                        <span>85% average</span>
                    </div>
                    <div style="width:100%; height:12px; background:var(--border); border-radius:10px; overflow:hidden;">
                        <div style="width:85%; height:100%; background:var(--green); border-radius:10px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <h3>Grade Distribution Metrics</h3>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; text-align:center;">
                <div style="background:var(--page); padding:15px; border-radius:10px; border:1px solid var(--border);">
                    <div style="font-size:24px; font-weight:800; color:var(--navy);">88.5%</div>
                    <span style="font-size:11px; color:var(--t2); text-transform:uppercase; font-weight:700;">Overall Pass Rate</span>
                </div>
                <div style="background:var(--page); padding:15px; border-radius:10px; border:1px solid var(--border);">
                    <div style="font-size:24px; font-weight:800; color:var(--gold);">A+ / A</div>
                    <span style="font-size:11px; color:var(--t2); text-transform:uppercase; font-weight:700;">Majority Grade Bracket</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
