<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'receipt_layout',
        'invoice_layout',
        'receipt_template',
        'advance_receipt_template',
        'num_copies',
        'default_payment_mode',
        'discount_label',
        'payment_url_enabled',
        'payment_url',
        'add_fee_due',
        'add_fee_discount',
        'add_fee_balance',
        'note_enabled',
        'note_text',
        'show_zero_paid_component',
        'collect_siblings_fee',
        'receipt_date_editable',
        'entry_date_editable',
        'no_show_zero_pending',
        'no_repeat_discount',
        'no_allow_cancelled_receipts',
        'allow_manual_receipt_no',
        'round_off_discount',
        'fine_apply_receipt_date',
        'enable_multiple_installments',
        'show_head_wise_total',
        'parent_select_component',
        'parent_select_fine',
        'parent_no_partial_payment',
        'parent_no_show_components',
        'parent_show_only_current_installment',
        'tally_separate_ledgers',
        'gst_enabled',
        'details_receipt_no',
        'details_receipt_date',
        'details_session',
        'details_student_name',
        'details_admission_no',
        'details_class',
        'details_father_name',
        'details_mother_name',
        'details_address',
        'details_father_phone',
        'details_mother_phone',
        'inst_affiliation_no',
        'inst_school_url',
        'inst_board_logo',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
