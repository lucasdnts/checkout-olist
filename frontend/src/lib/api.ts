import type { CouponValidationResponse, Subscription } from "./types";

export const API_BASE_URL = "/api";


export const fetcher = async (url: string) => {
  const res = await fetch(url);
  if (!res.ok) {
    const errorData = await res.json();
    const error = new Error(errorData.message || 'Ocorreu um erro ao buscar os dados.');
    throw error;
  }
  return res.json();
};



 // valida um cupom
export async function validateCoupon(code: string, plan_id: number): Promise<CouponValidationResponse> {
  const res = await fetch(`${API_BASE_URL}/coupons/validate`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ code, plan_id }),
  });
  return res.json();
}

 //formulário de checkout
export async function submitCheckout(payload: any): Promise<Subscription> {
  const res = await fetch(`${API_BASE_URL}/checkout`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify(payload),
  });

  if (!res.ok) {
    const errorData = await res.json();
    throw new Error(errorData.message || 'Falha no checkout');
  }

  return res.json();
}