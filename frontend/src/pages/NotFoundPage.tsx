import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { AlertTriangle } from "lucide-react";

export default function NotFoundPage() {
  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-white text-center p-4">
      <AlertTriangle className="h-20 w-20 text-yellow-500 mb-6" />
      <h1 className="text-5xl font-bold text-gray-900 mb-4">404</h1>
      <h2 className="text-2xl font-semibold text-gray-700 mb-4">
        Página Não Encontrada
      </h2>
      <p className="text-lg text-gray-500 mb-8 max-w-md">
        Desculpe, a página que você está procurando não existe ou foi movida.
      </p>
      <Button asChild size="lg">
        <Link to="/">Voltar para o Início</Link>
      </Button>
    </div>
  );
}