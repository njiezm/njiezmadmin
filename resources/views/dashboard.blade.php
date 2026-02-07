@extends('layouts.app')

@section('title', 'NJIEZM Admin | Dashboard')

@section('content')
<h2 class="section-title">ÉTAT DE L'ACTIVITÉ</h2>
<div class="row g-4">
    <div class="col-md-4">
        <div class="admin-card text-center">
            <h6>Devis en attente</h6>
            <div class="display-4 fw-bold">{{ $pendingDevisCount }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card text-center" style="border-color: #28a745;">
            <h6>Factures payées</h6>
            <div class="display-4 fw-bold text-success">{{ $paidFacturesCount }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card text-center" style="border-color: var(--nj-yellow);">
            <h6>CA Estimé (Mois)</h6>
            <div class="display-4 fw-bold">{{ $formattedMonthlyRevenue }}</div>
        </div>
    </div>
</div>

<!-- (BONUS) Sections pour afficher les dernières activités -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="admin-card">
            <h5 class="mb-3">📄 Derniers Devis</h5>
            <div class="list-group list-group-flush">
                @forelse ($latestDevis as $devis)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $devis->reference }}</strong><br>
                            <small class="text-muted">{{ $devis->client->name ?? 'Client supprimé' }}</small>
                        </div>
                        <div class="text-end">
                            <strong>{{ number_format($devis->total_ht, 2) }} €</strong><br>
                            <small class="text-muted">{{ $devis->date->format('d/m/Y') }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Aucun devis enregistré.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="admin-card">
            <h5 class="mb-3">💰 Dernières Factures</h5>
            <div class="list-group list-group-flush">
                @forelse ($latestFactures as $facture)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $facture->reference }}</strong><br>
                            <small class="text-muted">{{ $facture->client->name ?? 'Client supprimé' }}</small>
                        </div>
                        <div class="text-end">
                            <strong>{{ number_format($facture->total_ttc, 2) }} €</strong><br>
                            @switch($facture->status)
                                @case('paid')
                                    <span class="badge bg-success">Payée</span>
                                    @break
                                @case('sent')
                                    <span class="badge bg-warning text-dark">Envoyée</span>
                                    @break
                                @case('overdue')
                                    <span class="badge bg-danger">En retard</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">Brouillon</span>
                            @endswitch
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Aucune facture enregistrée.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="admin-card mt-4">
    <h5>Raccourcis rapides</h5>
    <p>Bienvenue dans votre pannel de gestion. Sélectionnez un outil dans le menu de gauche pour commencer.</p>
</div>
@endsection