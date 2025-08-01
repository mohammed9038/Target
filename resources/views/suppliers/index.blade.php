@extends('layouts.app')

@section('title', __('Suppliers'))

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1">{{ __('Suppliers') }}</h1>
        <p class="text-muted mb-0">{{ __('Manage supplier information and classifications') }}</p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>{{ __('Add Supplier') }}
        </a>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle me-2"></i>
            <div>
                <strong>{{ __('Success!') }}</strong> {{ session('success') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Error Alert -->
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <div>
                <strong>{{ __('Error!') }}</strong> {{ session('error') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Suppliers Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="bi bi-building me-2"></i>{{ __('Suppliers List') }}
            </h5>
            <small class="text-muted">{{ $suppliers->total() ?? count($suppliers) }} {{ __('suppliers total') }}</small>
        </div>
        <div class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 250px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" placeholder="{{ __('Search suppliers...') }}" id="searchInput">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="suppliersTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0" style="width: 120px;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hash me-1 text-muted"></i>{{ __('Supplier Code') }}
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building me-1 text-muted"></i>{{ __('Name') }}
                            </div>
                        </th>
                        <th class="border-0" style="width: 150px;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tags me-1 text-muted"></i>{{ __('Classification') }}
                            </div>
                        </th>
                        <th class="border-0" style="width: 120px;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-gear me-1 text-muted"></i>{{ __('Actions') }}
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <div class="fw-medium text-primary">{{ $supplier->supplier_code }}</div>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $supplier->name }}</div>
                            </td>
                            <td>
                                @if($supplier->classification === 'food')
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-basket me-1"></i>{{ __('Food') }}
                                    </span>
                                @elseif($supplier->classification === 'non_food')
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="bi bi-box me-1"></i>{{ __('Non-Food') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-question-circle me-1"></i>{{ __('Unknown') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('suppliers.show', $supplier) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="{{ __('View') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="{{ __('Edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this supplier?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="{{ __('Delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-building" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">{{ __('No suppliers found') }}</p>
                                    <small class="text-muted">{{ __('Click "Add Supplier" to create your first supplier') }}</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if(method_exists($suppliers, 'links') && $suppliers->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    {{ __('Showing') }} {{ $suppliers->firstItem() }} {{ __('to') }} {{ $suppliers->lastItem() }} 
                    {{ __('of') }} {{ $suppliers->total() }} {{ __('suppliers') }}
                </small>
                {{ $suppliers->links() }}
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('suppliersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            // Skip empty state row
            if (cells.length === 1) continue;
            
            // Search in supplier code and name
            for (let j = 0; j < 2; j++) {
                if (cells[j] && cells[j].textContent.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    });
});
</script>
@endsection 