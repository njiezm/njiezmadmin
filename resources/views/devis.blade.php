@extends('layouts.app')

@section('title', 'NJIEZM Admin | Générateur Devis')

@section('content')
<h2 class="section-title">GÉNÉRATEUR DE DEVIS</h2>
<div class="row">
    <div class="col-lg-4">
        <div class="admin-card">
            <h5>Infos Client</h5>
            <input type="text" id="devis-client-name" class="form-control mb-2" placeholder="Nom du Client / Entreprise">
            <textarea id="devis-client-addr" class="form-control mb-2" rows="3" placeholder="Adresse complète"></textarea>
            <input type="text" id="devis-client-siret" class="form-control mb-2" placeholder="SIRET (optionnel)">
            <hr>
            <h5>Référence</h5>
            <input type="text" id="devis-ref" class="form-control mb-2" placeholder="Ex: D2024-001">
            <input type="date" id="devis-date" class="form-control mb-2">
        </div>
    </div>
    <div class="col-lg-8">
        <div class="admin-card">
            <h5>Lignes du Devis</h5>
            <table class="table table-sm">
                <thead>
                    <tr><th>Description</th><th style="width:80px">Qté</th><th style="width:100px">Prix</th><th>Total</th></tr>
                </thead>
                <tbody id="devis-body">
                    <tr>
                        <td><input type="text" class="form-control form-control-sm" value="Prestation de développement"></td>
                        <td><input type="number" class="form-control form-control-sm qty" value="1" oninput="updateTotals('devis')"></td>
                        <td><input type="number" class="form-control form-control-sm price" value="650" oninput="updateTotals('devis')"></td>
                        <td class="row-total">650 €</td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-sm btn-outline-dark mb-3" onclick="addRow('devis-body', 'devis')">+ Ligne</button>
            <div class="total-box mb-3" id="devis-grand-total">650 €</div>
            <button class="btn btn-njie w-100" onclick="exportPDF('DEVIS')">Exporter Devis PDF</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function addRow(containerId, type) {
        const body = document.getElementById(containerId);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" placeholder="Nouvelle ligne"></td>
            <td><input type="number" class="form-control form-control-sm qty" value="1" oninput="updateTotals('${type}')"></td>
            <td><input type="number" class="form-control form-control-sm price" value="0" oninput="updateTotals('${type}')"></td>
            <td class="row-total">0 €</td>
        `;
        body.appendChild(tr);
        updateTotals(type);
    }

    function updateTotals(type) {
        let totalHT = 0;
        const container = document.getElementById(type + '-body');
        if(!container) return;
        const rows = container.querySelectorAll('tr');

        rows.forEach(row => {
            const q = parseFloat(row.querySelector('.qty').value) || 0;
            const p = parseFloat(row.querySelector('.price').value) || 0;
            const t = q * p;
            row.querySelector('.row-total').innerText = t.toLocaleString() + ' €';
            totalHT += t;
        });

        const totalEl = document.getElementById(type + '-grand-total');
        if(totalEl) totalEl.innerText = totalHT.toLocaleString() + ' €';
    }

    // Export PDF Professionnel (Devis)
    function exportPDF(docType) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const prefix = docType === 'DEVIS' ? 'devis' : 'fac';
        
        doc.setFillColor(0, 51, 102);
        doc.rect(0, 0, 210, 40, 'F');
        doc.setTextColor(255, 215, 0);
        doc.setFontSize(22);
        doc.text("NJIEZM.FR", 15, 25);
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(10);
        doc.text("Expertise IT & Digitalisation", 15, 32);

        doc.setTextColor(0, 51, 102);
        doc.setFontSize(18);
        doc.text(docType, 140, 60);
        doc.setFontSize(10);
        doc.text("Référence : " + document.getElementById(prefix + '-ref').value, 140, 68);
        doc.text("Date : " + (document.getElementById(prefix + '-date')?.value || new Date().toLocaleDateString()), 140, 74);

        doc.setTextColor(0,0,0);
        doc.text("CLIENT :", 15, 60);
        doc.setFont("helvetica", "bold");
        doc.text(document.getElementById(prefix + '-client-name').value || "Client Inconnu", 15, 66);
        doc.setFont("helvetica", "normal");
        doc.text(document.getElementById(prefix + '-client-addr').value || "Adresse non fournie", 15, 72, { maxWidth: 80 });

        const tableData = [];
        const rows = document.getElementById(prefix + '-body').querySelectorAll('tr');
        rows.forEach(row => {
            const inputs = row.querySelectorAll('input');
            tableData.push([
                inputs[0].value,
                inputs[1].value,
                inputs[2].value + " €",
                row.querySelector('.row-total').innerText
            ]);
        });

        doc.autoTable({
            startY: 95,
            head: [['Description', 'Qté', 'PU HT', 'Total HT']],
            body: tableData,
            theme: 'grid',
            headStyles: { fillColor: [0, 51, 102] }
        });

        const finalY = doc.lastAutoTable.finalY + 10;
        doc.setFontSize(14);
        doc.text("TOTAL HT : " + document.getElementById('devis-grand-total').innerText, 140, finalY);

        doc.setFontSize(8);
        doc.setTextColor(100, 100, 100);
        doc.text("N'JIE ZAMON - SIRET: 123 456 789 00012 - contact@njiezm.fr", 105, 285, { align: "center" });
        
        doc.save(`${docType}_${document.getElementById(prefix + '-ref').value || 'Doc'}.pdf`);
    }

    window.onload = () => {
        updateTotals('devis');
    };
</script>
@endpush