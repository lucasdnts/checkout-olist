import { useParams, Link } from "react-router-dom";
import { useSubscriptionDetails } from "@/controllers/subscription.contoller";
import {
  Card,
  CardHeader,
  CardTitle,
  CardContent,
  CardDescription,
} from "@/components/ui/card";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";
import { CheckCircle, XCircle } from "lucide-react";
export default function ConfirmationPage() {
  const { id } = useParams<{ id: string }>();
  const { subscription, error, isLoading } = useSubscriptionDetails(id || null);

  const formatPrice = (priceInCents: number) => (
    (priceInCents / 100).toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    })
  );

  if (isLoading) {
    return <div className="p-4 text-center">Carregando confirmação...</div>;
  }

  if (error || !subscription) {
    return (
      <div className="container max-w-lg mx-auto p-4 py-12">
        <Alert variant="destructive">
          <XCircle className="h-4 w-4" />
          <AlertTitle>Erro</AlertTitle>
          <AlertDescription>
            Não foi possível carregar os dados da sua assinatura.
          </AlertDescription>
        </Alert>
        <Button asChild variant="link" className="mt-4">
          <Link to="/">Voltar aos planos</Link>
        </Button>
      </div>
    );
  }

  const transaction = subscription.transactions?.[0];

  return (
    <div className="container max-w-2xl mx-auto p-4 py-12">
      <Card className="shadow-lg">
        <CardHeader className="items-center text-center">
          <div className="flex justify-center">
            <CheckCircle className="h-16 w-16 text-green-600 mb-4" />
          </div>
          <CardTitle className="text-3xl">Pagamento Aprovado!</CardTitle>
          <CardDescription className="text-lg">
            Sua assinatura do {subscription.plan.name} está ativa.
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <Separator />
          <h3 className="font-semibold">Resumo da Compra</h3>

          <div className="flex justify-between">
            <span className="text-muted-foreground">E-mail</span>
            <span className="font-medium">{subscription.user_email}</span>
          </div>
          <div className="flex justify-between">
            <span className="text-muted-foreground">Plano</span>
            <span className="font-medium">{subscription.plan.name}</span>
          </div>
          <div className="flex justify-between">
            <span className="text-muted-foreground">Valor Cobrado</span>
            <span className="font-medium">
              {transaction
                ? formatPrice(transaction.amount_paid_in_cents)
                : 'N/A'}
            </span>
          </div>
          <div className="flex justify-between">
            <span className="text-muted-foreground">ID da Transação</span>
            <span className="font-medium text-sm text-gray-600">
              {transaction?.gateway_transaction_id || 'N/A'}
            </span>
          </div>

          <Button asChild className="w-full mt-6">
            <Link to="/">Voltar ao início</Link>
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}