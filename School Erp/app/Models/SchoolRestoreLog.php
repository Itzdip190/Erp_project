<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class SchoolRestoreLog extends Model
{
    use BelongsToSchool;

    protected $table = 'school_restore_logs';

    protected $fillable = [
        'user_id',
        'school_id',
        'school_code',
        'snapshot_file',
        'snapshot_timestamp',
        'modules_selected',
        'tables_restored',
        'total_rows_deleted',
        'total_rows_inserted',
        'total_files_restored',
        'status',
        'error_message',
        'duration_ms',
        'pre_restore_backup',
        'ip_address',
    ];

    protected $casts = [
        'modules_selected' => 'array',
        'tables_restored' => 'array',
        'total_rows_deleted' => 'integer',
        'total_rows_inserted' => 'integer',
        'total_files_restored' => 'integer',
        'duration_ms' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
