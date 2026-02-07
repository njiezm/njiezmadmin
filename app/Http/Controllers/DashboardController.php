<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devis; // <-- Importer le modèle Devis
use App\Models\Facture; // <-- Importer le modèle Facture
use Carbon\Carbon; // <-- Importer Carbon pour gérer les dates facilement

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Compter les devis en attente (statut 'sent')
        $pendingDevisCount = Devis::where('status', 'sent')->count();

        // 2. Compter les factures payées (statut 'paid')
        $paidFacturesCount = Facture::where('status', 'paid')->count();

        // 3. Calculer le CA du mois en cours (somme des factures payées ce mois-ci)
        $monthlyRevenue = Facture::where('status', 'paid')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('total_ttc');

        // Formatter le CA pour l'affichage (ex: 8400.00 devient 8.4k€)
        if ($monthlyRevenue >= 1000) {
            $formattedMonthlyRevenue = number_format($monthlyRevenue / 1000, 1) . 'k€';
        } else {
            $formattedMonthlyRevenue = number_format($monthlyRevenue, 2) . '€';
        }

        // (Bonus) Récupérer les 5 derniers devis et factures pour un aperçu
        $latestDevis = Devis::with('client')->latest()->take(5)->get();
        $latestFactures = Facture::with('client')->latest()->take(5)->get();

        // Passer toutes les variables à la vue
        return view('dashboard', compact(
            'pendingDevisCount',
            'paidFacturesCount',
            'formattedMonthlyRevenue',
            'latestDevis',
            'latestFactures'
        ));
    }
}