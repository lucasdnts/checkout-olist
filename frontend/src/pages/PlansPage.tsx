import { Link } from "react-router-dom";
import { usePlans } from "@/controllers/plans.controller";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter,
} from "@/components/ui/card";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Check } from "lucide-react";

const planFeatures: Record<string, string[]> = {
  BASIC_MONTHLY: [
    "Gestão de Pedidos", "Catálogo de Produtos Simples", "Link de Pagamento", "Suporte via Chat",
  ],
  PRO_MONTHLY: [
    "Tudo do plano Básico", "Controle de Estoque Avançado", "Módulo de Faturamento (Billing)", "Relatórios de Vendas", "Suporte Prioritário",
  ],
  PRO_YEARLY: [
    "Tudo do plano Profissional", "Módulo de Faturamento (Billing)", "Relatórios Avançados com IA", "Gerente de Conta Dedicado",
  ],
};

export default function PlansPage() {

  const { plans, error, isLoading } = usePlans();
 //console.log(plans)
  const formatPrice = (priceInCents: number) => (
    (priceInCents / 100).toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    })
  );

  const getPeriodicityText = (periodicity: string) => (
    periodicity === 'monthly' ? "/mês" : "/ano"
  );

  if (isLoading) {
    return <div className="p-4 text-center">Carregando planos...</div>;
  }

  if (error) {
    return (
      <div className="p-4 max-w-lg mx-auto">
        <Alert variant="destructive">
          <AlertTitle>Erro</AlertTitle>
          <AlertDescription>
            Não foi possível carregar os planos. Tente novamente.
          </AlertDescription>
        </Alert>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-white text-gray-900">
      <div className="container mx-auto p-4 py-12 md:py-20">
        
        <div className="text-center max-w-xl mx-auto mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mt-2">
            Planos para sua Necessidade
          </h1>
          <p className="text-lg text-gray-500 mt-4">
            Do ERP ao Faturamento. Encontre o plano perfeito para sua loja
            crescer com a Olist.
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
          
          {plans?.map((plan) => {
            const isHighlighted = plan.slug.includes("PRO_MONTHLY");
            const cardClasses = isHighlighted
              ? "bg-gray-900 text-white border-gray-900 shadow-xl"
              : "bg-white text-gray-900 border-gray-200";
            const buttonVariant = isHighlighted ? "secondary" : "default";

            return (
              <Card
                key={plan.id}
                className={`flex flex-col rounded-xl ${cardClasses}`}
              >
                <CardHeader className="pb-4">
                  <CardTitle className="text-2xl font-semibold">
                    {plan.name}
                  </CardTitle>
                  <CardDescription className={isHighlighted ? "text-gray-400" : ""}>
                    {plan.slug.includes("BASIC")
                      ? "Ideal para lojistas começando."
                      : "Para quem busca escalar."}
                  </CardDescription>
                </CardHeader>
                <CardContent className="flex-grow">
                  {/* Preço */}
                  <div className="mb-6">
                    <span className="text-5xl font-bold">
                      {formatPrice(plan.price_in_cents)}
                    </span>
                    <span className={`text-lg ml-1 ${isHighlighted ? "text-gray-400" : "text-gray-500"}`}>
                      {getPeriodicityText(plan.periodicity)}
                    </span>
                  </div>

                  {/* Features */}
                  <h3 className="font-semibold mb-4">Features</h3>
                  <ul className="space-y-3">
                    {(planFeatures[plan.slug] || []).map((feature) => (
                      <li key={feature} className="flex items-center">
                        <Check
                          className={`h-5 w-5 mr-2 ${isHighlighted ? "text-blue-400" : "text-blue-600"}`}
                        />
                        <span className={isHighlighted ? "text-gray-300" : ""}>
                          {feature}
                        </span>
                      </li>
                    ))}
                  </ul>
                </CardContent>
                <CardFooter>
                  <Button
                    asChild
                    variant={buttonVariant}
                    className="w-full text-lg py-6"
                  >
                    <Link to={`/checkout?plan_id=${plan.id}`}>
                      Assinar Agora
                    </Link>
                  </Button>
                </CardFooter>
              </Card>
            );
          })}
        </div>
      </div>
    </div>
  );
}