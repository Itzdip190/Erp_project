import apiClient from './client';
import { API_ENDPOINTS } from '../config/constants';
import { ApiResponse } from '../types/auth';
import { FeeSummary, PaymentReceipt } from '../types/fee';

export const FeesApi = {
  // Get Fee Overview & Invoices for active child
  async getFees(childId: number): Promise<FeeSummary> {
    const response = await apiClient.get<ApiResponse<FeeSummary>>(API_ENDPOINTS.FEES, {
      params: { student_id: childId },
    });
    return response.data.data;
  },

  // Get receipt details for invoice
  async getReceipt(invoiceId: number): Promise<PaymentReceipt> {
    const response = await apiClient.get<ApiResponse<PaymentReceipt>>(
      API_ENDPOINTS.FEE_RECEIPT(invoiceId)
    );
    return response.data.data;
  },
};
