import axios, { AxiosError, AxiosInstance, InternalAxiosRequestConfig } from 'axios';
import { API_BASE_URL } from '../config/constants';
import { Storage } from '../utils/storage';

const apiClient: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 15000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Platform': 'android',
    'X-App-Scope': 'parent',
  },
});

// Request Interceptor: Attach Sanctum Bearer Token and Active Child Header
apiClient.interceptors.request.use(
  async (config: InternalAxiosRequestConfig) => {
    try {
      const token = await Storage.getToken();
      if (token && config.headers) {
        config.headers.Authorization = `Bearer ${token}`;
      }

      const activeChildId = await Storage.getActiveChildId();
      if (activeChildId && config.headers) {
        config.headers['X-Student-Id'] = activeChildId.toString();
      }
    } catch (error) {
      console.warn('Error reading token from storage:', error);
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response Interceptor: Catch 401 Unauthenticated & Network Errors
apiClient.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      console.warn('Parent authentication expired or invalid.');
      await Storage.clearSession();
    }
    return Promise.reject(error);
  }
);

export default apiClient;
