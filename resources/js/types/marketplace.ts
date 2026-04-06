declare global {
    interface Window {
        desksConfig: {
            apiUrl: string;
            siteKey: string;
            locale: string;
        };
    }
}

export interface AuthUser {
  id: number;
  name: string;
  email: string;
  role: 'client' | 'consultant' | null;
  avatar_url: string | null;
}

export interface PageProps {
  auth: {
    user: AuthUser | null;
  };
  flash: {
    status?: string;
    success?: string;
    error?: string;
  };
  [key: string]: unknown;
}

export interface Specialization {
  id: number;
  name_en: string;
  name_ar: string;
  slug: string;
}

export interface ConsultantCard {
  id: number;
  slug: string;
  name: string;
  bio_en: string;
  bio_ar: string | null;
  years_experience: number;
  hourly_rate: string;
  languages: string[];
  avatar: string | null;
  avatar_url: string | null;
  timezone: string;
  average_rating: string;
  total_reviews: number;
  total_bookings: number;
  specializations: Specialization[];
  user: {
    id: number;
    name: string;
  };
}

export interface ConsultantDetail extends ConsultantCard {
  response_time_hours: number;
  calendly_event_type_url: string | null;
}

export interface ReviewItem {
  id: number;
  rating: number;
  comment: string | null;
  reviewer_name: string;
  created_at: string;
}

export interface BookingItem {
  id: number;
  reference: string;
  calendly_event_uuid: string | null;
  meeting_url: string | null;
  start_at: string;
  end_at: string;
  duration_minutes: number;
  status: string;
  status_label: string;
  total_amount: string;
  commission_amount?: string;
  consultant_amount?: string;
  client_notes: string | null;
  cancellation_reason: string | null;
  is_refund_eligible: boolean;
  has_review: boolean;
  consultant?: {
    name: string;
    slug: string;
    avatar: string | null;
  };
  client?: {
    name: string;
    email: string;
  };
  created_at: string;
}

export interface PayoutItem {
  id: number;
  reference: string;
  amount: string;
  currency: string;
  status: string;
  status_label: string;
  transfer_reference: string | null;
  admin_notes: string | null;
  created_at: string;
  transferred_at: string | null;
  has_receipt: boolean;
}

export interface BalanceSummary {
  available: number;
  pending: number;
  total_earned: number;
  total_paid_out: number;
  total_in_process: number;
}

export interface BankDetails {
  bank_name: string | null;
  bank_account_holder_name: string | null;
  iban: string | null;
}

export interface PaginatedData<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
  first_page_url: string | null;
  last_page_url: string | null;
  prev_page_url: string | null;
  next_page_url: string | null;
  path: string;
}
