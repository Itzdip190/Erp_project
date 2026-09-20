import { AttendanceStatus } from '../types/attendance';
import { FeePaymentStatus } from '../types/fee';

export const formatCurrency = (amount: number, currency: string = '₹'): string => {
  if (isNaN(amount)) return `${currency}0.00`;
  return `${currency}${amount.toLocaleString('en-IN', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })}`;
};

export const formatDate = (dateString?: string): string => {
  if (!dateString) return '';
  try {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('en-GB', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    });
  } catch {
    return dateString;
  }
};

export const formatTime = (timeString?: string): string => {
  if (!timeString) return '';
  try {
    // If it's already HH:MM format
    if (/^\d{2}:\d{2}/.test(timeString)) {
      const [hours, minutes] = timeString.split(':');
      const h = parseInt(hours, 10);
      const ampm = h >= 12 ? 'PM' : 'AM';
      const formattedHour = h % 12 || 12;
      return `${formattedHour}:${minutes} ${ampm}`;
    }
    const date = new Date(timeString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  } catch {
    return timeString;
  }
};

export const getAttendanceColor = (status: AttendanceStatus): { bg: string; text: string; label: string } => {
  switch (status) {
    case 'present':
      return { bg: '#d1fae5', text: '#065f46', label: 'Present' };
    case 'absent':
      return { bg: '#fee2e2', text: '#991b1b', label: 'Absent' };
    case 'late':
      return { bg: '#fef3c7', text: '#92400e', label: 'Late' };
    case 'half_day':
      return { bg: '#ffedd5', text: '#9a3412', label: 'Half Day' };
    case 'holiday':
      return { bg: '#e0f2fe', text: '#075985', label: 'Holiday' };
    case 'leave':
      return { bg: '#ede9fe', text: '#5b21b6', label: 'On Leave' };
    default:
      return { bg: '#f1f5f9', text: '#475569', label: status };
  }
};

export const getFeeStatusColor = (status: FeePaymentStatus): { bg: string; text: string; label: string } => {
  switch (status) {
    case 'paid':
      return { bg: '#d1fae5', text: '#065f46', label: 'Paid' };
    case 'partial':
      return { bg: '#fef3c7', text: '#92400e', label: 'Partial' };
    case 'unpaid':
      return { bg: '#fee2e2', text: '#991b1b', label: 'Unpaid' };
    case 'overdue':
      return { bg: '#7f1d1d', text: '#ffffff', label: 'Overdue' };
    default:
      return { bg: '#f1f5f9', text: '#475569', label: status };
  }
};
