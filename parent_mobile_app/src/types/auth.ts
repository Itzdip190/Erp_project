import { StudentChild } from './student';

export interface ParentUser {
  id: number;
  name: string;
  email: string;
  phone?: string;
  role: 'parent' | 'student';
  school_id: number;
  is_active: boolean;
  avatar?: string;
  created_at?: string;
}

export interface SchoolInfo {
  id: number;
  name: string;
  code: string;
  logo?: string;
  phone?: string;
  email?: string;
  address?: string;
  status: string;
}

export interface LoginCredentials {
  school_code: string;
  email: string;
  password: string;
  device_name?: string;
  fcm_token?: string;
}

export interface OtpSendRequest {
  school_code: string;
  phone_or_email: string;
}

export interface OtpVerifyRequest {
  school_code: string;
  phone_or_email: string;
  otp: string;
  device_name?: string;
}

export interface AuthResponseData {
  token: string;
  user: ParentUser;
  school: SchoolInfo;
  children: StudentChild[];
  permissions?: string[];
}

export interface ApiResponse<T> {
  success: boolean;
  message?: string;
  data: T;
}
