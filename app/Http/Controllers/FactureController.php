<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facture;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'date' => 'required|date',
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

        // Création de la facture avec le statut "draft" par défaut
        $facture = Facture::create([
            'client_id' => $client->id,
            'reference' => $validated['reference'],
            'date' => $validated['date'],
            'deadline' => $validated['deadline'],
            'tva_rate' => $validated['tva_rate'],
            'total_ht' => 0,
            'total_tva' => 0,
            'total_ttc' => 0,
            'status' => 'draft', // Statut par défaut
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

    public function list()
    {
        $factures = Facture::with('client')->latest()->get();
        return view('factures.list', compact('factures'));
    }

    /**
     * Génère le PDF d'une facture spécifique.
     */
    public function generatePdf(Facture $facture)
    {
        $facture->load('client', 'items');

        $document = [
            'type' => 'invoice',
            'id' => $facture->id,
            'issue_date' => $facture->date,
            'due_date' => $facture->deadline,
            'client_name' => $facture->client->name,
            'amount' => $facture->total_ht,
            'status' => $facture->status, // Ajout du statut
            'metadata' => [
                'client_address' => $facture->client->address,
                'client_email' => $facture->client->email,
                'client_phone' => $facture->client->phone,
                'items' => $facture->items->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->price,
                        'vat' => $facture->tva_rate,
                    ];
                })->toArray(),
            ]
        ];

        $company = config('company');
        $pdf = Pdf::loadView('pdf.devis-template', compact('document', 'company'));
        return $pdf->download('facture-' . $facture->reference . '.pdf');
    }

    /**
     * Met à jour le statut d'une facture.
     */
    public function updateStatus(Request $request, Facture $facture)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue',
        ]);

        $facture->update(['status' => $validated['status']]);

        return redirect()->route('factures.list')
            ->with('success', 'Le statut de la facture a été mis à jour avec succès.');
    }
}