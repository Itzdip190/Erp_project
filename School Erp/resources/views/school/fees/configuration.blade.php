@extends('layouts.app')

@section('page-title', 'Fee Configuration')

@section('content')
<div class="fee-config" id="fee-config-container">
    <div class="page-hdr" style="border-bottom: 2px solid #bfdbfe; padding-bottom: 12px; margin-bottom: 24px;">
        <div class="page-hdr-left">
            <h1 style="color: #1e3a8a; font-size: 24px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">
                <i class="fas fa-sliders" style="color: #3b82f6; margin-right: 8px;"></i> Fee Configuration
            </h1>
            <p style="color: #475569; font-size: 13.5px; margin-top: 4px;">Configure fee layouts, payment URLs, default modes, details displayed on receipts, and parent portal app options.</p>
        </div>
        <div class="page-hdr-right">
            <button class="btn btn-outline" style="border-color: #bfdbfe; color: #1e40af; background: #eff6ff; font-weight: 700; font-size: 13px;" onclick="window.scrollTo({top: document.body.scrollHeight, behavior: 'smooth'})">
                <i class="fas fa-arrow-down"></i> Manage Categories
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 14.5px; padding: 14px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-check-circle" style="margin-right: 8px; font-size: 16px;"></i> {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('school.fees.configuration') }}">
        @csrf
        <input type="hidden" name="action" value="update_config">

        <!-- PANEL 1: Fee Receipt -->
        <div class="card" style="margin-bottom: 24px; border: 1px solid #bfdbfe; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.05);">
            <div class="card-hdr" style="background-color: #eff6ff; border-bottom: 1px solid #bfdbfe; padding: 16px 20px; color: #1e3a8a;">
                <h3 style="margin: 0; font-size: 16.5px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-invoice-dollar" style="color: #3b82f6;"></i> Fee Receipt Settings
                </h3>
            </div>
            <div class="card-body" style="padding: 24px; background: #fff;">
                <div class="grid-3" style="margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label" style="color: #1e3a8a; font-weight: 700; font-size: 12.5px;">Fee Receipt Layout</label>
                        <select name="receipt_layout" class="form-control select-blue">
                            <option value="A4 Portrait" {{ ($config?->receipt_layout == 'A4 Portrait') ? 'selected' : '' }}>A4 Portrait</option>
                            <option value="A4 Landscape" {{ ($config?->receipt_layout == 'A4 Landscape') ? 'selected' : '' }}>A4 Landscape</option>
                            <option value="Thermal Slip" {{ ($config?->receipt_layout == 'Thermal Slip') ? 'selected' : '' }}>Thermal Slip</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="color: #1e3a8a; font-weight: 700; font-size: 12.5px;">Fee Invoice Layout</label>
                        <select name="invoice_layout" class="form-control select-blue">
                            <option value="A4 Portrait" {{ ($config?->invoice_layout == 'A4 Portrait') ? 'selected' : '' }}>A4 Portrait</option>
                            <option value="A4 Landscape" {{ ($config?->invoice_layout == 'A4 Landscape') ? 'selected' : '' }}>A4 Landscape</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="color: #1e3a8a; font-weight: 700; font-size: 12.5px;">Fee Receipt Template</label>
                        <select name="receipt_template" class="form-control select-blue">
                            <option value="Default Template" {{ ($config?->receipt_template == 'Default Template') ? 'selected' : '' }}>Default Template</option>
                            <option value="Modern Template" {{ ($config?->receipt_template == 'Modern Template') ? 'selected' : '' }}>Modern Template</option>
                            <option value="Minimal Template" {{ ($config?->receipt_template == 'Minimal Template') ? 'selected' : '' }}>Minimal Template</option>
                        </select>
                    </div>
                </div>

                <div class="grid-3" style="margin-bottom: 24px;">
                    <div class="form-group">
                        <label class="form-label" style="color: #1e3a8a; font-weight: 700; font-size: 12.5px;">Fee receipt template (for advance payment collection)</label>
                        <select name="advance_receipt_template" class="form-control select-blue">
                            <option value="Default Template" {{ ($config?->advance_receipt_template == 'Default Template') ? 'selected' : '' }}>Default Template</option>
                            <option value="Advance Special Template" {{ ($config?->advance_receipt_template == 'Advance Special Template') ? 'selected' : '' }}>Advance Special Template</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="color: #1e3a8a; font-weight: 700; font-size: 12.5px;">No. of Copies</label>
                        <select name="num_copies" class="form-control select-blue">
                            <option value="1" {{ ($config?->num_copies == 1) ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($config?->num_copies == 2) ? 'selected' : '' }}>2</option>
                            <option value="3" {{ ($config?->num_copies == 3) ? 'selected' : '' }}>3</option>
                            <option value="4" {{ ($config?->num_copies == 4) ? 'selected' : '' }}>4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="color: #1e3a8a; font-weight: 700; font-size: 12.5px;">Default Payment Mode</label>
                        <select name="default_payment_mode" class="form-control select-blue">
                            <option value="Cash" {{ ($config?->default_payment_mode == 'Cash') ? 'selected' : '' }}>Cash</option>
                            <option value="Cheque" {{ ($config?->default_payment_mode == 'Cheque') ? 'selected' : '' }}>Cheque</option>
                            <option value="Online" {{ ($config?->default_payment_mode == 'Online') ? 'selected' : '' }}>Online</option>
                            <option value="UPI" {{ ($config?->default_payment_mode == 'UPI') ? 'selected' : '' }}>UPI</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom: 24px;">
                    <div class="form-group">
                        <label class="form-label" style="color: #1e3a8a; font-weight: 700; font-size: 12.5px;">Label for Discount</label>
                        <input type="text" name="discount_label" value="{{ $config?->discount_label ?? 'Discount' }}" class="form-control input-blue" placeholder="e.g. Discount, Concession, Waveoff">
                    </div>
                </div>

                <!-- Payment URL Row -->
                <div class="form-group" style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="color: #1e3a8a; font-weight: 700; font-size: 13.5px;">Payment URL Configuration</span>
                        <label class="switch">
                            <input type="checkbox" name="payment_url_enabled" value="1" {{ ($config?->payment_url_enabled ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="text" id="payment_url_input" name="payment_url" value="{{ $config?->payment_url ?? 'https://online.edutinker.com/form/student/fees?schoolId=' . auth()->user()->school_id . '&schoolName=Pragya%20School' }}" class="form-control" style="background:#fff; border-color:#bfdbfe; color:#1e293b; flex:1;" placeholder="Enter custom gateway URL. Use tags like {student_id}, {amount}, {purpose}">
                        <button type="button" class="btn btn-outline" style="border-color: #bfdbfe; background: #fff; padding: 10px;" onclick="copyUrlToClipboard()" title="Copy Payment Link">
                            <i class="fas fa-copy" style="color: #3b82f6;"></i>
                        </button>
                    </div>
                    <small style="display:block; color:#64748b; font-size:11.5px; margin-top:6px; line-height:1.4;">
                        <i class="fas fa-info-circle" style="color: #3b82f6;"></i> Replaceable parameters: <code>{student_id}</code>, <code>{student_name}</code>, <code>{admission_no}</code>, <code>{amount}</code>, <code>{purpose}</code>, <code>{school_id}</code>.
                    </small>
                </div>

                <!-- Add to fee receipt checkboxes -->
                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" style="color: #1e3a8a; font-weight: 700; font-size: 12.5px; margin-bottom: 12px; display:block;">Add to fee receipt</label>
                    <div style="display:flex; gap:24px; flex-wrap:wrap;">
                        <label style="display:inline-flex; align-items:center; gap:8px; font-weight:600; color:#1e293b; cursor:pointer;">
                            <input type="checkbox" name="add_fee_due" value="1" {{ ($config?->add_fee_due ?? true) ? 'checked' : '' }} class="checkbox-blue"> Fee Due
                        </label>
                        <label style="display:inline-flex; align-items:center; gap:8px; font-weight:600; color:#1e293b; cursor:pointer;">
                            <input type="checkbox" name="add_fee_discount" value="1" {{ ($config?->add_fee_discount ?? true) ? 'checked' : '' }} class="checkbox-blue"> Fee Discount
                        </label>
                        <label style="display:inline-flex; align-items:center; gap:8px; font-weight:600; color:#1e293b; cursor:pointer;">
                            <input type="checkbox" name="add_fee_balance" value="1" {{ ($config?->add_fee_balance ?? true) ? 'checked' : '' }} class="checkbox-blue"> Fee Balance
                        </label>
                    </div>
                </div>

                <!-- Note on Receipt -->
                <div class="form-group" style="border-top:1px solid #e2e8f0; padding-top:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <label class="form-label" style="color: #1e3a8a; font-weight: 700; font-size: 12.5px; margin:0;">Note on Fee Receipt</label>
                        <label class="switch">
                            <input type="checkbox" name="note_enabled" value="1" {{ ($config?->note_enabled) ? 'checked' : '' }} onchange="toggleNoteText(this.checked)">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <textarea name="note_text" id="note_text_area" class="form-control input-blue" style="height: 60px; {{ ($config?->note_enabled) ? '' : 'display:none;' }}" placeholder="Add a custom note to display at the footer of student fee receipts...">{{ $config?->note_text ?? 'Note on Fee Receipt' }}</textarea>
                </div>
            </div>
        </div>

        <!-- PANEL 2: Other payment configuration -->
        <div class="card" style="margin-bottom: 24px; border: 1px solid #bfdbfe; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.05);">
            <div class="card-hdr" style="background-color: #eff6ff; border-bottom: 1px solid #bfdbfe; padding: 16px 20px; color: #1e3a8a;">
                <h3 style="margin: 0; font-size: 16.5px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-cogs" style="color: #3b82f6;"></i> Other Payment Configuration
                </h3>
            </div>
            <div class="card-body" style="padding: 24px; background: #fff;">
                <div class="toggle-list">
                    <!-- Toggle item -->
                    <div class="toggle-item">
                        <span>Show zero paid component in receipt if selected at time of payment</span>
                        <label class="switch">
                            <input type="checkbox" name="show_zero_paid_component" value="1" {{ ($config?->show_zero_paid_component ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Collect siblings fee together in single page</span>
                        <label class="switch">
                            <input type="checkbox" name="collect_siblings_fee" value="1" {{ ($config?->collect_siblings_fee) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Keep fee receipt date editable at time of student fee collection</span>
                        <label class="switch">
                            <input type="checkbox" name="receipt_date_editable" value="1" {{ ($config?->receipt_date_editable ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Keep fee entry date editable at time of student fee collection</span>
                        <label class="switch">
                            <input type="checkbox" name="entry_date_editable" value="1" {{ ($config?->entry_date_editable ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Do not show component with zero pending amount at time of marking paid</span>
                        <label class="switch">
                            <input type="checkbox" name="no_show_zero_pending" value="1" {{ ($config?->no_show_zero_pending) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Do not repeat discount in fee receipt</span>
                        <label class="switch">
                            <input type="checkbox" name="no_repeat_discount" value="1" {{ ($config?->no_repeat_discount ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Do not allow usage of cancelled receipt numbers</span>
                        <label class="switch">
                            <input type="checkbox" name="no_allow_cancelled_receipts" value="1" {{ ($config?->no_allow_cancelled_receipts) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Allow Manual Input of Receipt Number</span>
                        <label class="switch">
                            <input type="checkbox" name="allow_manual_receipt_no" value="1" {{ ($config?->allow_manual_receipt_no) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Round off discount</span>
                        <label class="switch">
                            <input type="checkbox" name="round_off_discount" value="1" {{ ($config?->round_off_discount) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Fine should apply as per fee receipt date</span>
                        <label class="switch">
                            <input type="checkbox" name="fine_apply_receipt_date" value="1" {{ ($config?->fine_apply_receipt_date) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Enable multiple installments option in Student-wise fee to collect installment from student</span>
                        <label class="switch">
                            <input type="checkbox" name="enable_multiple_installments" value="1" {{ ($config?->enable_multiple_installments) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Show head-wise total in Mark Paid field</span>
                        <label class="switch">
                            <input type="checkbox" name="show_head_wise_total" value="1" {{ ($config?->show_head_wise_total) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL 3: Parent side configuration on app -->
        <div class="card" style="margin-bottom: 24px; border: 1px solid #bfdbfe; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.05);">
            <div class="card-hdr" style="background-color: #eff6ff; border-bottom: 1px solid #bfdbfe; padding: 16px 20px; color: #1e3a8a;">
                <h3 style="margin: 0; font-size: 16.5px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-mobile-screen" style="color: #3b82f6;"></i> Parent Side Configuration on App
                </h3>
            </div>
            <div class="card-body" style="padding: 24px; background: #fff;">
                <div class="toggle-list">
                    <div class="toggle-item">
                        <span>Allow component to be selected at time of making payment on parent side</span>
                        <label class="switch">
                            <input type="checkbox" name="parent_select_component" value="1" {{ ($config?->parent_select_component ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Allow fine to be selected at time of making payment on parent side</span>
                        <label class="switch">
                            <input type="checkbox" name="parent_select_fine" value="1" {{ ($config?->parent_select_fine ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Don't allow student/parent to do partial payment of component</span>
                        <label class="switch">
                            <input type="checkbox" name="parent_no_partial_payment" value="1" {{ ($config?->parent_no_partial_payment) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Do not show components when student is paying the fee from app</span>
                        <label class="switch">
                            <input type="checkbox" name="parent_no_show_components" value="1" {{ ($config?->parent_no_show_components) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <span>Show only current installment</span>
                        <label class="switch">
                            <input type="checkbox" name="parent_show_only_current_installment" value="1" {{ ($config?->parent_show_only_current_installment) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL 4: Tally & GST -->
        <div class="grid-2" style="margin-bottom: 24px;">
            <div class="card" style="margin-bottom: 0; border: 1px solid #bfdbfe; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.05);">
                <div class="card-hdr" style="background-color: #eff6ff; border-bottom: 1px solid #bfdbfe; padding: 16px 20px; color: #1e3a8a;">
                    <h3 style="margin: 0; font-size: 16.5px; font-weight: 700;">Tally Integration</h3>
                </div>
                <div class="card-body" style="padding: 24px; background: #fff;">
                    <div class="toggle-item" style="border:none; padding:0;">
                        <span>Create separate ledgers for each fee component</span>
                        <label class="switch">
                            <input type="checkbox" name="tally_separate_ledgers" value="1" {{ ($config?->tally_separate_ledgers) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 0; border: 1px solid #bfdbfe; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.05);">
                <div class="card-hdr" style="background-color: #eff6ff; border-bottom: 1px solid #bfdbfe; padding: 16px 20px; color: #1e3a8a;">
                    <h3 style="margin: 0; font-size: 16.5px; font-weight: 700;">GST Configuration</h3>
                </div>
                <div class="card-body" style="padding: 24px; background: #fff;">
                    <div class="toggle-item" style="border:none; padding:0;">
                        <span>Enable the GST on Components Fee and Configuration</span>
                        <label class="switch">
                            <input type="checkbox" name="gst_enabled" value="1" {{ ($config?->gst_enabled) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL 5: Student & Institute fields on Receipt -->
        <div class="grid-2" style="margin-bottom: 24px;">
            <!-- Student Details -->
            <div class="card" style="margin-bottom: 0; border: 1px solid #bfdbfe; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.05);">
                <div class="card-hdr" style="background-color: #eff6ff; border-bottom: 1px solid #bfdbfe; padding: 16px 20px; color: #1e3a8a;">
                    <h3 style="margin: 0; font-size: 16.5px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-id-card-clip" style="color: #3b82f6;"></i> Show Student's Basic Details on Receipt
                    </h3>
                </div>
                <div class="card-body" style="padding: 20px 24px; background: #fff;">
                    <div class="list-order-container">
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Receipt No.</span>
                            <label class="switch">
                                <input type="checkbox" name="details_receipt_no" value="1" {{ ($config?->details_receipt_no ?? true) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Receipt Date</span>
                            <label class="switch">
                                <input type="checkbox" name="details_receipt_date" value="1" {{ ($config?->details_receipt_date ?? true) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Academic Session</span>
                            <label class="switch">
                                <input type="checkbox" name="details_session" value="1" {{ ($config?->details_session ?? true) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Student Name</span>
                            <label class="switch">
                                <input type="checkbox" name="details_student_name" value="1" {{ ($config?->details_student_name ?? true) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Admission No.</span>
                            <label class="switch">
                                <input type="checkbox" name="details_admission_no" value="1" {{ ($config?->details_admission_no ?? true) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Student Class</span>
                            <label class="switch">
                                <input type="checkbox" name="details_class" value="1" {{ ($config?->details_class ?? true) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Father's Name</span>
                            <label class="switch">
                                <input type="checkbox" name="details_father_name" value="1" {{ ($config?->details_father_name) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Mother's Name</span>
                            <label class="switch">
                                <input type="checkbox" name="details_mother_name" value="1" {{ ($config?->details_mother_name) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Address</span>
                            <label class="switch">
                                <input type="checkbox" name="details_address" value="1" {{ ($config?->details_address) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Father's Phone</span>
                            <label class="switch">
                                <input type="checkbox" name="details_father_phone" value="1" {{ ($config?->details_father_phone) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="order-item">
                            <span class="item-handle"><i class="fas fa-grip-vertical"></i> Mother's Phone</span>
                            <label class="switch">
                                <input type="checkbox" name="details_mother_phone" value="1" {{ ($config?->details_mother_phone) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Institute fields -->
            <div class="card" style="margin-bottom: 0; border: 1px solid #bfdbfe; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.05);">
                <div class="card-hdr" style="background-color: #eff6ff; border-bottom: 1px solid #bfdbfe; padding: 16px 20px; color: #1e3a8a;">
                    <h3 style="margin: 0; font-size: 16.5px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-school" style="color: #3b82f6;"></i> Show Other Institute Fields
                    </h3>
                </div>
                <div class="card-body" style="padding: 24px; background: #fff;">
                    <div class="toggle-list">
                        <div class="toggle-item">
                            <span>Affiliation No.</span>
                            <label class="switch">
                                <input type="checkbox" name="inst_affiliation_no" value="1" {{ ($config?->inst_affiliation_no) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <span>School URL</span>
                            <label class="switch">
                                <input type="checkbox" name="inst_school_url" value="1" {{ ($config?->inst_school_url) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <span>Board Logo</span>
                            <label class="switch">
                                <input type="checkbox" name="inst_board_logo" value="1" {{ ($config?->inst_board_logo) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Save Configuration Button -->
        <div style="background: #eff6ff; border: 2px solid #bfdbfe; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 40px; box-shadow: 0 10px 15px -3px rgba(59,130,246,0.1);">
            <p style="color: #1e3a8a; font-size: 14.5px; font-weight: 700; margin-bottom: 12px;">Ensure all layout configurations and payment URLs are correct before saving.</p>
            <button type="submit" class="btn btn-primary" style="background:#1e40af; border:none; padding:12px 30px; font-size:14.5px; font-weight:700; border-radius:8px; display:inline-flex; align-items:center; gap:8px; cursor:pointer; color:#fff;">
                <i class="fas fa-save"></i> Save Configuration Settings
            </button>
        </div>
    </form>

    <hr style="border: 0; border-top: 2px dashed #bfdbfe; margin: 40px 0;">

    <!-- PANEL 6: Category Management (Preserved Feature) -->
    <div class="grid-3" id="categories-section" style="margin-top: 24px;">
        <div class="card" style="grid-column: span 1; border: 1px solid #bfdbfe; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="card-hdr" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px; color: #1e3a8a;">
                <h3 style="margin: 0; font-size: 15px; font-weight: 700;">Add Fee Category</h3>
            </div>
            <div class="card-body" style="padding: 20px; background: #fff;">
                <form method="POST" action="{{ route('school.fees.configuration') }}">
                    @csrf
                    <input type="hidden" name="action" value="add_category">
                    <div class="form-group">
                        <label class="form-label" style="color: #475569; font-weight: 700; font-size: 12px;">Category Name</label>
                        <input type="text" name="name" class="form-control input-blue" placeholder="e.g. Science Lab Fee, Hostel Fee" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="color: #475569; font-weight: 700; font-size: 12px;">Description</label>
                        <textarea name="description" class="form-control input-blue" style="height:80px;" placeholder="Brief details about what this fee covers..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; background:#f59e0b; color:#1e1b4b; font-weight:700; padding:10px;">
                        <i class="fas fa-plus-circle"></i> Add Category
                    </button>
                </form>
            </div>
        </div>

        <div class="card" style="grid-column: span 2; border: 1px solid #bfdbfe; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="card-hdr" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px; color: #1e3a8a;">
                <h3 style="margin: 0; font-size: 15px; font-weight: 700;">Fee Categories Directory</h3>
            </div>
            <div class="card-body" style="padding:0; background: #fff;">
                <div class="table-wrap">
                    <table class="tbl" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px 16px; text-align: left; font-size: 11.5px; color: #475569;">ID</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 11.5px; color: #475569;">Fee Category Name</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 11.5px; color: #475569;">Description</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 11.5px; color: #475569;">Date Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px;"><strong>#{{ $category->id }}</strong></td>
                                <td style="padding: 12px 16px;"><strong style="color:#1e3a8a;">{{ $category->name }}</strong></td>
                                <td style="padding: 12px 16px;"><span style="color:#64748b; font-size: 13px;">{{ $category->description ?? 'No description provided.' }}</span></td>
                                <td style="padding: 12px 16px; color:#64748b; font-size:12.5px;">{{ $category->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding:30px; color:#94a3b8;">No fee categories defined.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Scoped Fee Config Styles - Blue and White theme with bigger fonts */
.fee-config {
    font-size: 15px !important;
}
.fee-config input, .fee-config select, .fee-config textarea {
    font-size: 14px !important;
    padding: 10px 14px !important;
}
.fee-config label {
    font-size: 13px !important;
}
.select-blue, .input-blue {
    border: 1px solid #bfdbfe !important;
    background-color: #fff !important;
}
.select-blue:focus, .input-blue:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
}
.checkbox-blue {
    accent-color: #3b82f6;
    width: 17px;
    height: 17px;
}

/* Switch styling matching the toggles in screenshot */
.switch {
  position: relative;
  display: inline-block;
  width: 46px;
  height: 24px;
  flex-shrink: 0;
}
.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #fca5a5; /* Pink/Red when off */
  transition: .3s;
  border-radius: 24px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .3s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #10b981; /* Green when on */
}
input:checked + .slider:before {
  transform: translateX(22px);
}

/* Toggle List Layout */
.toggle-list {
    display: flex;
    flex-direction: column;
    gap: 0;
}
.toggle-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}
.toggle-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.toggle-item > span {
    font-weight: 600;
    color: #334155;
    font-size: 14px;
    line-height: 1.5;
}

/* Draggable/Reorderable student details styling */
.list-order-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}
.order-item .item-handle {
    font-weight: 600;
    color: #475569;
    font-size: 13.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.order-item .item-handle i {
    color: #94a3b8;
    cursor: grab;
}
</style>

<script>
function copyUrlToClipboard() {
    const copyText = document.getElementById("payment_url_input");
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
    
    try {
        navigator.clipboard.writeText(copyText.value);
        showToast("Payment URL copied to clipboard!");
    } catch (err) {
        document.execCommand("copy");
        showToast("Payment URL copied to clipboard!");
    }
}

function showToast(message) {
    const toast = document.getElementById("appToast");
    if (toast) {
        toast.textContent = message;
        toast.classList.add("show");
        setTimeout(() => {
            toast.classList.remove("show");
        }, 3000);
    } else {
        alert(message);
    }
}

function toggleNoteText(checked) {
    const textArea = document.getElementById("note_text_area");
    if (textArea) {
        textArea.style.display = checked ? "block" : "none";
        if (checked) textArea.focus();
    }
}
</script>
@endsection
