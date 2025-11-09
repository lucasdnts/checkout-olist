import useSWR from 'swr';
import { API_BASE_URL, fetcher } from '@/lib/api';
import type { Plan } from '@/lib/types';

/**
 * @param plan_id id plano a ser buscado
 */
export function usePlanDetails(plan_id: string | null) {

  const API_URL = plan_id ? `${API_BASE_URL}/plans/${plan_id}` : null;
  const { data, error, isLoading } = useSWR<Plan>(API_URL, fetcher);

  return {
    plan: data,
    error,
    isLoading,
  };
}