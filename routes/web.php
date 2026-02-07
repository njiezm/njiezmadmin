<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\LogoController;
use App\Http\Controllers\SimulatorController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/simulator', [SimulatorController::class, 'index'])->name('simulator');
Route::get('/devis', [DevisController::class, 'index'])->name('devis');
Route::get('/facture', [FactureController::class, 'index'])->name('facture');
Route::get('/logo', [LogoController::class, 'index'])->name('logo');

// API routes for AJAX calls
Route::post('/api/devis', [DevisController::class, 'store'])->name('devis.store');
Route::post('/api/facture', [FactureController::class, 'store'])->name('facture.store');

// Routes pour la visualisation des documents
Route::get('/devis/list', [DevisController::class, 'list'])->name('devis.list');
Route::get('/factures/list', [FactureController::class, 'list'])->name('factures.list');

// Routes pour la génération des PDF
Route::get('/devis/{devis}/pdf', [DevisController::class, 'generatePdf'])->name('devis.pdf');
Route::get('/factures/{facture}/pdf', [FactureController::class, 'generatePdf'])->name('factures.pdf');

// Routes pour la mise à jour du statut
Route::put('/devis/{devis}/status', [DevisController::class, 'updateStatus'])->name('devis.updateStatus');
Route::put('/factures/{facture}/status', [FactureController::class, 'updateStatus'])->name('factures.updateStatus');

// Route pour le PDF rapide du simulateur
Route::post('/api/simulator-pdf', [SimulatorController::class, 'generatePdf'])->name('simulator.pdf');