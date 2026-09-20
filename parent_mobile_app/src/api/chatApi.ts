import apiClient from './client';
import { API_ENDPOINTS } from '../config/constants';
import { ApiResponse } from '../types/auth';

export interface ChatMessage {
  id: number;
  sender_id: number;
  sender_type: 'parent' | 'teacher' | 'admin';
  sender_name: string;
  message: string;
  created_at: string;
  is_mine: boolean;
}

export const ChatApi = {
  // Get chat messages
  async getMessages(childId: number): Promise<ChatMessage[]> {
    const response = await apiClient.get<ApiResponse<ChatMessage[]>>(
      API_ENDPOINTS.CHAT_MESSAGES,
      { params: { student_id: childId } }
    );
    return response.data.data || [];
  },

  // Send message
  async sendMessage(childId: number, message: string): Promise<ChatMessage> {
    const response = await apiClient.post<ApiResponse<ChatMessage>>(
      API_ENDPOINTS.CHAT_SEND,
      { student_id: childId, message }
    );
    return response.data.data;
  },
};
