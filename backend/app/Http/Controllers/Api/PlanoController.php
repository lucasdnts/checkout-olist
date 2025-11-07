<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plano; 
use Illuminate\Http\Request;

class PlanoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        $planos = Plano::all(); // por enquanto, puxa todos
        return response()->json($planos);
    }

}