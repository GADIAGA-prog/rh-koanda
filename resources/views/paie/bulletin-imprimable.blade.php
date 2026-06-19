<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bulletin {{ $bulletin->periode }} — {{ $bulletin->employe->nom_complet }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2933; margin: 0; padding: 32px; font-size: 13px; }
        .entete { display: flex; justify-content: space-between; border-bottom: 3px solid #5D9730; padding-bottom: 14px; margin-bottom: 18px; }
        .marque { font-size: 20px; font-weight: 800; color: #07120B; }
        .marque span { color: #70B339; }
        .sous { color: #6b7280; font-size: 12px; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 7px 10px; text-align: left; }
        thead th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
        tbody tr { border-bottom: 1px solid #eef0f2; }
        .num { text-align: right; }
        .gain { color: #047857; }
        .ret { color: #be123c; }
        .totaux { margin-top: 16px; width: 100%; }
        .net { background: #E9F4DF; border: 1px solid #70B339; }
        .net td { font-size: 16px; font-weight: 700; color: #07120B; }
        .infos { display: flex; gap: 40px; margin: 12px 0 4px; }
        .infos div span { display: block; color: #6b7280; font-size: 11px; }
        .pied { margin-top: 30px; font-size: 11px; color: #9ca3af; border-top: 1px solid #eee; padding-top: 10px; }
        @media print { body { padding: 0; } .noprint { display: none; } }
    </style>
</head>
<body>
    <div class="entete">
        <div>
            <div class="marque">K<span>OANDA</span> GROUPE</div>
            <div class="sous">{{ $bulletin->filiale->nom }}</div>
        </div>
        <div style="text-align:right">
            <h1>Bulletin de paie</h1>
            <div class="sous">Période : {{ \Illuminate\Support\Carbon::parse($bulletin->periode.'-01')->translatedFormat('F Y') }}</div>
        </div>
    </div>

    <div class="infos">
        <div><span>Employé</span><strong>{{ $bulletin->employe->nom_complet }}</strong></div>
        <div><span>Matricule</span><strong>{{ $bulletin->employe->matricule ?? '—' }}</strong></div>
        <div><span>Poste</span><strong>{{ $bulletin->employe->poste->intitule ?? '—' }}</strong></div>
    </div>

    <table>
        <thead>
            <tr><th>Libellé</th><th class="num">Base</th><th class="num">Taux</th><th class="num">Montant</th></tr>
        </thead>
        <tbody>
            @foreach ($bulletin->lignes as $l)
                <tr>
                    <td>{{ $l->libelle }}</td>
                    <td class="num">{{ number_format($l->base, 0, ',', ' ') }}</td>
                    <td class="num">{{ $l->taux ? $l->taux.' %' : '' }}</td>
                    <td class="num {{ $l->type->value === 'gain' ? 'gain' : 'ret' }}">{{ $l->type->value === 'gain' ? '' : '−' }}{{ number_format($l->montant, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totaux">
        <tr><td>Salaire brut</td><td class="num">{{ number_format($bulletin->salaire_brut, 0, ',', ' ') }} XOF</td></tr>
        <tr><td>Total cotisations</td><td class="num">{{ number_format($bulletin->total_cotisations, 0, ',', ' ') }} XOF</td></tr>
        <tr><td>Total retenues</td><td class="num">{{ number_format($bulletin->total_retenues, 0, ',', ' ') }} XOF</td></tr>
        <tr class="net"><td>NET À PAYER</td><td class="num">{{ number_format($bulletin->net_a_payer, 0, ',', ' ') }} XOF</td></tr>
        <tr><td>Coût employeur</td><td class="num">{{ number_format($bulletin->cout_employeur, 0, ',', ' ') }} XOF</td></tr>
    </table>

    <p class="pied">Document généré le {{ now()->format('d/m/Y') }}. Les taux appliqués (CNSS, IUTS…) sont paramétrables et doivent être validés par un comptable.</p>

    <button class="noprint" onclick="window.print()" style="margin-top:20px;padding:10px 18px;background:#70B339;color:#fff;border:0;border-radius:8px;font-weight:600;cursor:pointer">Imprimer</button>
</body>
</html>
