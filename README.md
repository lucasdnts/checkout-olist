# Teste Técnico Olist - Checkout Full Stack

Este repositório contém o desafio técnico de Desenvolvedor Full Stack da Olist. O projeto simula um fluxo de checkout completo, desde a seleção de planos até a aplicação de cupons e pagamento.

A aplicação é 100% containerizada com Docker, permitindo que todo o ambiente (Frontend, Backend, Nginx e Banco de Dados) seja iniciado com um único comando.

-----

## Features

  * **Backend** em PHP/Laravel para gerenciar planos, cupons e assinaturas.
  * **Frontend** em React (Vite + TS) com componentes Shadcn.
  * **Fluxo de Checkout Completo** com validação de cupom em tempo real e resumo de valores.
  * **Gateway de Pagamento Simulado** (`GatewayService`) com regras de negócio.
  * **Idempotência** no endpoint de checkout para prevenir cobranças duplicadas.
  * **Testes Unitários** no ponto crítico do sistema (cálculo de descontos no `CouponService`).
  * **Logs Estruturados** para facilitar a depuração de falhas no checkout.

-----

## Stack de Tecnologias

  * **Backend:** Laravel, PostgreSQL
  * **Frontend:** React, Vite, TS, Tailwind, Shadcn
  * **Infraestrutura:** Docker, Nginx

-----

## Como Rodar o Projeto

Este projeto é 100% containerizado. O único pré-requisito é o **Docker** instalado.

### 1\. Clonar o Repositório

```bash
git clone https://github.com/lucasdnts/checkout-olist.git
cd checkout-olist
```

### 2\. Configurar o Backend

O Laravel precisa de um arquivo `.env` e uma chave de aplicação para rodar.

**a. Copie o arquivo de ambiente:**

```bash
cp backend/.env.example backend/.env
```

**b. Gere a chave da aplicação (via Docker):**
*(Rode este comando via docker ou na pasta backend: php artisan key:generate)*

```bash
docker-compose run --rm backend php artisan key:generate
```

### 3\. Subir os Contêineres

```bash
docker-compose up --build -d
```

### 4\. Rodar as Migrações do Banco

Com os contêineres rodando, execute as migrações e seeders para popular o banco com os planos e cupons de teste.

```bash
docker-compose exec backend php artisan migrate:fresh --seed
```

### 5\. Acessar o Projeto

  * **Frontend (Aplicação):** [**http://localhost:5173**]
  * **Backend (API):** `http://localhost:5173/api` (acessível via proxy)

-----

## Testes Unitários

Testes unitários:

```bash
docker-compose exec backend php artisan test
```

-----

## Documentação da API (Postman)

Importe o arquivo `olist-checkout.postman_collection.json` (incluído na raiz deste projeto) diretamente no seu Postman.

-----

## Decisões de Arquitetura e Trade-offs

O principal *trade-off* do projeto foi priorizar a velocidade de entrega. As ferramentas foram escolhidas para maximizar a produtividade, já que o tempo de desenvolvimento foi limitado ao período noturno, em paralelo com um emprego em tempo integral.

  * **Laravel**
    A escolha do Laravel foi baseada em sua arquitetura robusta "pronta para uso". O Eloquent ORM, o sistema de rotas, o Artisan e a configuração de banco de dados nativa aceleram o desenvolvimento de uma API segura e escalável, permitindo focar na lógica de negócios.

  * **Shadcn/ui**
    O Shadcn permitiu a construção de uma UI complexa (formulários, validação em tempo real, toasts) de forma rápida e previsível. Em vez de gastar tempo escrevendo do zero, o foco foi direcionado para a lógica de estado, gerenciamento de dados e a integração com a API.

  * **O Trade-off do `GatewayService`**
    O teste exige um "endpoint local mockado". A primeira tentativa foi criar um endpoint `/api/gateway` real e chamá-lo via HTTP. Só que isso causou falhas por conta da natureza single-threaded do `php artisan serve`.

    A decisão foi refatorar essa lógica para um `GatewayService` injetado no `CheckoutController`.
-----

## Nota sobre o Uso de IA (Gemini-CLI)

Para acelerar o desenvolvimento e focar nos problemas de arquitetura e lógica de negócios, utilizei o Gemini CLI para auxilio em algumas funções e ajustes de bugs.

O uso foi fundamental nas seguintes etapas:

  * **Lógica de Negócios:** Refinamento da lógica complexa de validação de cupons e do cálculo de descontos half-up no `CouponService`.
  * **Design de Sistema:** Auxílio no design e implementação da estratégia de **idempotência** (`idempotency_key`) no `CheckoutController`.
  * **Bugs:** Ajuda na identificação de bugs complexos.
  * **Configuração Docker:** Estruturação da configuração completa do `docker-compose.yml` e dos `Dockerfiles` para o ambiente full stack, incluindo a complexa configuração do Nginx como proxy reverso para o frontend.
  * **Documentação:** Geração da overview na coleção do Postman.