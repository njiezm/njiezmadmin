@extends('layouts.app')

@section('title', 'Liste des Factures')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">LISTE DES FACTURES</h2>
    <a href="{{ route('facture') }}" class="btn btn-njie">Créer une nouvelle facture</a>
</div>

<div class="admin-card">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Référence</th>
                <th>Client</th>
                <th>Date</th>
                <th>Échéance</th>
                <th>Total TTC</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($factures as $facture)
                <tr>
                    <td><strong>{{ $facture->reference }}</strong></td>
                    <td>{{ $facture->client->name }}</td>
                    <td>{{ $facture->date->format('d/m/Y') }}</td>
                    <td>{{ $facture->deadline->format('d/m/Y') }}</td>
                    <td>{{ number_format($facture->total_ttc, 2) }} €</td>
                    <td>
                        @switch($facture->status)
                            @case('draft')
                                <span class="badge bg-secondary">Brouillon</span>
                                @break
                            @case('sent')
                                <span class="badge bg-warning text-dark">Envoyée</span>
                                @break
                            @case('paid')
                                <span class="badge bg-success">Payée</span>
                                @break
                            @case('overdue')
                                <span class="badge bg-danger">En retard</span>
                                @break
                        @endswitch
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('factures.pdf', $facture->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">Voir PDF</a>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Statut
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('factures.updateStatus', ['facture' => $facture->id, 'status' => 'draft']) }}">Brouillon</a></li>
                                    <li><a class="dropdown-item" href="{{ route('factures.updateStatus', ['facture' => $facture->id, 'status' => 'sent']) }}">Envoyée</a></li>
                                    <li><a class="dropdown-item" href="{{ route('factures.updateStatus', ['facture' => $facture->id, 'status' => 'paid']) }}">Payée</a></li>
                                    <li><a class="dropdown-item" href="{{ route('factures.updateStatus', ['facture' => $facture->id, 'status' => 'overdue']) }}">En retard</a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Aucune facture trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection