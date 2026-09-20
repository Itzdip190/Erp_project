export interface NoticeItem {
  id: number;
  title: string;
  message: string;
  notice_date: string;
  publish_on?: string;
  created_by?: string;
  category?: string;
  priority: 'low' | 'normal' | 'high' | 'urgent';
  attachment_url?: string;
  is_read?: boolean;
}

export interface AppBanner {
  id: number;
  title: string;
  image_url: string;
  link_url?: string;
  action_type?: string;
}
