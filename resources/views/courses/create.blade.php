@extends('layouts.app')

@section('title', 'Create Course')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-plus-circle me-2"></i>Create New Course</h4>
            <p class="text-muted mb-0">Choose your creation method below</p>
        </div>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Courses
        </a>
    </div>

    <!-- Creation Method Tabs -->
    <ul class="nav nav-pills nav-justified mb-4 gap-3" id="creationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active py-3" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">
                <i class="bi bi-pencil-square fs-4 d-block mb-1"></i>
                <span class="fw-medium">Manual Creation</span>
                <small class="d-block text-muted">Build your course from scratch</small>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-3" id="ai-tab" data-bs-toggle="tab" data-bs-target="#ai" type="button" role="tab">
                <i class="bi bi-robot fs-4 d-block mb-1 text-primary"></i>
                <span class="fw-medium">AI-Powered Creation</span>
                <small class="d-block text-muted">Upload materials and let AI build it</small>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- === MANUAL CREATION TAB === -->
        <div class="tab-pane fade show active" id="manual" role="tabpanel">
            <div class="card stat-card">
                <div class="card-body">
                    <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Course Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="e.g. Advanced Machine Learning">
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="Describe what students will learn...">{{ old('description') }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-medium">Level <span class="text-danger">*</span></label>
                                        <select name="level" class="form-select @error('level') is-invalid @enderror">
                                            <option value="beginner">Beginner</option>
                                            <option value="intermediate">Intermediate</option>
                                            <option value="advanced">Advanced</option>
                                            <option value="all">All Levels</option>
                                        </select>
                                        @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-medium">Status</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                        </select>
                                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Duration (minutes)</label>
                                        <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration') }}" min="0" placeholder="e.g. 120">
                                        @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Price ($)</label>
                                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', 0) }}" min="0" step="0.01">
                                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Course Thumbnail</label>
                                    <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted">Recommended: 800x450px (16:9)</small>
                                    @error('thumbnail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="border rounded-3 p-3 text-center bg-light mb-3">
                                    <img id="thumbnailPreview" src="#" alt="Preview" class="img-fluid d-none rounded">
                                    <div id="thumbnailPlaceholder">
                                        <i class="bi bi-image fs-1 text-muted d-block mb-2"></i>
                                        <small class="text-muted">Preview will appear here</small>
                                    </div>
                                </div>
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <small>You can add modules, lessons, quizzes and more after creating the course.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Create Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- === AI-POWERED CREATION TAB === -->
        <div class="tab-pane fade" id="ai" role="tabpanel">
            <!-- Step Progress Indicator -->
            <div class="card stat-card mb-4">
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col">
                            <div class="step-indicator active" data-step="1">
                                <div class="step-circle mx-auto mb-2">1</div>
                                <small class="fw-medium">Upload Content</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="step-indicator" data-step="2">
                                <div class="step-circle mx-auto mb-2">2</div>
                                <small class="fw-medium">AI Analysis</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="step-indicator" data-step="3">
                                <div class="step-circle mx-auto mb-2">3</div>
                                <small class="fw-medium">Review & Edit</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="step-indicator" data-step="4">
                                <div class="step-circle mx-auto mb-2">4</div>
                                <small class="fw-medium">Publish</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Upload Content -->
            <div id="aiStep1" class="ai-step">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-cloud-arrow-up-fill text-primary" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold mt-3">Upload Educational Materials</h5>
                            <p class="text-muted">Upload one or more documents and our AI will automatically generate a complete course</p>
                        </div>

                        <form id="aiUploadForm" method="POST" action="{{ route('ai-generator.upload.post') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-4 mb-4">
                                <div class="col-md-8">
                                    <!-- Drop Zone -->
                                    <div id="dropZone" class="border-2 border-dashed rounded-3 p-5 text-center bg-light"
                                         style="border: 2px dashed #ccc; cursor: pointer; transition: all 0.3s;">
                                        <i class="bi bi-file-earmark-arrow-up text-primary" style="font-size: 3rem;"></i>
                                        <h6 class="fw-bold mt-3">Drag & drop your files here</h6>
                                        <p class="text-muted small mb-3">or click to browse files</p>
                                        <input type="file" name="files[]" id="fileInput" class="d-none" multiple accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.epub,.png,.jpg,.jpeg">
                                        <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                                            <i class="bi bi-folder2-open me-1"></i> Browse Files
                                        </button>
                                    </div>

                                    <!-- File List Preview -->
                                    <div id="fileList" class="mt-3 d-none">
                                        <h6 class="fw-bold mb-2"><i class="bi bi-paperclip me-1"></i>Selected Files</h6>
                                        <div id="fileItems" class="list-group"></div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!-- Supported Formats -->
                                    <div class="card bg-light border-0 mb-3">
                                        <div class="card-body p-3">
                                            <h6 class="fw-bold small mb-2"><i class="bi bi-info-circle me-1"></i> Supported Formats</h6>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge bg-primary">PDF</span>
                                                <span class="badge bg-primary">DOCX</span>
                                                <span class="badge bg-primary">PPTX</span>
                                                <span class="badge bg-primary">TXT</span>
                                                <span class="badge bg-primary">EPUB</span>
                                                <span class="badge bg-info">PNG</span>
                                                <span class="badge bg-info">JPG</span>
                                            </div>
                                            <hr>
                                            <h6 class="fw-bold small mb-2"><i class="bi bi-magic me-1"></i> OCR Support</h6>
                                            <small class="text-muted">Scanned documents and images with text are automatically processed</small>
                                        </div>
                                    </div>

                                    <!-- Generation Mode -->
                                    <div class="card border-0 mb-3">
                                        <div class="card-body p-3">
                                            <h6 class="fw-bold small mb-2"><i class="bi bi-sliders2 me-1"></i> Generation Mode</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="generation_mode" id="modeQuick" value="quick" checked>
                                                <label class="form-check-label" for="modeQuick">
                                                    <span class="fw-medium">Quick Mode</span>
                                                    <small class="d-block text-muted">Generate course directly from material</small>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="generation_mode" id="modeEnhanced" value="enhanced">
                                                <label class="form-check-label" for="modeEnhanced">
                                                    <span class="fw-medium">Enhanced Mode</span>
                                                    <small class="d-block text-muted">AI adds explanations, examples, practice questions</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div class="mb-3">
                                        <label class="form-label fw-medium small">Course Category <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="multiDocSection" class="d-none mb-4 p-3 bg-light rounded-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-layers-fill text-primary fs-4"></i>
                                    <div>
                                        <h6 class="fw-bold mb-0">Multi-Document Mode Active</h6>
                                        <small class="text-muted">Multiple files will be combined into one structured course</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4" id="aiGenerateBtn" disabled>
                                    <i class="bi bi-magic me-1"></i> Analyze & Generate
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .step-indicator { opacity: 0.5; }
    .step-indicator.active { opacity: 1; }
    .step-indicator.completed { opacity: 1; }
    .step-indicator .step-circle {
        width: 36px; height: 36px; border-radius: 50%;
        background: #e9ecef; color: #6c757d;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.9rem;
        transition: all 0.3s;
    }
    .step-indicator.active .step-circle {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff; box-shadow: 0 4px 15px rgba(102,126,234,0.4);
    }
    .step-indicator.completed .step-circle {
        background: #28a745; color: #fff;
    }
    .border-dashed { border-style: dashed !important; }
    #dropZone.dragover { border-color: #667eea !important; background: rgba(102,126,234,0.05) !important; }
    .ai-step { display: block; }
    .ai-step.d-none { display: none; }
</style>
@endsection

@push('scripts')
<script>
// Thumbnail preview
document.querySelector('input[name="thumbnail"]')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('thumbnailPreview').src = ev.target.result;
            document.getElementById('thumbnailPreview').classList.remove('d-none');
            document.getElementById('thumbnailPlaceholder').classList.add('d-none');
        }
        reader.readAsDataURL(file);
    }
});

