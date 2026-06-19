<?php

namespace App\Http\Controllers;

use App\Http\Requests\FonctionRequest;
use App\Models\Fonction;

class FonctionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Fonction::class, 'fonction');
    }

    public function index()
    {
        return view('organisation.fonctions.index', [
            'fonctions' => Fonction::orderBy('libelle')->paginate(20),
        ]);
    }

    public function store(FonctionRequest $request)
    {
        Fonction::create($request->validated());

        return redirect()->route('fonctions.index')->with('succes', 'Fonction créée.');
    }

    public function edit(Fonction $fonction)
    {
        return view('organisation.fonctions.edit', compact('fonction'));
    }

    public function update(FonctionRequest $request, Fonction $fonction)
    {
        $fonction->update($request->validated());

        return redirect()->route('fonctions.index')->with('succes', 'Fonction mise à jour.');
    }

    public function destroy(Fonction $fonction)
    {
        $fonction->delete();

        return redirect()->route('fonctions.index')->with('succes', 'Fonction supprimée.');
    }
}
