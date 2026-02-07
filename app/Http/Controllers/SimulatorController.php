<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // <-- Importer la façade PDF



class SimulatorController extends Controller
{
    public function index()
    {
        return view('simulator');
    }

    /**
     * Génère un PDF à la volée depuis le simulateur.
     */
    public function generatePdf(Request $request)
    {
        $data = $request->validate([
            'clientName' => 'required|string',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric',
        ]);

        $totalHT = collect($data['items'])->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });

        $document = [
            'type' => 'quote', // C'est un "devis" rapide
            'id' => 'SIM-' . now()->timestamp, // ID factice
            'issue_date' => now(),
            'client_name' => $data['clientName'],
            'title' => 'Estimation rapide de prestations',
            'amount' => $totalHT,
            'metadata' => [
                'items' => collect($data['items'])->map(function ($item) {
                    return [
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'vat' => 20.0,
                    ];
                })->toArray(),
                'notes' => 'Ceci est une estimation générée depuis le simulateur. Elle n\'a pas de valeur contractuelle.'
            ]
        ];

        $company = config('company');
        $pdf = Pdf::loadView('pdf.document', compact('document', 'company'));
        return $pdf->download('estimation-rapide.pdf');
    }
}