// Drag & Drop for AI upload
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');
const fileItems = document.getElementById('fileItems');
const multiDocSection = document.getElementById('multiDocSection');
const aiGenerateBtn = document.getElementById('aiGenerateBtn');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
    dropZone.addEventListener(event, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(event => {
    dropZone.addEventListener(event, () => dropZone.classList.add('dragover'), false);
});

['dragleave', 'drop'].forEach(event => {
    dropZone.addEventListener(event, () => dropZone.classList.remove('dragover'), false);
});

dropZone.addEventListener('drop', function(e) {
    const files = e.dataTransfer.files;
    fileInput.files = files;
    updateFileList(files);
});

fileInput.addEventListener('change', function() {
    updateFileList(this.files);
});

function updateFileList(files) {
    fileItems.innerHTML = '';
    if (files.length === 0) {
        fileList.classList.add('d-none');
        aiGenerateBtn.disabled = true;
        return;
    }

    fileList.classList.remove('d-none');
    aiGenerateBtn.disabled = false;

    if (files.length > 1) {
        multiDocSection.classList.remove('d-none');
    } else {
        multiDocSection.classList.add('d-none');
    }

    Array.from(files).forEach((file, index) => {
        const size = (file.size / 1024).toFixed(1);
        const icon = getFileIcon(file.name);
        const item = document.createElement('div');
        item.className = 'list-group-item d-flex align-items-center gap-3 py-2';
        item.innerHTML = `
            <i class="bi ${icon} fs-4 text-primary"></i>
            <div class="flex-grow-1">
                <span class="fw-medium small">${file.name}</span>
                <small class="text-muted d-block">${size} KB</small>
            </div>
            <span class="badge bg-light text-dark">${getFileExt(file.name)}</span>
        `;
        fileItems.appendChild(item);
    });
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const icons = {
        pdf: 'bi-filetype-pdf',
        doc: 'bi-filetype-doc',
        docx: 'bi-filetype-docx',
        ppt: 'bi-filetype-ppt',
        pptx: 'bi-filetype-pptx',
        txt: 'bi-filetype-txt',
        epub: 'bi-book',
        png: 'bi-filetype-png',
        jpg: 'bi-filetype-jpg',
        jpeg: 'bi-filetype-jpg'
    };
    return icons[ext] || 'bi-file-earmark';
}

function getFileExt(filename) {
    return filename.split('.').pop().toUpperCase();
}
</script>
@endpush
