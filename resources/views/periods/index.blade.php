@extends('layouts.app')

@section('title', __('Active Periods'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-1">{{ __('Active Periods') }}</h1>
        <p class="text-muted mb-0">{{ __('Manage active sales periods') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('periods.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>{{ __('Add Period') }}
        </a>
    </div>
</div>

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

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 me-3">
                    <i class="bi bi-calendar me-2"></i>{{ __('Periods List') }}
                </h5>
                <small class="text-muted">
                    {{ count($periods) }} {{ __('records') }}
                </small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('periods.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm" id="status" name="status" style="width: 150px;">
                        <option value="open" {{ $status === 'open' ? 'selected' : '' }}>{{ __('Open Periods') }}</option>
                        <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>{{ __('Closed Periods') }}</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>{{ __('All Periods') }}</option>
                    </select>
                    <select class="form-select form-select-sm" id="year" name="year" style="width: 120px;">
                        <option value="">{{ __('All Years') }}</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" id="month" name="month" style="width: 130px;">
                        <option value="">{{ __('All Months') }}</option>
                        @foreach($months as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('periods.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>{{ __('Clear') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="periodsTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-date me-2 text-muted"></i>{{ __('Year') }}
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-month me-2 text-muted"></i>{{ __('Month') }}
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-activity me-2 text-muted"></i>{{ __('Status') }}
                            </div>
                        </th>
                        <th class="border-0 text-center" style="width: 120px;">
                            <i class="bi bi-gear me-1 text-muted"></i>{{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $period)
                        <tr>
                            <td class="px-4">
                                <code class="bg-primary-subtle text-primary px-2 py-1 rounded small">{{ $period->year }}</code>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ date('F', mktime(0, 0, 0, $period->month, 1)) }}</div>
                            </td>
                            <td>
                                @if($period->is_open)
                                    <span class="badge bg-success-subtle text-success px-2">
                                        <i class="bi bi-unlock me-1"></i>{{ __('Open') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2">
                                        <i class="bi bi-lock me-1"></i>{{ __('Closed') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <form action="{{ route('periods.update', $period) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_open" value="{{ $period->is_open ? '0' : '1' }}">
                                        <button type="submit" 
                                                class="btn btn-sm {{ $period->is_open ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                title="{{ $period->is_open ? __('Close Period') : __('Open Period') }}"
                                                data-bs-toggle="tooltip">
                                            <i class="bi {{ $period->is_open ? 'bi-lock' : 'bi-unlock' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('periods.show', $period) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="{{ __('View') }}"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('periods.destroy', $period) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('{{ __('Are you sure?') }}')" 
                                                title="{{ __('Delete') }}"
                                                data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">{{ __('No periods found') }}</p>
                                    <small class="text-muted">{{ __('Try adjusting your filters or create a new period') }}</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection 