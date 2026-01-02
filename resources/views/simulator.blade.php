@extends('layouts.app')

@section('title', 'NJIEZM Admin | Simulateur de Tarifs')

@section('content')
<h2 class="section-title">SIMULATEUR DE TARIFS</h2>
<div class="admin-card">
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
</script>
@endpush