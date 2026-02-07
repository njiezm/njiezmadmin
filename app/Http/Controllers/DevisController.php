<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devis;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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

        // Création du devis avec le statut "draft" par défaut
        $devis = Devis::create([
            'client_id' => $client->id,
            'reference' => $validated['reference'],
            'date' => $validated['date'],
            'total_ht' => 0,
            'status' => 'draft', // Statut par défaut
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

    public function list()
    {
        $devis = Devis::with('client')->latest()->get();
        return view('devis.list', compact('devis'));
    }

    /**
     * Génère le PDF d'un devis spécifique.
     */
    public function generatePdf(Devis $devis)
    {
        // Charger les relations nécessaires
        $devis->load('client', 'items');

        // Préparer les données pour le template
        $document = [
            'type' => 'quote',
            'id' => $devis->id,
            'issue_date' => $devis->date,
            'client_name' => $devis->client->name,
            'title' => 'Devis : Prestations de services',
            'amount' => $devis->total_ht,
            'status' => $devis->status, // Ajout du statut
            'metadata' => [
                'client_address' => $devis->client->address,
                'client_email' => $devis->client->email,
                'client_phone' => $devis->client->phone,
                'items' => $devis->items->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->price,
                        'vat' => 20.0,
                    ];
                })->toArray(),
            ]
        ];

        $company = config('company');

        // Charger la vue et générer le PDF avec le nouveau template
        $pdf = Pdf::loadView('pdf.devis-template', compact('document', 'company'));
        
        // Télécharger le PDF
        return $pdf->download('devis-' . $devis->reference . '.pdf');
    }

    /**
     * Met à jour le statut d'un devis.
     */
    public function updateStatus(Request $request, Devis $devis)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected',
        ]);

        $devis->update(['status' => $validated['status']]);

        return redirect()->route('devis.list')
            ->with('success', 'Le statut du devis a été mis à jour avec succès.');
    }
}