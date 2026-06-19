<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>État RH consolidé — Koanda Groupe</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2933; margin: 0; padding: 32px; font-size: 12px; }
        .entete { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 3px solid #5D9730; padding-bottom: 12px; margin-bottom: 18px; }
        .marque { font-size: 20px; font-weight: 800; color: #07120B; }
        .marque span { color: #70B339; }
        h1 { font-size: 15px; margin: 0; }
        .sous { color: #6b7280; font-size: 11px; }
        .cartes { display: flex; gap: 14px; margin-bottom: 16px; }
        .carte { flex: 1; border: 1px solid #DDE1DA; border-radius: 8px; padding: 10px 14px; }
        .carte span { display: block; color: #6b7280; font-size: 10px; text-transform: uppercase; }
        .carte strong { font-size: 18px; color: #07120B; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 7px 10px; border-bottom: 1px solid #eef0f2; }
        th { background: #f3f4f6; text-align: left; font-size: 10px; text-transform: uppercase; color: #6b7280; }
        td.num, th.num { text-align: right; }
        tfoot td { font-weight: 700; border-top: 2px solid #DDE1DA; }
        .pied { margin-top: 24px; font-size: 10px; color: #9ca3af; border-top: 1px solid #eee; padding-top: 8px; }
        @media print { body { padding: 0; } .noprint { display: none; } }
    </style>
</head>
<body>
    <div class="entete">
        <div>
            <div class="marque">K<span>OANDA</span> GROUPE</div>
            <div class="sous">Système RH — état consolidé par filiale</div>
        </div>
        <div style="text-align:right">
            <h1>Rapport RH consolidé</h1>
            <div class="sous">Généré le {{ $genereLe->format('d/m/Y à H:i') }}</div>
        </div>
    </div>

    <div class="cartes">
        <div class="carte"><span>Effectif total</span><strong>{{ number_format($totaux['effectif'], 0, ',', ' ') }}</strong></div>
        <div class="carte"><span>Masse salariale (mois)</span><strong>{{ number_format($totaux['masse_salariale'], 0, ',', ' ') }} XOF</strong></div>
        <div class="carte"><span>Contrats à renouveler</span><strong>{{ $totaux['contrats_a_renouveler'] }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Filiale</th>
                <th class="num">Effectif</th>
                <th class="num">Absentéisme</th>
                <th class="num">Retards</th>
                <th class="num">Turnover</th>
                <th class="num">Masse salariale</th>
                <th class="num">Contrats à renouveler</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stats as $s)
                <tr>
                    <td>{{ $s['filiale'] }} ({{ $s['code'] }})</td>
                    <td class="num">{{ $s['effectif'] }}</td>
                    <td class="num">{{ $s['taux_absenteisme'] }} %</td>
                    <td class="num">{{ $s['taux_retard'] }} %</td>
                    <td class="num">{{ $s['turnover'] }} %</td>
                    <td class="num">{{ number_format($s['masse_salariale'], 0, ',', ' ') }}</td>
                    <td class="num">{{ $s['contrats_a_renouveler'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total groupe</td>
                <td class="num">{{ $stats->sum('effectif') }}</td>
                <td class="num"></td>
                <td class="num"></td>
                <td class="num"></td>
                <td class="num">{{ number_format($stats->sum('masse_salariale'), 0, ',', ' ') }}</td>
                <td class="num">{{ $stats->sum('contrats_a_renouveler') }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="pied">Document généré automatiquement. Périmètre limité aux filiales accessibles de l'utilisateur. Masse salariale = somme des nets de la période en cours.</p>
    <button class="noprint" onclick="window.print()" style="margin-top:18px;padding:9px 16px;background:#70B339;color:#fff;border:0;border-radius:8px;font-weight:600;cursor:pointer">Imprimer / PDF</button>
</body>
</html>
