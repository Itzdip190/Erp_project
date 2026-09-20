import apiClient from './client';
import { API_ENDPOINTS } from '../config/constants';
import { ApiResponse } from '../types/auth';
import {
  ApplyLeavePayload,
  AttendanceMonthlySummary,
  LeaveRequest,
} from '../types/attendance';

export const AttendanceApi = {
  // Get Monthly Attendance for selected child
  async getMonthlyAttendance(childId: number, month?: string, year?: string): Promise<AttendanceMonthlySummary> {
    const response = await apiClient.get<ApiResponse<AttendanceMonthlySummary>>(
      API_ENDPOINTS.ATTENDANCE,
      {
        params: {
          student_id: childId,
          month: month || new Date().toISOString().slice(5, 7),
          year: year || new Date().getFullYear().toString(),
        },
      }
    );
    return response.data.data;
  },

  // Get leave requests history
  async getLeaves(childId: number): Promise<LeaveRequest[]> {
    const response = await apiClient.get<ApiResponse<LeaveRequest[]>>(API_ENDPOINTS.LEAVES, {
      params: { student_id: childId },
    });
    return response.data.data || [];
  },

  // Submit a leave application
  async applyLeave(payload: ApplyLeavePayload): Promise<{ message: string; id?: number }> {
    const response = await apiClient.post<ApiResponse<{ message: string; id?: number }>>(
      API_ENDPOINTS.LEAVE_APPLY,
      payload
    );
    return response.data.data;
  },
};
