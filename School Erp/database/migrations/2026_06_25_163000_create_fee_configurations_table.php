<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            
            // Fee Receipt settings
            $table->string('receipt_layout')->default('A4 Portrait');
            $table->string('invoice_layout')->default('A4 Portrait');
            $table->string('receipt_template')->default('Default Template');
            $table->string('advance_receipt_template')->default('Default Template');
            $table->integer('num_copies')->default(2);
            $table->string('default_payment_mode')->default('Cash');
            $table->string('discount_label')->default('Discount');
            
            // Payment link URL (edited by user)
            $table->boolean('payment_url_enabled')->default(true);
            $table->text('payment_url')->nullable();
            
            // Add to fee receipt
            $table->boolean('add_fee_due')->default(true);
            $table->boolean('add_fee_discount')->default(true);
            $table->boolean('add_fee_balance')->default(true);
            
            // Note on fee receipt
            $table->boolean('note_enabled')->default(false);
            $table->text('note_text')->nullable();
            
            // Other configuration toggles
            $table->boolean('show_zero_paid_component')->default(true);
            $table->boolean('collect_siblings_fee')->default(false);
            $table->boolean('receipt_date_editable')->default(true);
            $table->boolean('entry_date_editable')->default(true);
            $table->boolean('no_show_zero_pending')->default(false);
            $table->boolean('no_repeat_discount')->default(true);
            $table->boolean('no_allow_cancelled_receipts')->default(false);
            $table->boolean('allow_manual_receipt_no')->default(false);
            $table->boolean('round_off_discount')->default(false);
            $table->boolean('fine_apply_receipt_date')->default(false);
            $table->boolean('enable_multiple_installments')->default(false);
            $table->boolean('show_head_wise_total')->default(false);
            
            // Parent side configuration
            $table->boolean('parent_select_component')->default(true);
            $table->boolean('parent_select_fine')->default(true);
            $table->boolean('parent_no_partial_payment')->default(false);
            $table->boolean('parent_no_show_components')->default(false);
            $table->boolean('parent_show_only_current_installment')->default(false);
            
            // Tally Integration
            $table->boolean('tally_separate_ledgers')->default(false);
            
            // GST Configuration
            $table->boolean('gst_enabled')->default(false);
            
            // Show student details
            $table->boolean('details_receipt_no')->default(true);
            $table->boolean('details_receipt_date')->default(true);
            $table->boolean('details_session')->default(true);
            $table->boolean('details_student_name')->default(true);
            $table->boolean('details_admission_no')->default(true);
            $table->boolean('details_class')->default(true);
            $table->boolean('details_father_name')->default(false);
            $table->boolean('details_mother_name')->default(false);
            $table->boolean('details_address')->default(false);
            $table->boolean('details_father_phone')->default(false);
            $table->boolean('details_mother_phone')->default(false);
            
            // Show other institute fields
            $table->boolean('inst_affiliation_no')->default(false);
            $table->boolean('inst_school_url')->default(false);
            $table->boolean('inst_board_logo')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_configurations');
    }
};
