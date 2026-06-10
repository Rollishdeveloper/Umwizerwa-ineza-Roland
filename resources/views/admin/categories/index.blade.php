@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Course Categories</h4><p class="text-muted mb-0">Manage course categories</p></div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><i class="bi bi-plus-circle"></i> Add Category</button>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Name</th><th>Description</th><th>Courses</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->category_id }}</td>
                                <td class="fw-medium">{{ $category->category_name }}</td>
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td><span class="badge bg-primary">{{ $category->courses_count }}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->category_id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editCategoryModal{{ $category->category_id }}">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST" action="{{ route('admin.categories.update', $category) }}">@csrf @method('PUT')
                                        <div class="modal-header"><h5 class="modal-title">Edit Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">
                                            <div class="mb-3"><label class="form-label">Category Name</label><input type="text" name="category_name" class="form-control" value="{{ $category->category_name }}" required></div>
                                            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control">{{ $category->description }}</textarea></div>
                                        </div>
                                        <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No categories</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addCategoryModal">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.categories.store') }}">@csrf
                <div class="modal-header"><h5 class="modal-title">Add Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Category Name</label><input type="text" name="category_name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Create</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
