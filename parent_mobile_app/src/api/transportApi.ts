import apiClient from './client';
import { API_ENDPOINTS } from '../config/constants';
import { ApiResponse } from '../types/auth';
import { BusTrackingInfo } from '../types/transport';

export const TransportApi = {
  // Live bus tracking & driver info
  async getLiveTracking(childId: number): Promise<BusTrackingInfo> {
    const response = await apiClient.get<ApiResponse<BusTrackingInfo>>(
      API_ENDPOINTS.TRANSPORT,
      { params: { student_id: childId } }
    );
    return response.data.data;
  },
};
