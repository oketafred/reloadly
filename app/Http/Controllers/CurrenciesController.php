<?php

namespace App\Http\Controllers;

use App\Models\Currency;

class CurrenciesController extends Controller
{
    public function getAll()
    {
        return response()->json(Currency::all());
    }
}
