import useSWR from 'swr';
import { API_BASE_URL, fetcher } from '@/lib/api';
import type { Subscription } from '@/lib/types';

export function useSubscriptionDetails(subscriptionId: string | null) {
  const API_URL = subscriptionId 
    ? `${API_BASE_URL}/subscriptions/${subscriptionId}` 
    : null;

  const { data, error, isLoading } = useSWR<Subscription>(API_URL, fetcher, {
    revalidateOnFocus: false,
    revalidateOnReconnect: false,
  });

  return {
    subscription: data,
    error,
    isLoading,
  };
}