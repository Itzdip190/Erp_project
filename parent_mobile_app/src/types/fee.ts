export type FeePaymentStatus = 'paid' | 'unpaid' | 'partial' | 'overdue';

export interface FeeItem {
  id: number;
  title: string;
  amount: number;
  paid_amount: number;
  discount_amount?: number;
  fine_amount?: number;
  due_date: string;
  status: FeePaymentStatus;
}

export interface FeeInvoice {
  id: number;
  invoice_number: string;
  student_id: number;
  title: string;
  month_year?: string;
  total_amount: number;
  paid_amount: number;
  due_amount: number;
  due_date: string;
  status: FeePaymentStatus;
  items: FeeItem[];
  created_at: string;
}

export interface FeeSummary {
  total_fee: number;
  total_paid: number;
  total_due: number;
  upcoming_due_date?: string;
  currency_symbol: string;
  invoices: FeeInvoice[];
}

export interface PaymentReceipt {
  id: number;
  receipt_number: string;
  invoice_id: number;
  amount: number;
  payment_mode: string;
  transaction_id?: string;
  payment_date: string;
  receipt_url?: string;
}
