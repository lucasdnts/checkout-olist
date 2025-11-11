<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = Plan::all();
        return response()->json($plans); 
    }

    public function show($id)
    {
    $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['message' => 'Plano não encontrado'], 404);
        }
            return response()->json($plan);
    }
}