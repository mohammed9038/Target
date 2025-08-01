@extends('layouts.app')

@section('title', __('User Details'))

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1">{{ __('User Details') }}</h1>
        <p class="text-muted mb-0">{{ __('View user account information') }}</p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>{{ __('Edit User') }}
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Users') }}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i>{{ __('User Information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">
                                <i class="bi bi-person me-1"></i>{{ __('Username') }}
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $user->username }}</h5>
                                    @if($user->id === auth()->id())
                                        <small class="text-primary">
                                            <i class="bi bi-star-fill me-1"></i>{{ __('Your Account') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">
                                <i class="bi bi-shield me-1"></i>{{ __('Role') }}
                            </label>
                            <div>
                                @if($user->isAdmin())
                                    <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2">
                                        <i class="bi bi-shield-fill me-2"></i>{{ __('Administrator') }}
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-muted">{{ __('Full system access and user management') }}</small>
                                    </div>
                                @else
                                    <span class="badge bg-info-subtle text-info fs-6 px-3 py-2">
                                        <i class="bi bi-person-check me-2"></i>{{ __('Manager') }}
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-muted">{{ __('Limited to assigned region and channel') }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($user->isManager())
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-medium text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ __('Assigned Region') }}
                                </label>
                                <div>
                                    @if($user->region)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                                <i class="bi bi-geo-alt text-success"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->region->name }}</h6>
                                                <small class="text-muted">{{ $user->region->region_code }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center text-muted">
                                            <div class="bg-secondary bg-opacity-10 p-2 rounded me-3">
                                                <i class="bi bi-dash-circle text-secondary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ __('All Regions') }}</h6>
                                                <small>{{ __('No specific region assigned') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-medium text-muted">
                                    <i class="bi bi-diagram-3 me-1"></i>{{ __('Assigned Channel') }}
                                </label>
                                <div>
                                    @if($user->channel)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                                <i class="bi bi-diagram-3 text-warning"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->channel->name }}</h6>
                                                <small class="text-muted">{{ $user->channel->channel_code }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center text-muted">
                                            <div class="bg-secondary bg-opacity-10 p-2 rounded me-3">
                                                <i class="bi bi-dash-circle text-secondary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ __('All Channels') }}</h6>
                                                <small>{{ __('No specific channel assigned') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($user->isManager() && $user->classification)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-medium text-muted">
                                        <i class="bi bi-tags me-1"></i>{{ __('Classification Filter') }}
                                    </label>
                                    <div>
                                        @if($user->classification === 'food')
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                                    <i class="bi bi-apple text-success"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ __('Food Only') }}</h6>
                                                    <small class="text-muted">{{ __('Can only manage food salesmen') }}</small>
                                                </div>
                                            </div>
                                        @elseif($user->classification === 'non_food')
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                                    <i class="bi bi-box text-info"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ __('Non-Food Only') }}</h6>
                                                    <small class="text-muted">{{ __('Can only manage non-food salesmen') }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                                    <i class="bi bi-collection text-warning"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ __('Both Classifications') }}</h6>
                                                    <small class="text-muted">{{ __('Can manage both food and non-food salesmen') }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">
                                <i class="bi bi-calendar-plus me-1"></i>{{ __('Account Created') }}
                            </label>
                            <div>
                                <h6 class="mb-0">{{ $user->created_at->format('F d, Y') }}</h6>
                                <small class="text-muted">{{ $user->created_at->format('g:i A') }} ({{ $user->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">
                                <i class="bi bi-arrow-clockwise me-1"></i>{{ __('Last Updated') }}
                            </label>
                            <div>
                                <h6 class="mb-0">{{ $user->updated_at->format('F d, Y') }}</h6>
                                <small class="text-muted">{{ $user->updated_at->format('g:i A') }} ({{ $user->updated_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-gear me-2"></i>{{ __('Quick Actions') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>{{ __('Edit User') }}
                    </a>
                    
                    @if($user->id !== auth()->id())
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-2"></i>{{ __('Delete User') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>{{ __('Permissions') }}
                </h6>
            </div>
            <div class="card-body">
                @if($user->isAdmin())
                    <div class="mb-3">
                        <h6 class="text-danger mb-2">
                            <i class="bi bi-shield-fill me-1"></i>{{ __('Administrator Access') }}
                        </h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Manage all master data') }}</li>
                            <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Create and edit users') }}</li>
                            <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Access all regions and channels') }}</li>
                            <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Manage active periods') }}</li>
                            <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Full system access') }}</li>
                        </ul>
                    </div>
                @else
                    <div class="mb-3">
                        <h6 class="text-info mb-2">
                            <i class="bi bi-person-check me-1"></i>{{ __('Manager Access') }}
                        </h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Manage sales targets') }}</li>
                            <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i>{{ __('View and generate reports') }}</li>
                            <li class="mb-1"><i class="bi bi-x-circle text-danger me-2"></i>{{ __('Limited to assigned scope') }}</li>
                            <li class="mb-1"><i class="bi bi-x-circle text-danger me-2"></i>{{ __('Cannot manage master data') }}</li>
                            <li class="mb-1"><i class="bi bi-x-circle text-danger me-2"></i>{{ __('Cannot create users') }}</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        
        @if($user->id === auth()->id())
            <div class="card mt-3">
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <small><strong>{{ __('Note:') }}</strong> {{ __('This is your current account. You cannot delete your own account.') }}</small>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if($user->id !== auth()->id())
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>{{ __('Delete User') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to delete this user?') }}</p>
                <div class="alert alert-warning">
                    <strong>{{ __('Warning:') }}</strong> {{ __('This action cannot be undone. The user will be permanently removed from the system.') }}
                </div>
                <div class="bg-light p-3 rounded">
                    <strong>{{ __('User to be deleted:') }}</strong><br>
                    <i class="bi bi-person me-1"></i>{{ $user->username }}<br>
                    <i class="bi bi-shield me-1"></i>{{ $user->isAdmin() ? __('Admin') : __('Manager') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>{{ __('Delete User') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection