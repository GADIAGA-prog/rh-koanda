<?php

namespace App\Http\Controllers;

use App\Models\Filiale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FilialeController extends Controller
{
    public function index()
    {
        $filiales = Filiale::withCount('employes')->orderBy('nom')->get();

        return view('filiales.index', compact('filiales'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(['super-admin', 'drh-groupe']), 403);

        $donnees = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('filiales', 'code')],
            'nom' => ['required', 'string', 'max:255'],
            'domaine' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
        ]);

        Filiale::create($donnees);

        return back()->with('succes', 'Filiale ajoutée.');
    }
}
