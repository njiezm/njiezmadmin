@extends('layouts.app')

@section('title', 'NJIEZM Admin | Exportateur de Logo')

@section('content')
<h2 class="section-title">EXPORTATEUR DE LOGO</h2>
<div class="row">
    <div class="col-md-8">
        <div class="logo-preview-box" id="logo-preview-box">
            <div id="logo-canvas-text">NJIEZM<span>.FR</span></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <h5>Paramètres d'export</h5>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="transparent-toggle">
                <label class="form-check-label" for="transparent-toggle">Fond transparent</label>
            </div>
            <label class="form-label">Format de fichier</label>
            <select id="format-select" class="form-select mb-4">
                <option value="png">Format PNG (.png)</option>
                <option value="jpeg">Format JPEG (.jpg)</option>
                <option value="webp">Format WEBP (.webp)</option>
                <option value="svg">Format Vectoriel (.svg)</option>
            </select>
            <button class="btn btn-njie w-100" onclick="downloadLogo()">Télécharger le Logo</button>
            <p class="text-muted small mt-3 text-center">Version Haute Définition (1200x400px)</p>
        </div>
    </div>
</div>
<canvas id="export-canvas"></canvas>
@endsection

@push('scripts')
<script>
    // LOGO GENERATOR LOGIC
    const transparentToggle = document.getElementById('transparent-toggle');
    const previewBox = document.getElementById('logo-preview-box');
    const formatSelect = document.getElementById('format-select');

    transparentToggle.addEventListener('change', () => {
        if (transparentToggle.checked) {
            previewBox.classList.add('transparent');
            previewBox.style.backgroundColor = 'transparent';
        } else {
            previewBox.classList.remove('transparent');
            previewBox.style.backgroundColor = '#003366';
        }
    });

    function downloadLogo() {
        const format = formatSelect.value;
        const isTransparent = transparentToggle.checked;
        if (format === 'svg') { downloadSVG(isTransparent); } 
        else { downloadRaster(format, isTransparent); }
    }

    function downloadRaster(format, isTransparent) {
        const canvas = document.getElementById('export-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 1200;
        canvas.height = 400;

        if (!isTransparent || format === 'jpeg') {
            ctx.fillStyle = '#003366';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        ctx.font = "120px 'Special Elite'";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";

        const text1 = "NJIEZM";
        const text2 = ".FR";
        const w1 = ctx.measureText(text1).width;
        const w2 = ctx.measureText(text2).width;
        const totalW = w1 + w2;
        const startX = (canvas.width - totalW) / 2;

        ctx.fillStyle = "#FFD700";
        ctx.textAlign = "left";
        ctx.fillText(text1, startX, canvas.height / 2);
        ctx.fillStyle = "#FFFFFF";
        ctx.fillText(text2, startX + w1, canvas.height / 2);

        const mime = `image/${format === 'jpeg' ? 'jpeg' : format}`;
        const link = document.createElement('a');
        link.download = `logo-njiezm.${format}`;
        link.href = canvas.toDataURL(mime, 1.0);
        link.click();
    }

    function downloadSVG(isTransparent) {
        const bgColor = isTransparent ? 'none' : '#003366';
        const svgContent = `
            <svg xmlns="http://www.w3.org/2000/svg" width="600" height="200" viewBox="0 0 600 200">
                <defs>
                    <style type="text/css">
                        @import url('https://fonts.googleapis.com/css2?family=Special+Elite&amp;display=swap');
                        text { font-family: 'Special Elite', cursive; }
                    </style>
                </defs>
                <rect width="100%" height="100%" fill="${bgColor}"/>
                <text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" font-size="80">
                    <tspan fill="#FFD700">NJIEZM</tspan><tspan fill="#FFFFFF">.FR</tspan>
                </text>
            </svg>
        `;
        const blob = new Blob([svgContent], {type: 'image/svg+xml;charset=utf-8'});
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'logo-njiezm.svg';
        link.click();
        URL.revokeObjectURL(url);
    }
</script>
@endpush