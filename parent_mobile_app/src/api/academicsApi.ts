import apiClient from './client';
import { API_ENDPOINTS } from '../config/constants';
import { ApiResponse } from '../types/auth';
import { DayTimetable, DiaryEntry, HomeworkItem } from '../types/academic';

export const AcademicsApi = {
  // Homework Assignments
  async getHomework(childId: number): Promise<HomeworkItem[]> {
    const response = await apiClient.get<ApiResponse<HomeworkItem[]>>(
      API_ENDPOINTS.HOMEWORK,
      { params: { student_id: childId } }
    );
    return response.data.data || [];
  },

  // Digital Diary Entries
  async getDiaryEntries(childId: number, date?: string): Promise<DiaryEntry[]> {
    const response = await apiClient.get<ApiResponse<DiaryEntry[]>>(API_ENDPOINTS.DIARY, {
      params: { student_id: childId, date },
    });
    return response.data.data || [];
  },

  // Timetable
  async getTimetable(childId: number): Promise<DayTimetable[]> {
    const response = await apiClient.get<ApiResponse<DayTimetable[]>>(
      API_ENDPOINTS.TIMETABLE,
      { params: { student_id: childId } }
    );
    return response.data.data || [];
  },
};
