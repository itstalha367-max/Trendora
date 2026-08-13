@extends('layouts.admin')

@section('title', 'Import Products')

@section('content')
<style>
    .import-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .drop-zone {
        border: 3px dashed #2d3748;
        border-radius: 20px;
        padding: 60px 20px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        background: #0f141e;
    }
    .drop-zone:hover {
        border-color: #8b5cf6;
        background: #141a27;
    }
    .drop-zone.dragover {
        border-color: #8b5cf6;
        background: #182133;
    }
    .drop-zone i {
        font-size: 60px;
        color: #93a1b4;
        margin-bottom: 20px;
    }
    .drop-zone h5 {
        font-weight: 700;
        color: #151d2a;
    }
    .drop-zone p {
        color: #93a1b4;
    }
</style>

<div class="import-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-file-import me-2 text-primary"></i>Import Products</h2>
            <p class="text-muted mb-0">Import products from CSV/Excel file</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary rounded-3">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('admin.products.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        
                        <div class="drop-zone" id="dropZone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h5>Drop your file here</h5>
                            <p>or click to browse</p>
                            <small class="text-muted">Supported: XLSX, XLS, CSV (Max 5MB)</small>
                            <input type="file" name="file" id="fileInput" class="d-none" accept=".xlsx,.xls,.csv">
                        </div>
                        
                        <div id="fileInfo" class="mt-3" style="display: none;">
                            <div class="alert alert-info border-0 rounded-4">
                                <i class="fas fa-file me-2"></i>
                                <span id="fileName"></span>
                                <button type="button" class="btn btn-sm btn-danger float-end" onclick="removeFile()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mt-3 rounded-3 py-3" id="importBtn" style="display: none;">
                            <i class="fas fa-upload me-2"></i>Import Products
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Instructions</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            File must be CSV, XLSX, or XLS
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            First row should be column headers
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Required columns: name, category, price
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Optional: description, stock, sku
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Max file size: 5MB
                        </li>
                    </ul>
                    <hr>
                    <a href="{{ route('admin.products.sample') }}" class="btn btn-outline-primary w-100 rounded-3">
                        <i class="fas fa-download me-2"></i>Download Sample File
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const importBtn = document.getElementById('importBtn');
    
    // Click to upload
    dropZone.addEventListener('click', function() {
        fileInput.click();
    });
    
    // File selected
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            showFileInfo(this.files[0]);
        }
    });
    
    // Drag and drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        if (e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'];
            
            if (validTypes.includes(file.type) || file.name.endsWith('.csv')) {
                fileInput.files = e.dataTransfer.files;
                showFileInfo(file);
            } else {
                alert('Please upload a valid Excel or CSV file.');
            }
        }
    });
    
    function showFileInfo(file) {
        fileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        fileInfo.style.display = 'block';
        importBtn.style.display = 'block';
        dropZone.style.borderColor = '#10b981';
    }
    
    window.removeFile = function() {
        fileInput.value = '';
        fileInfo.style.display = 'none';
        importBtn.style.display = 'none';
        dropZone.style.borderColor = '#2d3748';
    }
});
</script>
@endsection