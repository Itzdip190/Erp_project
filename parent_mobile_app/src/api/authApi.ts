import apiClient from './client';
import { API_ENDPOINTS } from '../config/constants';
import {
  ApiResponse,
  AuthResponseData,
  LoginCredentials,
  OtpSendRequest,
  OtpVerifyRequest,
  ParentUser,
} from '../types/auth';

export const AuthApi = {
  // Parent Login (Strict Parent/Student Guard)
  async login(credentials: LoginCredentials): Promise<AuthResponseData> {
    const response = await apiClient.post<ApiResponse<AuthResponseData>>(
      API_ENDPOINTS.LOGIN,
      {
        school_code: credentials.school_code,
        email: credentials.email,
        password: credentials.password,
        device_name: credentials.device_name || 'Android_Parent_App',
        fcm_token: credentials.fcm_token,
      }
    );

    if (!response.data.success) {
      throw new Error(response.data.message || 'Login failed.');
    }

    const userData = response.data.data.user;
    if (userData.role !== 'parent' && userData.role !== 'student') {
      throw new Error('Access Denied: This app is strictly for parents and student wards.');
    }

    return response.data.data;
  },

  // Send OTP
  async sendOtp(payload: OtpSendRequest): Promise<{ message: string; test_otp?: string }> {
    const response = await apiClient.post<ApiResponse<{ message: string; test_otp?: string }>>(
      API_ENDPOINTS.OTP_SEND,
      payload
    );
    if (!response.data.success) {
      throw new Error(response.data.message || 'Failed to send OTP.');
    }
    return response.data.data;
  },

  // Verify OTP
  async verifyOtp(payload: OtpVerifyRequest): Promise<AuthResponseData> {
    const response = await apiClient.post<ApiResponse<AuthResponseData>>(
      API_ENDPOINTS.OTP_VERIFY,
      {
        ...payload,
        device_name: payload.device_name || 'Android_Parent_App',
      }
    );
    if (!response.data.success) {
      throw new Error(response.data.message || 'OTP verification failed.');
    }
    return response.data.data;
  },

  // Get Current Parent Profile
  async getProfile(): Promise<ParentUser> {
    const response = await apiClient.get<ApiResponse<ParentUser>>(API_ENDPOINTS.ME);
    return response.data.data;
  },

  // Logout
  async logout(): Promise<void> {
    try {
      await apiClient.post(API_ENDPOINTS.LOGOUT);
    } catch (e) {
      console.warn('Logout API error:', e);
    }
  },

  // Fetch Mobile ERP Config & Branding
  async getAppConfig(): Promise<any> {
    const response = await apiClient.get(API_ENDPOINTS.APP_CONFIG, {
      params: { scope: 'parent' },
    });
    return response.data.data;
  },
};
