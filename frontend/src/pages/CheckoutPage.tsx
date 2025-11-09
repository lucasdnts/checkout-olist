import { useState } from "react";
import { useSearchParams, useNavigate } from "react-router-dom";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { v4 as uuidv4 } from "uuid";
import { usePlanDetails } from "@/controllers/PlanDetails.controller";
import { validateCoupon, submitCheckout } from "@/lib/api";
import type { CouponValidationResponse } from "@/lib/types";
import { Button } from "@/components/ui/button";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import {
  Card,
  CardHeader,
  CardTitle,
  CardContent,
  CardFooter,
} from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { toast } from "sonner";

const FormSchema = z.object({
  email: z.string()
    .min(1, { message: "E-mail é obrigatório." })
    .email({ message: "Por favor, insira um e-mail válido." }),
  
  card_holder: z.string()
    .min(3, { message: "Nome no cartão é obrigatório." }),
  
  card_number: z.string()
    .min(16, { message: "Deve ter 16 dígitos." })
    .max(16, { message: "Deve ter 16 dígitos." }),
    
  card_expiry: z.string()
    .min(5, { message: "Use MM/AA" })
    .regex(/^(0[1-9]|1[0-2])\/?([0-9]{2})$/, {
      message: "Data inválida. Use MM/AA",
    }),
    
  card_cvc: z.string()
    .min(3, { message: "CVC deve ter 3 ou 4 dígitos." })
    .max(4, { message: "CVC deve ter 3 ou 4 dígitos." }),
    
  coupon_code: z.string().optional(),
});


type OrderSummary = {
  subtotal: number;
  discount: number;
  total: number;
};

export default function CheckoutPage() {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const planId = searchParams.get("plan_id");
  const { plan, isLoading: isLoadingPlan } = usePlanDetails(planId);

  const [isProcessing, setIsProcessing] = useState(false);
  const [isCheckingCoupon, setIsCheckingCoupon] = useState(false);
  const [couponCode, setCouponCode] = useState("");
  const [summary, setSummary] = useState<OrderSummary | null>(null);

  const form = useForm<z.infer<typeof FormSchema>>({
    resolver: zodResolver(FormSchema),
    defaultValues: {
      email: "",
      card_holder: "",
      card_number: "",
      card_expiry: "",
      card_cvc: "",
      coupon_code: "", // Garantir que não seja 'undefined'
    },
  });

  const handleApplyCoupon = async () => {
    if (!plan) {
       toast.error("Erro", { description: "Primeiro, selecione um plano." });
       return;
    }
    const code = form.getValues("coupon_code");
    if (!code) {
      toast.error("Erro", { description: "Digite um código de cupom." });
      return;
    }

    setIsCheckingCoupon(true);
    try {
      const res: CouponValidationResponse = await validateCoupon(code, plan.id);

      if (res.valid && res.values) {
        setSummary(res.values);
        setCouponCode(code);
        toast.success("Sucesso!", {
          description: res.message,
        });
      } else {

        toast.warning("Atenção", {
          description: res.message,
        });
      }
    } catch (err) {
      toast.error("Erro", { description: "Não foi possível validar o cupom." });
    } finally {
      setIsCheckingCoupon(false);
    }
  };

  async function onSubmit(data: z.infer<typeof FormSchema>) {
    if (!plan) return;
    setIsProcessing(true);

    try {
      const payload = {
        ...data,
        plan_id: plan.id,
        coupon_code: couponCode || null,
        idempotency_key: uuidv4(),
      };
      const subscription = await submitCheckout(payload);
      toast.success("Pagamento Aprovado!", {
        description: "Sua assinatura está ativa.",
      });
      navigate(`/confirmation/${subscription.id}`);

    } catch (err: any) {
      toast.error("Erro no Pagamento", {
        description: err.message || "Não foi possível processar seu pagamento.",
      });
    } finally {
      setIsProcessing(false);
    }
  }
  
  const formatPrice = (priceInCents: number) => (
    (priceInCents / 100).toLocaleString("pt-BR", { style: "currency", currency: "BRL" })
  );

  if (isLoadingPlan) {
    return <div className="p-4 text-center">Carregando checkout...</div>;
  }
  
  if (!plan) {
    return <div className="p-4 text-center">Plano não encontrado.</div>;
  }

  const displaySummary = summary || {
    subtotal: plan.price_in_cents,
    discount: 0,
    total: plan.price_in_cents,
  };

  return (
    <div className="container mx-auto max-w-4xl p-4 py-12">
      <Form {...form}>
        <form onSubmit={form.handleSubmit(onSubmit)}>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {/* Coluna Formulário de Pagamento */}
            <Card>
              <CardHeader>
                <CardTitle>Pagamento</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <FormField
                  control={form.control}
                  name="email"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>E-mail</FormLabel>
                      <FormControl>
                        <Input placeholder="seu@email.com" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                
                <FormField
                  control={form.control}
                  name="card_holder"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Nome no Cartão</FormLabel>
                      <FormControl>
                        <Input placeholder="Nome Completo" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="card_number"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Número do Cartão</FormLabel>
                      <FormControl>
                        <Input placeholder="0000 0000 0000 0000" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <div className="flex gap-4">
                  <FormField
                    control={form.control}
                    name="card_expiry"
                    render={({ field }) => (
                      <FormItem className="flex-1">
                        <FormLabel>Validade</FormLabel>
                        <FormControl>
                          <Input placeholder="MM/AA" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <FormField
                    control={form.control}
                    name="card_cvc"
                    render={({ field }) => (
                      <FormItem className="flex-1">
                        <FormLabel>CVC</FormLabel>
                        <FormControl>
                          <Input placeholder="123" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </div>
              </CardContent>
            </Card>

            {/* Coluna Resumo do Pedido */}
            <Card className="flex flex-col">
              <CardHeader>
                <CardTitle>Resumo do Pedido</CardTitle>
              </CardHeader>
              <CardContent className="flex-grow space-y-4">
                {/* Plano Selecionado */}
                <div className="flex justify-between items-center">
                  <span className="text-muted-foreground">{plan.name}</span>
                  <span className="font-semibold">{formatPrice(plan.price_in_cents)}</span>
                </div>

                <Separator />
                
                {/* Cupom */}
                <FormField
                  control={form.control}
                  name="coupon_code"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Cupom de Desconto</FormLabel>
                      <div className="flex gap-2">
                        <FormControl>
                          <Input placeholder="EX: OFF10" {...field} disabled={!!couponCode} />
                        </FormControl>
                        <Button
                          type="button"
                          variant="outline"
                          onClick={handleApplyCoupon}
                          disabled={isCheckingCoupon || !!couponCode}
                        >
                          {isCheckingCoupon ? "..." : "Aplicar"}
                        </Button>
                      </div>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <Separator />

                {/* Resumo de Valores */}
                <div className="space-y-2">
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">Subtotal</span>
                    <span>{formatPrice(displaySummary.subtotal)}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">Desconto</span>
                    <span>- {formatPrice(displaySummary.discount)}</span>
                  </div>
                  <div className="flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span>{formatPrice(displaySummary.total)}</span>
                  </div>
                </div>

              </CardContent>
              <CardFooter>
                <Button
                  type="submit"
                  className="w-full text-lg py-6"
                  disabled={isProcessing}
                >
                  {isProcessing ? "Processando..." : `Pagar ${formatPrice(displaySummary.total)}`}
                </Button>
              </CardFooter>
            </Card>
            
          </div>
        </form>
      </Form>
    </div>
  );
}