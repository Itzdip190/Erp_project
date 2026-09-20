import apiClient from './client';
import { API_ENDPOINTS } from '../config/constants';
import { ApiResponse } from '../types/auth';
import { ExamScheduleItem, ReportCard } from '../types/academic';

export const ExamsApi = {
  // Exam Schedules
  async getExamSchedules(childId: number): Promise<ExamScheduleItem[]> {
    const response = await apiClient.get<ApiResponse<ExamScheduleItem[]>>(
      API_ENDPOINTS.EXAMS,
      { params: { student_id: childId } }
    );
    return response.data.data || [];
  },

  // Report Cards
  async getReportCards(childId: number): Promise<ReportCard[]> {
    const response = await apiClient.get<ApiResponse<ReportCard[]>>(
      API_ENDPOINTS.REPORT_CARDS,
      { params: { student_id: childId } }
    );
    return response.data.data || [];
  },
};
