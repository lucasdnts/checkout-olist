
export interface Plan {
  id: number;
  name: string;
  slug: string;
  price_in_cents: number;
  periodicity: 'monthly' | 'yearly';
}

export interface CouponValidationResponse {
  valid: boolean;
  message: string;
  values?: {
    subtotal: number;
    discount: number;
    total: number;
  };
}

export interface Transaction {
  id: number;
  subscription_id: number;
  gateway_status: string;
  amount_paid_in_cents: number;
  gateway_transaction_id: string;
}

export interface Subscription {
  id: number;
  plan_id: number;
  plan_price_in_cents: number;
  user_email: string;
  status: string;
  plan: Plan;
  transactions: Transaction[];
}