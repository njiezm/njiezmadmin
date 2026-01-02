@extends('layouts.app')

@section('title', 'NJIEZM Admin | Dashboard')

@section('content')
<h2 class="section-title">ÉTAT DE L'ACTIVITÉ</h2>
<div class="row g-4">
    <div class="col-md-4">
        <div class="admin-card text-center">
            <h6>Devis en attente</h6>
            <div class="display-4 fw-bold">04</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card text-center" style="border-color: #28a745;">
            <h6>Factures payées</h6>
            <div class="display-4 fw-bold text-success">12</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card text-center" style="border-color: var(--nj-yellow);">
            <h6>CA Estimé (Mois)</h6>
            <div class="display-4 fw-bold">8.4k€</div>
        </div>
    </div>
</div>
<div class="admin-card mt-4">
    <h5>Raccourcis rapides</h5>
    <p>Bienvenue dans votre pannel de gestion. Sélectionnez un outil dans le menu de gauche pour commencer.</p>
</div>
@endsection