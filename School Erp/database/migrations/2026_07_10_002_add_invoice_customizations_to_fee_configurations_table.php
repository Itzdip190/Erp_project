<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_configurations', function (Blueprint $table) {
            $table->boolean('show_installment_components_on_invoice')->default(false)->after('note_text');
            $table->boolean('show_due_on_invoice')->default(true)->after('show_installment_components_on_invoice');
        });
    }

    public function down(): void
    {
        Schema::table('fee_configurations', function (Blueprint $table) {
            $table->dropColumn(['show_installment_components_on_invoice', 'show_due_on_invoice']);
        });
    }
};
