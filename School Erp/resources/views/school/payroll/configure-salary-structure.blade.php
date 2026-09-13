@extends('layouts.app')

@section('title', ($structure ? 'Edit' : 'Create') . ' Salary Structure — HR Payroll')

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
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .sal-card-body {
        padding: 28px;
    }
    .sal-form-row-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    .sal-form-row-3col {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    .sal-form-row-4col {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }
    .sal-input {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        outline: none;
        background: #ffffff;
        color: #1e293b;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .sal-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .btn-submit-main {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        padding: 12px 36px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        transition: all 0.2s ease;
    }
    .btn-submit-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
    }
    .btn-discard-main {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .btn-discard-main:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .sal-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 14px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #e2e8f0;
    }
    
    /* Live Summary Card */
    .sal-live-summary {
        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 28px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    .sal-live-item-title {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .sal-live-item-val {
        font-size: 20px;
        font-weight: 800;
    }

    .sal-section-hdr {
        font-size: 13.5px;
        font-weight: 800;
        color: #1e40af;
        margin: 20px 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .sal-container {
            padding: 14px 10px !important;
        }
        .sal-form-row-2col, .sal-form-row-3col, .sal-form-row-4col {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
        }
        .sal-live-summary {
            grid-template-columns: 1fr 1fr !important;
        }
    }
</style>
@endsection

@section('content')
<div class="sal-container">

    @if ($errors->any())
        <div style="padding: 14px 18px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(239,68,68,0.1);">
            <div style="font-weight: 800; margin-bottom: 6px;"><i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> Please check the following errors:</div>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Configure Form Card -->
    <div class="sal-card">
        
        <!-- Header Banner -->
        <div class="sal-card-hdr">
            <div style="font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 10px; letter-spacing: 0.2px;">
                <i class="fas fa-coins" style="color: #ffffff;"></i>
                {{ $structure ? 'Edit Salary Structure: ' . $structure->name : 'Create New Salary Structure' }}
            </div>
            <a href="{{ route('school.payroll.salary-structure') }}" style="color: #ffffff; text-decoration: none; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(4px);">
                <i class="fas fa-arrow-left"></i> Back to Salary Structures
            </a>
        </div>

        <div class="sal-card-body">

            <!-- Live Calculation Preview Banner -->
            <div class="sal-live-summary">
                <div>
                    <div class="sal-live-item-title">Basic Salary</div>
                    <div class="sal-live-item-val" style="color: #2563eb;" id="previewBasic">₹0.00</div>
                </div>
                <div>
                    <div class="sal-live-item-title">Total Allowances</div>
                    <div class="sal-live-item-val" style="color: #059669;" id="previewAllowances">+₹0.00</div>
                </div>
                <div>
                    <div class="sal-live-item-title">Total Deductions</div>
                    <div class="sal-live-item-val" style="color: #dc2626;" id="previewDeductions">-₹0.00</div>
                </div>
                <div>
                    <div class="sal-live-item-title">Estimated Net Pay</div>
                    <div class="sal-live-item-val" style="color: #1e3a8a;" id="previewNet">₹0.00</div>
                </div>
            </div>

            <form action="{{ route('school.payroll.salary-structure.store') }}" method="POST" id="salaryStructureForm">
                @csrf
                @if($structure)
                    <input type="hidden" name="id" value="{{ $structure->id }}">
                @endif

                <!-- Section 1: Basic Information -->
                <div class="sal-section-hdr">
                    <i class="fas fa-info-circle"></i> Basic Structure Details
                </div>

                <div class="sal-form-row-3col">
                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Structure Name / Title <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" name="name" id="name" placeholder="e.g. Senior Faculty Grade A, Primary Teacher, Admin Staff" value="{{ old('name', $structure?->name ?? '') }}" required class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Basic Salary (₹) <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="basic_salary" id="basic_salary" placeholder="0.00" value="{{ old('basic_salary', $structure?->basic_salary ?? '') }}" required class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Salary Type <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="salary_type" id="salary_type" required class="sal-input">
                            <option value="Monthly" {{ old('salary_type', $structure?->salary_type ?? 'Monthly') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="Daily" {{ old('salary_type', $structure?->salary_type) == 'Daily' ? 'selected' : '' }}>Daily</option>
                            <option value="Hourly" {{ old('salary_type', $structure?->salary_type) == 'Hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="Contract" {{ old('salary_type', $structure?->salary_type) == 'Contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                        Description / Department Eligibility (Optional)
                    </label>
                    <input type="text" name="description" id="description" placeholder="Brief note or applicable grades/roles for this structure" value="{{ old('description', $structure?->description ?? '') }}" class="sal-input">
                </div>

                <!-- Section 2: Allowances -->
                <div class="sal-section-hdr" style="color: #059669;">
                    <i class="fas fa-plus-circle"></i> Earnings & Allowances (₹)
                </div>

                <div class="sal-form-row-4col">
                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            House Rent Allowance (HRA)
                        </label>
                        <input type="number" step="0.01" name="hra" id="hra" placeholder="0.00" value="{{ old('hra', $structure?->hra ?? '0') }}" class="sal-input sal-calc">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Dearness Allowance (DA)
                        </label>
                        <input type="number" step="0.01" name="da" id="da" placeholder="0.00" value="{{ old('da', $structure?->da ?? '0') }}" class="sal-input sal-calc">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Transport Allowance (TA)
                        </label>
                        <input type="number" step="0.01" name="ta" id="ta" placeholder="0.00" value="{{ old('ta', $structure?->ta ?? '0') }}" class="sal-input sal-calc">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Other Allowance / Incentives
                        </label>
                        <input type="number" step="0.01" name="allowance" id="allowance" placeholder="0.00" value="{{ old('allowance', $structure?->allowance ?? '0') }}" class="sal-input sal-calc">
                    </div>
                </div>

                <!-- Section 3: Statutory Deductions -->
                <div class="sal-section-hdr" style="color: #dc2626;">
                    <i class="fas fa-minus-circle"></i> Statutory Deductions (₹)
                </div>

                <div class="sal-form-row-4col">
                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Provident Fund (PF)
                        </label>
                        <input type="number" step="0.01" name="pf" id="pf" placeholder="0.00" value="{{ old('pf', $structure?->pf ?? '0') }}" class="sal-input sal-calc">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Employee State Insurance (ESI)
                        </label>
                        <input type="number" step="0.01" name="esi" id="esi" placeholder="0.00" value="{{ old('esi', $structure?->esi ?? '0') }}" class="sal-input sal-calc">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Tax Deducted at Source (TDS)
                        </label>
                        <input type="number" step="0.01" name="tds" id="tds" placeholder="0.00" value="{{ old('tds', $structure?->tds ?? '0') }}" class="sal-input sal-calc">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Other / Professional Tax
                        </label>
                        <input type="number" step="0.01" name="prof_tax" id="prof_tax" placeholder="0.00" value="{{ old('prof_tax', $structure?->prof_tax ?? '0') }}" class="sal-input sal-calc">
                    </div>
                </div>

                <!-- Section 4: Effective Date & Status -->
                <div class="sal-section-hdr" style="color: #475569;">
                    <i class="fas fa-calendar-check"></i> Validity & Status
                </div>

                <div class="sal-form-row-2col">
                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Effective From Date <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="date" name="effective_from" id="effective_from" required value="{{ old('effective_from', $structure?->effective_from ? \Carbon\Carbon::parse($structure->effective_from)->format('Y-m-d') : date('Y-m-d')) }}" class="sal-input">
                    </div>

                    <div>
                        <label class="sal-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Structure Active Status
                        </label>
                        <div style="padding: 10px 16px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="is_active" value="1" id="isActiveCheck" {{ old('is_active', $structure?->is_active ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                            <label for="isActiveCheck" style="font-size: 13.5px; font-weight: 700; color: #0f172a; cursor: pointer; margin: 0;">
                                <i class="fas fa-check-circle" style="color: #16a34a; margin-right: 4px;"></i> Active (Available for Teacher Assignment)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="sal-form-actions">
                    <button type="submit" class="btn-submit-main">
                        <i class="fas fa-save me-1"></i> {{ $structure ? 'Update Structure' : 'Save Salary Structure' }}
                    </button>
                    <a href="{{ route('school.payroll.salary-structure') }}" class="btn-discard-main">
                        Discard & Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const basicInput = document.getElementById('basic_salary');
        const hraInput = document.getElementById('hra');
        const daInput = document.getElementById('da');
        const taInput = document.getElementById('ta');
        const allowanceInput = document.getElementById('allowance');
        const pfInput = document.getElementById('pf');
        const esiInput = document.getElementById('esi');
        const tdsInput = document.getElementById('tds');
        const profTaxInput = document.getElementById('prof_tax');

        const previewBasic = document.getElementById('previewBasic');
        const previewAllowances = document.getElementById('previewAllowances');
        const previewDeductions = document.getElementById('previewDeductions');
        const previewNet = document.getElementById('previewNet');

        function updateLiveCalc() {
            const basic = parseFloat(basicInput.value) || 0;
            const hra = parseFloat(hraInput.value) || 0;
            const da = parseFloat(daInput.value) || 0;
            const ta = parseFloat(taInput.value) || 0;
            const allowance = parseFloat(allowanceInput.value) || 0;

            const pf = parseFloat(pfInput.value) || 0;
            const esi = parseFloat(esiInput.value) || 0;
            const tds = parseFloat(tdsInput.value) || 0;
            const profTax = parseFloat(profTaxInput.value) || 0;

            const totalAllowances = hra + da + ta + allowance;
            const totalDeductions = pf + esi + tds + profTax;
            const netPay = Math.max(0, basic + totalAllowances - totalDeductions);

            previewBasic.innerText = '₹' + basic.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            previewAllowances.innerText = '+₹' + totalAllowances.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            previewDeductions.innerText = '-₹' + totalDeductions.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            previewNet.innerText = '₹' + netPay.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        const calcInputs = [basicInput, hraInput, daInput, taInput, allowanceInput, pfInput, esiInput, tdsInput, profTaxInput];
        calcInputs.forEach(input => {
            if (input) {
                input.addEventListener('input', updateLiveCalc);
            }
        });

        updateLiveCalc();
    });
</script>
@endsection
