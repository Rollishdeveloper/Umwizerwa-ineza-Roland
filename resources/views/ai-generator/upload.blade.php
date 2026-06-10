@extends('layouts.app')
@section('title', 'Upload Materials - AI Course Generator')
@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-cloud-arrow-up text-primary me-2"></i>Upload Educational Materials</h4>
            <p class="text-muted mb-0">Upload books, documents, or presentations — AI will build your course</p>
        </div>
        <a href="{{ route('ai-generator.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card stat-card">
                <div class="card-body">
                    <form method="POST" action="{{ route('ai-generator.upload.post') }}" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <!-- Drop Zone -->
                        <div id="dropZone" class="border-2 border-dashed rounded-3 p-5 text-center bg-light mb-4"
                             style="border: 2px dashed #ccc; cursor: pointer; transition: all 0.3s;">
                            <div class="py-4">
                                <i class="bi bi-cloud-upload-fill text-primary" style="font-size: 4rem;"></i>
                                <h5 class="fw-bold mt-3">Drag & Drop Files Here</h5>
                                <p class="text-muted mb-3">or click to browse — supports PDF, DOCX, PPTX, TXT, EPUB, and images</p>
                                <input type="file" name="files[]" id="fileInput" class="d-none" multiple accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.epub,.png,.jpg,.jpeg">
                                <button type="button" class="btn btn-primary btn-lg" onclick="document.getElementById('fileInput').click()">
                                    <i class="bi bi-folder2-open me-2"></i> Browse Files
                                </button>
                            </div>
                        </div>

                        <!-- File List -->
                        <div id="fileList" class="d-none mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0"><i class="bi bi-paperclip me-1"></i>Selected Files (<span id="fileCount">0</span>)</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFiles()"><i class="bi bi-trash"></i> Clear All</button>
                            </div>
                            <div id="fileItems" class="list-group"></div>
                        </div>

                        <!-- Multi-document Notice -->
                        <div id="multiDocNotice" class="d-none alert alert-info">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-layers-fill fs-4"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Multi-Document Course</h6>
                                    <small>Multiple files will be intelligently combined into one structured course with chapters in logical order.</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Course Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Generation Mode</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="generation_mode" id="modeQuick" value="quick" checked>
                                        <label class="form-check-label" for="modeQuick">
                                            <span class="fw-medium">Quick</span>
                                            <small class="d-block text-muted">Direct from material</small>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="generation_mode" id="modeEnhanced" value="enhanced">
                                        <label class="form-check-label" for="modeEnhanced">
                                            <span class="fw-medium">Enhanced</span>
                                            <small class="d-block text-muted">AI expands content</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('ai-generator.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4" id="submitBtn" disabled>
                                <i class="bi bi-magic me-1"></i> Analyze & Generate Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Supported Formats -->
            <div class="card stat-card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-check text-primary me-2"></i>Supported Formats</h6>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge bg-primary fs-6">PDF</span>
                        <span class="badge bg-primary fs-6">DOCX</span>
                        <span class="badge bg-primary fs-6">PPTX</span>
                        <span class="badge bg-primary fs-6">TXT</span>
                        <span class="badge bg-primary fs-6">EPUB</span>
                        <span class="badge bg-info fs-6">PNG</span>
                        <span class="badge bg-info fs-6">JPG</span>
                    </div>
                    <small class="text-muted">Max file size: 50MB per file</small>
                </div>
            </div>

            <!-- Generation Info -->
            <div class="card stat-card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-info me-2"></i>What AI Will Generate</h6>
                    <ul class="small mb-0 list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> Course title & description</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> Learning objectives</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> Modules with ordered lessons</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> Lesson content & summaries</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> Practice exercises</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> Module quizzes (MCQ, T/F)</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> Course assignments</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> Final examination</li>
                    </ul>
                </div>
            </div>

            <!-- OCR Support -->
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-eyeglasses text-warning me-2"></i>OCR Support</h6>
                    <p class="small text-muted mb-0">Scanned documents and images with text are automatically processed using OCR technology.</p>
                    <p class="small text-muted mb-0 mt-1">Supported languages: English, French, Kinyarwanda</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-dashed { border-style: dashed !important; }
#dropZone.dragover { border-color: #667eea !important; background: rgba(102,126,234,0.08) !important; }
</style>
@endsection

@push('scripts')
<script>
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');
const fileItems = document.getElementById('fileItems');
const fileCount = document.getElementById('fileCount');
const multiDocNotice = document.getElementById('multiDocNotice');
const submitBtn = document.getElementById('submitBtn');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => dropZone.addEventListener(e, e => { e.preventDefault(); e.stopPropagation(); }, false));
['dragenter', 'dragover'].forEach(e => dropZone.addEventListener(e, () => dropZone.classList.add('dragover'), false));
['dragleave', 'drop'].forEach(e => dropZone.addEventListener(e, () => dropZone.classList.remove('dragover'), false));
dropZone.addEventListener('drop', e => { fileInput.files = e.dataTransfer.files; updateFileList(fileInput.files); });
fileInput.addEventListener('change', () => updateFileList(fileInput.files));

function updateFileList(files) {
    fileItems.innerHTML = '';
    if (files.length === 0) { fileList.classList.add('d-none'); multiDocNotice.classList.add('d-none'); submitBtn.disabled = true; return; }
    fileList.classList.remove('d-none');
    submitBtn.disabled = false;
    fileCount.textContent = files.length;
    if (files.length > 1) multiDocNotice.classList.remove('d-none'); else multiDocNotice.classList.add('d-none');
    Array.from(files).forEach((f, i) => {
        const ext = f.name.split('.').pop().toUpperCase();
        const icon = {PDF:'bi-filetype-pdf',DOC:'bi-filetype-doc',DOCX:'bi-filetype-docx',PPT:'bi-filetype-ppt',PPTX:'bi-filetype-pptx',TXT:'bi-filetype-txt',EPUB:'bi-book',PNG:'bi-filetype-png',JPG:'bi-filetype-jpg',JPEG:'bi-filetype-jpg'}[ext] || 'bi-file-earmark';
        const el = document.createElement('div');
        el.className = 'list-group-item d-flex align-items-center gap-3 py-2 px-3';
        el.innerHTML = `<i class="bi ${icon} fs-3 text-primary"></i><div class="flex-grow-1"><span class="fw-medium small">${f.name}</span><small class="text-muted d-block">${(f.size/1024).toFixed(1)} KB</small></div><span class="badge bg-light text-dark">${ext}</span>`;
        fileItems.appendChild(el);
    });
}

function clearFiles() { fileInput.value = ''; fileItems.innerHTML = ''; fileList.classList.add('d-none'); multiDocNotice.classList.add('d-none'); submitBtn.disabled = true; }
</script>
@endpush
