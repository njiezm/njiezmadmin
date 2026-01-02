<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devis;
use App\Models\Client;

class DevisController extends Controller
{
    public function index()
    {
        return view('devis');
    }

    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_address' => 'required|string',
            'client_siret' => 'nullable|string',
            'reference' => 'required|string|max:50',
            'date' => 'required|date',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Création du client s'il n'existe pas
        $client = Client::firstOrCreate([
            'name' => $validated['client_name'],
            'address' => $validated['client_address'],
            'siret' => $validated['client_siret'] ?? null,
        ]);

        // Création du devis
        $devis = Devis::create([
            'client_id' => $client->id,
            'reference' => $validated['reference'],
            'date' => $validated['date'],
            'total_ht' => 0, // Sera calculé ci-dessous
        ]);

        // Ajout des articles au devis
        $totalHT = 0;
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['price'];
            $totalHT += $total;
            
            $devis->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $total,
            ]);
        }

        // Mise à jour du total
        $devis->update(['total_ht' => $totalHT]);

        return response()->json([
            'success' => true,
            'message' => 'Devis créé avec succès',
            'devis_id' => $devis->id,
        ]);
    }
}