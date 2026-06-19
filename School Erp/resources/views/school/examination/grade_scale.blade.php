@extends('layouts.app')

@section('page-title', 'Grade Scale Manager')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-percent" style="color:var(--gold);margin-right:8px;"></i>Grade Scale Manager</h1>
        <p>Define percentage boundaries and marks brackets for grading categories</p>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-hdr">
            <h3>Add Grade Rule</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.examination.grade-scale') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Grade Key</label>
                    <input type="text" class="form-control" name="grade_key" required placeholder="e.g. A+">
                </div>
                <div class="form-group">
                    <label class="form-label">Min Percentage Required (%)</label>
                    <input type="number" class="form-control" name="min_percentage" required value="90">
                </div>
                <div class="form-group">
                    <label class="form-label">Description / Remarks</label>
                    <input type="text" class="form-control" name="description" placeholder="e.g. Outstanding performance">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus"></i> Save Grade Rule
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <h3>Current Grading System (CBSE / Custom)</h3>
        </div>
        <div class="card-body">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Min Score %</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>A+</strong></td>
                        <td>90%</td>
                        <td style="color:var(--t2);">Outstanding</td>
                    </tr>
                    <tr>
                        <td><strong>A</strong></td>
                        <td>80%</td>
                        <td style="color:var(--t2);">Excellent</td>
                    </tr>
                    <tr>
                        <td><strong>B</strong></td>
                        <td>70%</td>
                        <td style="color:var(--t2);">Very Good</td>
                    </tr>
                    <tr>
                        <td><strong>C</strong></td>
                        <td>60%</td>
                        <td style="color:var(--t2);">Good</td>
                    </tr>
                    <tr>
                        <td><strong>D</strong></td>
                        <td>50%</td>
                        <td style="color:var(--t2);">Pass</td>
                    </tr>
                    <tr>
                        <td><strong style="color:var(--red);">F</strong></td>
                        <td>0%</td>
                        <td style="color:var(--red);">Fail</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
