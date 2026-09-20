export type AttendanceStatus = 'present' | 'absent' | 'late' | 'half_day' | 'holiday' | 'leave';

export interface AttendanceRecord {
  id: number;
  date: string;
  status: AttendanceStatus;
  remark?: string;
  check_in_time?: string;
  check_out_time?: string;
}

export interface AttendanceMonthlySummary {
  month: string;
  total_days: number;
  present_days: number;
  absent_days: number;
  late_days: number;
  half_days: number;
  holidays: number;
  percentage: number;
  records: Record<string, AttendanceRecord>;
}

export interface LeaveRequest {
  id: number;
  student_id: number;
  from_date: string;
  to_date: string;
  reason: string;
  status: 'pending' | 'approved' | 'rejected';
  rejection_reason?: string;
  created_at: string;
}

export interface ApplyLeavePayload {
  student_id: number;
  from_date: string;
  to_date: string;
  reason: string;
  attachment?: string;
}
