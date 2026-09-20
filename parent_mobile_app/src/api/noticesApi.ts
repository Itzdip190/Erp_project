import apiClient from './client';
import { API_ENDPOINTS } from '../config/constants';
import { ApiResponse } from '../types/auth';
import { AppBanner, NoticeItem } from '../types/notice';

export const NoticesApi = {
  // Notices / Circulars
  async getNotices(childId?: number): Promise<NoticeItem[]> {
    const response = await apiClient.get<ApiResponse<NoticeItem[]>>(API_ENDPOINTS.NOTICES, {
      params: { student_id: childId },
    });
    return response.data.data || [];
  },

  // Promotional Banners & Sliders
  async getBanners(): Promise<AppBanner[]> {
    const response = await apiClient.get(API_ENDPOINTS.APP_BANNERS, {
      params: { role: 'parent' },
    });
    return response.data?.data?.banners || [];
  },
};
