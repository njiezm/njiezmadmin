@extends('layouts.app')

@section('title', 'Liste des Devis')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">LISTE DES DEVIS</h2>
    <a href="{{ route('devis') }}" class="btn btn-njie">Créer un nouveau devis</a>
</div>

<div class="admin-card">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Référence</th>
                <th>Client</th>
                <th>Date</th>
                <th>Total HT</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($devis as $devi)
                <tr>
                    <td><strong>{{ $devi->reference }}</strong></td>
                    <td>{{ $devi->client->name }}</td>
                    <td>{{ $devi->date->format('d/m/Y') }}</td>
                    <td>{{ number_format($devi->total_ht, 2) }} €</td>
                    <td>
                        @switch($devi->status)
                            @case('draft')
                                <span class="badge bg-secondary">Brouillon</span>
                                @break
                            @case('sent')
                                <span class="badge bg-warning text-dark">Envoyé</span>
                                @break
                            @case('accepted')
                                <span class="badge bg-success">Accepté</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">Refusé</span>
                                @break
                        @endswitch
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('devis.pdf', $devi->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">Voir PDF</a>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Statut
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('devis.updateStatus', ['devis' => $devi->id, 'status' => 'draft']) }}">Brouillon</a></li>
                                    <li><a class="dropdown-item" href="{{ route('devis.updateStatus', ['devis' => $devi->id, 'status' => 'sent']) }}">Envoyé</a></li>
                                    <li><a class="dropdown-item" href="{{ route('devis.updateStatus', ['devis' => $devi->id, 'status' => 'accepted']) }}">Accepté</a></li>
                                    <li><a class="dropdown-item" href="{{ route('devis.updateStatus', ['devis' => $devi->id, 'status' => 'rejected']) }}">Refusé</a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Aucun devis trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection