@extends('layouts.app')

@section('title', 'NJIEZM Admin | Simulateur de Tarifs')

@section('content')
<h2 class="section-title">SIMULATEUR DE TARIFS</h2>
<div class="admin-card">
    <!-- NOUVEAU CHAMP POUR LE NOM -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="sim-client-name" class="form-label">Nom du client ou de l'entité (pour l'export)</label>
            <input type="text" id="sim-client-name" class="form-control" placeholder="Ex: Société Gamma">
        </div>
    </div>
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Prestation</th>
                <th style="width:120px">Qté</th>
                <th style="width:150px">PU HT (€)</th>
                <th style="width:150px">Total</th>
                <th style="width:50px"></th>
            </tr>
        </thead>
        <tbody id="sim-body">
            <tr>
                <td><input type="text" class="form-control" value="Expertise IT / Conseil"></td>
                <td><input type="number" class="form-control qty" value="1" oninput="updateTotals('sim')"></td>
                <td><input type="number" class="form-control price" value="450" oninput="updateTotals('sim')"></td>
                <td class="row-total fw-bold">450 €</td>
                <td><button class="btn btn-sm btn-link text-danger" onclick="this.closest('tr').remove(); updateTotals('sim')">×</button></td>
            </tr>
        </tbody>
    </table>
    <button class="btn btn-sm btn-njie mb-3" onclick="addRow('sim-body', 'sim')">+ Ajouter</button>
    <div class="total-box" id="sim-grand-total">450 €</div>
<hr>
    <button class="btn btn-njie" onclick="exportSimulatorPDF()">Exporter en PDF</button>
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
            <td><button class="btn btn-sm text-danger" onclick="this.closest('tr').remove(); updateTotals('sim')">×</button></td>
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

    window.onload = () => {
        updateTotals('sim');
    };

    // NOUVELLE FONCTION D'EXPORT
    async function exportSimulatorPDF() {
        const clientName = document.getElementById('sim-client-name').value;
        if (!clientName) {
            alert('Veuillez entrer un nom de client ou d\'entité pour l\'export.');
            return;
        }

        const rows = document.querySelectorAll('#sim-body tr');
        const items = Array.from(rows).map(row => {
            const inputs = row.querySelectorAll('input');
            return {
                description: inputs[0].value,
                quantity: parseFloat(inputs[1].value) || 0,
                price: parseFloat(inputs[2].value) || 0,
            };
        });

        if (items.length === 0 || items.every(item => !item.description)) {
            alert('Le devis est vide. Veuillez ajouter au moins une prestation.');
            return;
        }

        try {
            const response = await fetch('{{ route("simulator.pdf") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    clientName: clientName,
                    items: items
                })
            });

            if (!response.ok) {
                throw new Error('Erreur lors de la génération du PDF.');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'estimation-rapide.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
        }
    }

    window.onload = () => {
        updateTotals('sim');
    };
</script>
@endpush