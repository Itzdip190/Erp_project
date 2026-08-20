@extends('layouts.app')

@section('title', 'Configure Payroll Settings — HR Payroll')

@section('styles')
<style>
    .sal-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px 30px;
        box-sizing: border-box;
    }
    .sal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        overflow: hidden;
    }
    .sal-card-hdr {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        padding: 18px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .sal-card-body {
        padding: 30px 36px;
    }
    .sal-form-row-3col {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }
    .sal-form-row-4col {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }
    .sal-form-row-2col {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 24px;
        align-items: center;
        margin-bottom: 36px;
    }
    .sal-form-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-top: 36px;
        flex-wrap: wrap;
    }
    .btn-submit-main {
        padding: 12px 42px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        min-width: 150px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-submit-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        color: #ffffff;
    }
    .btn-discard-main {
        padding: 12px 42px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 150px;
        box-shadow: 0 4px 14px rgba(239,68,68,0.25);
        transition: all 0.2s ease;
    }
    .btn-discard-main:hover {
        transform: translateY(-2px);
        color: #ffffff;
        box-shadow: 0 6px 18px rgba(239,68,68,0.35);
    }
    .sal-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .sal-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    /* Responsive Mobile Media Queries */
    @media (max-width: 768px) {
        .sal-container {
            padding: 14px 10px !important;
        }
        .sal-card-hdr {
            padding: 14px 16px !important;
        }
        .sal-card-body {
            padding: 20px 16px !important;
        }
        .sal-form-row-3col, .sal-form-row-4col, .sal-form-row-2col {
            gap: 16px !important;
            margin-bottom: 20px !important;
        }
    }
    @media (max-width: 540px) {
        .sal-form-row-3col, .sal-form-row-4col, .sal-form-row-2col {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
        }
        .sal-form-actions {
            flex-direction: column !important;
            width: 100% !important;
            gap: 10px !important;
        }
        .sal-form-actions .btn-submit-main,
        .sal-form-actions .btn-discard-main {
            width: 100% !important;
            min-width: 100% !important;
        }
    }

    /* Dark Mode Overrides */
    body.dark-mode .sal-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
    }
    body.dark-mode .sal-card-hdr {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
        color: #ffffff !important;
    }
    body.dark-mode .sal-card-body {
        background: #1e293b !important;
        color: #f8fafc !important;
    }
    body.dark-mode .sal-label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .sal-input {
        background: #0f172a !important;
        color: #f8fafc !important;
        border-color: #334155 !important;
    }
    body.dark-mode .sal-status-box {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
</style>
@endsection

@section('content')
<div class="sal-container">

    @if ($errors->any())
        <div style="padding: 14px 18px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(239,68,68,0.1);">
            <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> Please fill all mandatory fields correctly.
        </div>
    @endif

    <!-- Configure Form Card -->
    <div class="sal-card">
        
        <!-- Header Banner (Vibrant Blue) -->
        <div class="sal-card-hdr">
            <div style="font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 10px; letter-spacing: 0.2px;">
                <i class="fas fa-coins" style="color: #ffffff;"></i> Configure Payroll Settings
            </div>
            <a href="{{ route('school.payroll.salary-structure') }}" style="color: #ffffff; text-decoration: none; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(4px);">
                <i class="fas fa-arrow-left"></i> Back to Salary List
            </a>
        </div>

        <div class="sal-card-body">
            
            <!-- Mandatory Warning Pill -->
            <div style="text-align: center; margin-bottom: 28px;">
                <span style="color: #dc2626; font-weight: 700; font-size: 13px; background: #fef2f2; padding: 7px 20px; border-radius: 20px; border: 1px solid #fecaca; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(239,68,68,0.06);">
                    <i class="fas fa-exclamation-circle"></i> All fields are mandatory
                </span>
            </div>

            <form action="{{ route('school.payroll.salary-structure.store') }}" method="POST" id="salaryStructureForm">
                @csrf
                <input type="hidden" name="id" value="{{ $structure?->id }}">

                <!-- Row 1: Select Employee, Basic Salary, Salary Type -->
                <div class="sal-form-row-3col">
                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Select Employee <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="staff_id" id="staff_id_select" required class="sal-input">
                            <option value="">-- Select Employee --</option>
                            @foreach($activeStaff as $staff)
                                <option value="{{ $staff->id }}" 
                                    {{ (old('staff_id', $selectedStaffId) == $staff->id) ? 'selected' : '' }}>
                                    {{ $staff->employee_id ? '['.$staff->employee_id.'] ' : '' }}{{ $staff->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Basic Salary <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="basic_salary" id="basic_salary" placeholder="Enter basic salary" value="{{ old('basic_salary', $structure?->basic_salary ?? '') }}" required class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Salary Type <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="salary_type" id="salary_type" required class="sal-input">
                            <option value="">-- Select --</option>
                            <option value="Monthly" {{ old('salary_type', $structure?->salary_type ?? 'Monthly') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="Daily" {{ old('salary_type', $structure?->salary_type) == 'Daily' ? 'selected' : '' }}>Daily</option>
                            <option value="Hourly" {{ old('salary_type', $structure?->salary_type) == 'Hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="Contract" {{ old('salary_type', $structure?->salary_type) == 'Contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Allowances -->
                <div class="sal-form-row-4col">
                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            HRA <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="hra" id="hra" placeholder="Enter HRA amount" value="{{ old('hra', $structure?->hra ?? '0') }}" class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            DA <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="da" id="da" placeholder="Enter DA amount" value="{{ old('da', $structure?->da ?? '0') }}" class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            TA <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="ta" id="ta" placeholder="Enter TA amount" value="{{ old('ta', $structure?->ta ?? '0') }}" class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Allowance <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="allowance" id="allowance" placeholder="Enter allowance amount" value="{{ old('allowance', $structure?->allowance ?? '0') }}" class="sal-input">
                    </div>
                </div>

                <!-- Row 3: Deductions -->
                <div class="sal-form-row-4col">
                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            PF <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="pf" id="pf" placeholder="Enter PF amount" value="{{ old('pf', $structure?->pf ?? '0') }}" class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            ESI <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="esi" id="esi" placeholder="Enter ESI amount" value="{{ old('esi', $structure?->esi ?? '0') }}" class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            TDS <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="tds" id="tds" placeholder="Enter TDS amount" value="{{ old('tds', $structure?->tds ?? '0') }}" class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Other / Professional Tax <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="prof_tax" id="prof_tax" placeholder="Enter other tax amount" value="{{ old('prof_tax', $structure?->prof_tax ?? '0') }}" class="sal-input">
                    </div>
                </div>

                <!-- Row 4: Effective From & Active -->
                <div class="sal-form-row-2col">
                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Effective From <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="date" name="effective_from" id="effective_from" required value="{{ old('effective_from', $structure?->effective_from ? \Carbon\Carbon::parse($structure->effective_from)->format('Y-m-d') : date('Y-m-d')) }}" class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Active
                        </label>
                        <div class="sal-status-box" style="padding: 10px 16px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="is_active" value="1" id="isActiveCheck" {{ old('is_active', $structure?->is_active ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                            <label for="isActiveCheck" class="sal-label" style="font-size: 13.5px; font-weight: 700; color: #0f172a; cursor: pointer; margin: 0;">
                                <i class="fas fa-check-circle" style="color: #16a34a; margin-right: 4px;"></i> Active Status
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="sal-form-actions">
                    <button type="submit" class="btn-submit-main">
                        Submit
                    </button>
                    <a href="{{ route('school.payroll.salary-structure') }}" class="btn-discard-main">
                        Discard
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const staffDataMap = {
            @foreach($activeStaff as $staff)
                "{{ $staff->id }}": {
                    @if($staff->salaryStructure)
                        basic_salary: "{{ $staff->salaryStructure->basic_salary }}",
                        salary_type: "{{ $staff->salaryStructure->salary_type }}",
                        hra: "{{ $staff->salaryStructure->hra }}",
                        da: "{{ $staff->salaryStructure->da }}",
                        ta: "{{ $staff->salaryStructure->ta }}",
                        allowance: "{{ $staff->salaryStructure->allowance }}",
                        pf: "{{ $staff->salaryStructure->pf }}",
                        esi: "{{ $staff->salaryStructure->esi }}",
                        tds: "{{ $staff->salaryStructure->tds }}",
                        prof_tax: "{{ $staff->salaryStructure->prof_tax }}",
                        effective_from: "{{ $staff->salaryStructure->effective_from ? \Carbon\Carbon::parse($staff->salaryStructure->effective_from)->format('Y-m-d') : '' }}",
                        is_active: {{ $staff->salaryStructure->is_active ? 'true' : 'false' }}
                    @else
                        basic_salary: "{{ $staff->basic_salary ?: '' }}",
                        salary_type: "Monthly",
                        hra: "0",
                        da: "0",
                        ta: "0",
                        allowance: "0",
                        pf: "0",
                        esi: "0",
                        tds: "0",
                        prof_tax: "0",
                        effective_from: "{{ date('Y-m-d') }}",
                        is_active: true
                    @endif
                },
            @endforeach
        };

        const staffSelect = document.getElementById('staff_id_select');
        const basicSalaryInput = document.getElementById('basic_salary');
        const salaryTypeSelect = document.getElementById('salary_type');
        const hraInput = document.getElementById('hra');
        const daInput = document.getElementById('da');
        const taInput = document.getElementById('ta');
        const allowanceInput = document.getElementById('allowance');
        const pfInput = document.getElementById('pf');
        const esiInput = document.getElementById('esi');
        const tdsInput = document.getElementById('tds');
        const profTaxInput = document.getElementById('prof_tax');
        const effectiveFromInput = document.getElementById('effective_from');
        const isActiveCheck = document.getElementById('isActiveCheck');

        staffSelect.addEventListener('change', function () {
            const selectedId = this.value;
            if (selectedId && staffDataMap[selectedId]) {
                const data = staffDataMap[selectedId];
                if (data.basic_salary) basicSalaryInput.value = data.basic_salary;
                if (data.salary_type) salaryTypeSelect.value = data.salary_type;
                hraInput.value = data.hra || '0';
                daInput.value = data.da || '0';
                taInput.value = data.ta || '0';
                allowanceInput.value = data.allowance || '0';
                pfInput.value = data.pf || '0';
                esiInput.value = data.esi || '0';
                tdsInput.value = data.tds || '0';
                profTaxInput.value = data.prof_tax || '0';
                if (data.effective_from) effectiveFromInput.value = data.effective_from;
                isActiveCheck.checked = data.is_active;
            }
        });
    });
</script>
@endpush
@endsection
