@extends('layouts.app')

@section('title', 'NJIEZM Admin | Générateur Facture')

@section('content')
<h2 class="section-title">GÉNÉRATEUR DE FACTURE</h2>
<div class="row">
    <div class="col-lg-4">
        <div class="admin-card">
            <h5>Destinataire</h5>
            <input type="text" id="fac-client-name" class="form-control mb-2" placeholder="Nom du Client">
            <textarea id="fac-client-addr" class="form-control mb-2" rows="3" placeholder="Adresse"></textarea>
            <hr>
            <h5>Détails Facturation</h5>
            <input type="text" id="fac-ref" class="form-control mb-2" placeholder="Ex: F2024-001">
            <label class="small">Date d'échéance</label>
            <input type="date" id="fac-deadline" class="form-control mb-2">
            <select id="fac-tva" class="form-select" onchange="updateTotals('fac')">
                <option value="0">TVA 0% (Auto-entrepreneur)</option>
                <option value="20">TVA 20%</option>
            </select>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="admin-card">
            <h5>Articles / Services</h5>
            <table class="table table-sm">
                <thead>
                    <tr><th>Description</th><th style="width:80px">Qté</th><th style="width:100px">Prix HT</th><th>Total HT</th></tr>
                </thead>
                <tbody id="fac-body">
                    <tr>
                        <td><input type="text" class="form-control form-control-sm" value="Maintenance Système"></td>
                        <td><input type="number" class="form-control form-control-sm qty" value="1" oninput="updateTotals('fac')"></td>
                        <td><input type="number" class="form-control form-control-sm price" value="150" oninput="updateTotals('fac')"></td>
                        <td class="row-total">150 €</td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-sm btn-outline-dark mb-3" onclick="addRow('fac-body', 'fac')">+ Article</button>
            <div class="p-3 bg-light border mb-3">
                <div class="d-flex justify-content-between"><span>Total HT :</span> <span id="fac-total-ht">0 €</span></div>
                <div class="d-flex justify-content-between"><span>TVA :</span> <span id="fac-total-tva">0 €</span></div>
                <div class="d-flex justify-content-between fw-bold fs-4 mt-2 border-top pt-2">
                    <span>TOTAL TTC :</span> <span id="fac-grand-total">0 €</span>
                </div>
            </div>
            <button class="btn btn-njie w-100" onclick="exportPDF('FACTURE')">Générer la Facture PDF</button>
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

        if (type === 'fac') {
            const tvaRate = parseFloat(document.getElementById('fac-tva').value) / 100;
            const tvaAmount = totalHT * tvaRate;
            document.getElementById('fac-total-ht').innerText = totalHT.toLocaleString() + ' €';
            document.getElementById('fac-total-tva').innerText = tvaAmount.toLocaleString() + ' €';
            document.getElementById('fac-grand-total').innerText = (totalHT + tvaAmount).toLocaleString() + ' €';
        } else {
            const totalEl = document.getElementById(type + '-grand-total');
            if(totalEl) totalEl.innerText = totalHT.toLocaleString() + ' €';
        }
    }

    // Export PDF Professionnel (Facture)
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
        doc.text("Date : " + new Date().toLocaleDateString(), 140, 74);
        doc.text("Échéance : " + document.getElementById('fac-deadline').value, 140, 80);

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
        doc.text("Total HT : " + document.getElementById('fac-total-ht').innerText, 140, finalY);
        doc.text("TVA : " + document.getElementById('fac-total-tva').innerText, 140, finalY + 8);
        doc.setFontSize(14);
        doc.setFont("helvetica", "bold");
        doc.text("TOTAL TTC : " + document.getElementById('fac-grand-total').innerText, 140, finalY + 18);

        doc.setFontSize(8);
        doc.setTextColor(100, 100, 100);
        doc.text("N'JIE ZAMON - SIRET: 123 456 789 00012 - contact@njiezm.fr", 105, 285, { align: "center" });
        
        doc.save(`${docType}_${document.getElementById(prefix + '-ref').value || 'Doc'}.pdf`);
    }

    window.onload = () => {
        updateTotals('fac');
    };
</script>
@endpush