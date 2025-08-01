@extends('layouts.app')

@section('title', __('Users'))

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1">{{ __('Users') }}</h1>
        <p class="text-muted mb-0">{{ __('Manage system users and permissions') }}</p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>{{ __('Add User') }}
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

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 me-3">
                    <i class="bi bi-person-gear me-2"></i>{{ __('Users List') }}
                </h5>
                <small class="text-muted">
                    {{ count($users) }} {{ __('records') }}
                </small>
            </div>
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="{{ __('Search users...') }}">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person me-2 text-muted"></i>{{ __('Username') }}
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-shield me-2 text-muted"></i>{{ __('Role') }}
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2 text-muted"></i>{{ __('Region') }}
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-diagram-3 me-2 text-muted"></i>{{ __('Channel') }}
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tags me-2 text-muted"></i>{{ __('Classification') }}
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock me-2 text-muted"></i>{{ __('Created') }}
                            </div>
                        </th>
                        <th class="border-0 text-center" style="width: 120px;">
                            <i class="bi bi-gear me-1 text-muted"></i>{{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4">
                                <div class="fw-medium text-dark">
                                    {{ $user->username }}
                                    @if($user->id === auth()->id())
                                        <small class="text-primary ms-2">
                                            <i class="bi bi-star-fill me-1"></i>{{ __('You') }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($user->isAdmin())
                                    <span class="badge bg-danger-subtle text-danger px-2">
                                        <i class="bi bi-shield-fill me-1"></i>{{ __('Admin') }}
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info px-2">
                                        <i class="bi bi-person-check me-1"></i>{{ __('Manager') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->region)
                                    <div class="text-muted small">{{ $user->region->name }}</div>
                                @else
                                    <span class="text-muted small">
                                        <i class="bi bi-dash-circle me-1"></i>{{ __('All Regions') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->channel)
                                    <div class="text-muted small">{{ $user->channel->name }}</div>
                                @else
                                    <span class="text-muted small">
                                        <i class="bi bi-dash-circle me-1"></i>{{ __('All Channels') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $userClassifications = $user->getClassificationListAttribute();
                                @endphp
                                @if(!empty($userClassifications))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($userClassifications as $classification)
                                            @if($classification === 'food')
                                                <span class="badge bg-success-subtle text-success px-2">
                                                    <i class="bi bi-apple me-1"></i>{{ __('Food') }}
                                                </span>
                                            @elseif($classification === 'non_food')
                                                <span class="badge bg-info-subtle text-info px-2">
                                                    <i class="bi bi-box me-1"></i>{{ __('Non-Food') }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">
                                        <i class="bi bi-dash-circle me-1"></i>{{ __('No Classifications') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">{{ $user->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('users.show', $user) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="{{ __('View') }}"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="{{ __('Edit') }}"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('{{ __('Are you sure? This will permanently delete the user.') }}')" 
                                                    title="{{ __('Delete') }}"
                                                    data-bs-toggle="tooltip">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-person-gear" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">{{ __('No users found') }}</p>
                                    <small class="text-muted">{{ __('Try adjusting your search or create a new user') }}</small>
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
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

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