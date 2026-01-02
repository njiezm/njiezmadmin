<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facture;
use App\Models\Client;

class FactureController extends Controller
{
    public function index()
    {
        return view('facture');
    }

    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_address' => 'required|string',
            'reference' => 'required|string|max:50',
            'deadline' => 'required|date',
            'tva_rate' => 'required|numeric|min:0|max:100',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Création du client s'il n'existe pas
        $client = Client::firstOrCreate([
            'name' => $validated['client_name'],
            'address' => $validated['client_address'],
        ]);

        // Création de la facture
        $facture = Facture::create([
            'client_id' => $client->id,
            'reference' => $validated['reference'],
            'deadline' => $validated['deadline'],
            'tva_rate' => $validated['tva_rate'],
            'total_ht' => 0, // Sera calculé ci-dessous
            'total_tva' => 0, // Sera calculé ci-dessous
            'total_ttc' => 0, // Sera calculé ci-dessous
        ]);

        // Ajout des articles à la facture
        $totalHT = 0;
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['price'];
            $totalHT += $total;
            
            $facture->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $total,
            ]);
        }

        // Calcul des totaux
        $tvaAmount = $totalHT * ($validated['tva_rate'] / 100);
        $totalTTC = $totalHT + $tvaAmount;

        // Mise à jour des totaux
        $facture->update([
            'total_ht' => $totalHT,
            'total_tva' => $tvaAmount,
            'total_ttc' => $totalTTC,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Facture créée avec succès',
            'facture_id' => $facture->id,
        ]);
    }
}