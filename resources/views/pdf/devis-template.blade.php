<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->type == 'quote' ? 'Devis' : 'Facture' }} N°{{ $document->id }}</title>
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #003366;
            padding-bottom: 20px;
        }
        .logo {
            font-family: 'Special Elite', cursive;
            font-size: 24px;
            color: #003366;
        }
        .logo span {
            color: #FFD700;
        }
        .document-info {
            text-align: right;
        }
        .document-type {
            font-size: 24px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 5px;
        }
        .document-number {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .document-date {
            font-size: 14px;
            color: #666;
        }
        .content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .company-info, .client-info {
            width: 48%;
        }
        .section-title {
            font-weight: bold;
            color: #003366;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals {
            text-align: right;
            margin-bottom: 30px;
        }
        .totals-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 5px;
        }
        .totals-label {
            width: 150px;
            padding-right: 10px;
            text-align: right;
        }
        .totals-value {
            width: 100px;
            text-align: right;
        }
        .totals-row.grand-total {
            font-weight: bold;
            font-size: 18px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .payment-info {
            margin-bottom: 20px;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 3px solid #003366;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .status-draft { background-color: #f8f9fa; color: #6c757d; }
        .status-sent { background-color: #cce5ff; color: #004085; }
        .status-accepted { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-paid { background-color: #d1ecf1; color: #0c5460; }
        .status-overdue { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                NJIEZM<span>.FR</span>
            </div>
            <div class="document-info">
                @if($document->status)
                    <div class="status-badge status-{{ $document->status }}">
                        @switch($document->status)
                            @case('draft')
                                Brouillon
                                @break
                            @case('sent')
                                Envoyé
                                @break
                            @case('accepted')
                                Accepté
                                @break
                            @case('rejected')
                                Refusé
                                @break
                            @case('paid')
                                Payé
                                @break
                            @case('overdue')
                                En retard
                                @break
                        @endswitch
                    </div>
                @endif
                <div class="document-type">{{ $document->type == 'quote' ? 'DEVIS' : 'FACTURE' }}</div>
                <div class="document-number">N° {{ str_pad($document->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="document-date">Date: {{ $document->issue_date->format('d/m/Y') }}</div>
                @if($document->type == 'invoice')
                    <div class="document-date">Échéance: {{ $document->due_date->format('d/m/Y') }}</div>
                @endif
            </div>
        </div>
        
        <div class="content">
            <div class="company-info">
                <div class="section-title">NJIEZM.FR</div>
                <div class="info-row">{{ $company['address'] }}</div>
                <div class="info-row">{{ $company['phone'] }}</div>
                <div class="info-row">{{ $company['email'] }}</div>
                <div class="info-row">SIRET: {{ $company['siret'] }}</div>
                <div class="info-row">TVA: {{ $company['tva'] }}</div>
            </div>
            
            <div class="client-info">
                <div class="section-title">Client</div>
                <div class="info-row">{{ $document->client_name }}</div>
                @if(isset($document->metadata['client_address']))
                    <div class="info-row">{{ $document->metadata['client_address'] }}</div>
                @endif
                @if(isset($document->metadata['client_email']))
                    <div class="info-row">{{ $document->metadata['client_email'] }}</div>
                @endif
                @if(isset($document->metadata['client_phone']))
                    <div class="info-row">{{ $document->metadata['client_phone'] }}</div>
                @endif
            </div>
        </div>
        
        @if($document->type == 'quote')
            <div class="section-title">Objet du devis</div>
            <div class="info-row">{{ $document->title }}</div>
        @endif
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Quantité</th>
                    <th class="text-right">Prix unitaire</th>
                    <th class="text-right">TVA (%)</th>
                    <th class="text-right">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($document->metadata['items']) && count($document->metadata['items']) > 0)
                    @foreach($document->metadata['items'] as $item)
                    <tr>
                        <td>{{ $item['description'] }}</td>
                        <td class="text-right">{{ $item['quantity'] }}</td>
                        <td class="text-right">{{ number_format($item['unit_price'], 2, ',', ' ') }} €</td>
                        <td class="text-right">{{ $item['vat'] }}</td>
                        <td class="text-right">{{ number_format($item['quantity'] * $item['unit_price'], 2, ',', ' ') }} €</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">
                            Aucun article n'a été ajouté à ce {{ $document->type == 'quote' ? 'devis' : 'facture' }}.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <div class="totals">
            <div class="totals-row">
                <div class="totals-label">Total HT:</div>
                <div class="totals-value">{{ number_format($document->amount, 2, ',', ' ') }} €</div>
            </div>
            <div class="totals-row">
                <div class="totals-label">TVA (20%):</div>
                <div class="totals-value">{{ number_format($document->amount * 0.2, 2, ',', ' ') }} €</div>
            </div>
            <div class="totals-row grand-total">
                <div class="totals-label">Total TTC:</div>
                <div class="totals-value">{{ number_format($document->amount * 1.2, 2, ',', ' ') }} €</div>
            </div>
        </div>
        
        @if($document->type == 'invoice')
            <div class="payment-info">
                <div class="section-title">Informations de paiement</div>
                <div class="info-row">Virement bancaire</div>
                <div class="info-row">IBAN: FR76 1759 8000 0100 0174 9240 307</div>
                <div class="info-row">BIC: LYDIFRP2XXX</div>
            </div>
        @endif
        
        @if(isset($document->metadata['notes']))
            <div class="notes">
                <div class="section-title">Notes</div>
                <div>{{ $document->metadata['notes'] }}</div>
            </div>
        @endif
        
        <div class="footer">
            <div>{{ $document->type == 'quote' ? 'Devis' : 'Facture' }} établi par NJIEZM.FR</div>
            <div>{{ $document->type == 'quote' ? 'Ce devis est valable 30 jours.' : 'En cas de retard de paiement, une pénalité de 3 fois le taux d\'intérêt légal sera appliquée.' }}</div>
        </div>
    </div>
</body>
</html>