import apiClient from './client';
import { API_ENDPOINTS } from '../config/constants';
import { ApiResponse } from '../types/auth';
import { StudentChild, StudentDocumentItem } from '../types/student';

export const ChildrenApi = {
  // Get all children linked to this parent
  async getChildren(): Promise<StudentChild[]> {
    const response = await apiClient.get<ApiResponse<StudentChild[]>>(API_ENDPOINTS.CHILDREN);
    return response.data.data || [];
  },

  // Get full child profile
  async getChildProfile(childId: number): Promise<StudentChild> {
    const response = await apiClient.get<ApiResponse<StudentChild>>(
      API_ENDPOINTS.CHILD_PROFILE(childId)
    );
    return response.data.data;
  },

  // Get child uploaded documents
  async getChildDocuments(childId: number): Promise<StudentDocumentItem[]> {
    const response = await apiClient.get<ApiResponse<StudentDocumentItem[]>>(
      API_ENDPOINTS.CHILD_DOCUMENTS(childId)
    );
    return response.data.data || [];
  },
};
