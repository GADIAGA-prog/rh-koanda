<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Exports CSV (ouvrables directement dans Excel, BOM UTF-8 pour les accents).
 * Pour des fichiers .xlsx natifs, ajouter maatwebsite/excel ultérieurement.
 */
class ExportService
{
    public function csv(string $nomFichier, array $entetes, iterable $lignes): StreamedResponse
    {
        return response()->streamDownload(function () use ($entetes, $lignes) {
            $sortie = fopen('php://output', 'w');
            // BOM UTF-8 pour qu'Excel affiche correctement les accents.
            fwrite($sortie, "\xEF\xBB\xBF");
            fputcsv($sortie, $entetes, ';');
            foreach ($lignes as $ligne) {
                fputcsv($sortie, $ligne, ';');
            }
            fclose($sortie);
        }, $nomFichier, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
