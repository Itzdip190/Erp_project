export interface HomeworkItem {
  id: number;
  subject_name: string;
  subject_code?: string;
  title: string;
  description: string;
  assigned_date: string;
  submission_date: string;
  teacher_name?: string;
  attachment_url?: string;
  is_submitted: boolean;
  submission_status?: 'pending' | 'submitted' | 'evaluated';
  evaluation_remarks?: string;
  marks_obtained?: number;
  total_marks?: number;
}

export interface DiaryEntry {
  id: number;
  date: string;
  subject?: string;
  title: string;
  content: string;
  entry_type: 'homework' | 'remark' | 'activity' | 'announcement';
  teacher_name?: string;
  is_urgent?: boolean;
}

export interface TimetablePeriod {
  id: number;
  period_number: number;
  start_time: string;
  end_time: string;
  subject_name: string;
  teacher_name?: string;
  room_number?: string;
}

export interface DayTimetable {
  day: 'Monday' | 'Tuesday' | 'Wednesday' | 'Thursday' | 'Friday' | 'Saturday';
  periods: TimetablePeriod[];
}

export interface ExamScheduleItem {
  id: number;
  exam_name: string;
  subject_name: string;
  exam_date: string;
  start_time: string;
  end_time: string;
  room_number?: string;
  max_marks: number;
  passing_marks: number;
}

export interface SubjectMark {
  subject_name: string;
  max_marks: number;
  passing_marks: number;
  marks_obtained: number;
  grade?: string;
  remarks?: string;
}

export interface ReportCard {
  id: number;
  exam_name: string;
  academic_session: string;
  total_marks: number;
  obtained_marks: number;
  percentage: number;
  grade: string;
  rank?: number;
  attendance_percentage?: number;
  teacher_remarks?: string;
  subjects: SubjectMark[];
  download_url?: string;
}
