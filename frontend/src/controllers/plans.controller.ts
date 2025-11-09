import useSWR from 'swr';
import { API_BASE_URL, fetcher } from '@/lib/api';
import type { Plan } from '@/lib/types';

export function usePlans() {
  const API_URL = `${API_BASE_URL}/plans`;

  const { data, error, isLoading } = useSWR<Plan[]>(API_URL, fetcher);

  return {
    plans: data,
    error,
    isLoading,
  };
}