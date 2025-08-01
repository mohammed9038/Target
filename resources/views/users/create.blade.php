@extends('layouts.app')

@section('title', __('Add User'))

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1">{{ __('Add User') }}</h1>
        <p class="text-muted mb-0">{{ __('Create a new user account') }}</p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
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
                    <i class="bi bi-person-plus me-2"></i>{{ __('User Information') }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-medium">
                                    <i class="bi bi-person me-1"></i>{{ __('Username') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username') }}" 
                                       placeholder="{{ __('Enter username') }}"
                                       required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label fw-medium">
                                    <i class="bi bi-shield me-1"></i>{{ __('Role') }} <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">{{ __('Select Role') }}</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                                        {{ __('Admin') }} - {{ __('Full system access') }}
                                    </option>
                                    <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>
                                        {{ __('Manager') }} - {{ __('Limited to assigned region/channel') }}
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">
                                    <i class="bi bi-lock me-1"></i>{{ __('Password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="{{ __('Enter password') }}"
                                           required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="password-icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">{{ __('Minimum 6 characters') }}</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-medium">
                                    <i class="bi bi-lock-fill me-1"></i>{{ __('Confirm Password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="{{ __('Confirm password') }}"
                                           required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="scope-section" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>{{ __('Manager Scope') }}</strong><br>
                                {{ __('Managers can only see and manage data within their assigned regions and channels. Select multiple regions/channels or leave blank for access to all.') }}
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="bi bi-geo-alt me-1"></i>{{ __('Regions') }}
                                </label>
                                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="all_regions" onclick="toggleAllRegions()">
                                        <label class="form-check-label fw-medium text-primary" for="all_regions">
                                            {{ __('All Regions') }}
                                        </label>
                                    </div>
                                    <hr class="my-2">
                                    @foreach($regions as $region)
                                        <div class="form-check">
                                            <input class="form-check-input region-checkbox" type="checkbox" 
                                                   name="region_ids[]" value="{{ $region->id }}" 
                                                   id="region_{{ $region->id }}"
                                                   {{ in_array($region->id, old('region_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="region_{{ $region->id }}">
                                                {{ $region->name }} <small class="text-muted">({{ $region->region_code }})</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('region_ids')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('region_ids.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="bi bi-diagram-3 me-1"></i>{{ __('Channels') }}
                                </label>
                                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="all_channels" onclick="toggleAllChannels()">
                                        <label class="form-check-label fw-medium text-primary" for="all_channels">
                                            {{ __('All Channels') }}
                                        </label>
                                    </div>
                                    <hr class="my-2">
                                    @foreach($channels as $channel)
                                        <div class="form-check">
                                            <input class="form-check-input channel-checkbox" type="checkbox" 
                                                   name="channel_ids[]" value="{{ $channel->id }}" 
                                                   id="channel_{{ $channel->id }}"
                                                   {{ in_array($channel->id, old('channel_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="channel_{{ $channel->id }}">
                                                {{ $channel->name }} <small class="text-muted">({{ $channel->channel_code }})</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('channel_ids')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('channel_ids.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="classification-section" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="bi bi-funnel me-2"></i>
                                <strong>{{ __('Classification Filter') }}</strong><br>
                                {{ __('Optionally restrict this manager to only see salesmen with a specific classification. Leave blank for access to all classifications.') }}
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="classification" class="form-label fw-medium">
                                    <i class="bi bi-tags me-1"></i>{{ __('Classification') }}
                                </label>
                                <select class="form-select @error('classification') is-invalid @enderror" id="classification" name="classification">
                                    <option value="">{{ __('All Classifications') }}</option>
                                    <option value="food" {{ old('classification') === 'food' ? 'selected' : '' }}>
                                        {{ __('Food') }}
                                    </option>
                                    <option value="non_food" {{ old('classification') === 'non_food' ? 'selected' : '' }}>
                                        {{ __('Non-Food') }}
                                    </option>
                                    <option value="both" {{ old('classification') === 'both' ? 'selected' : '' }}>
                                        {{ __('Both') }}
                                    </option>
                                </select>
                                @error('classification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>{{ __('Create User') }}
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>{{ __('Role Permissions') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-danger">
                        <i class="bi bi-shield-fill me-1"></i>{{ __('Admin') }}
                    </h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li><i class="bi bi-check text-success me-1"></i>{{ __('Manage all master data') }}</li>
                        <li><i class="bi bi-check text-success me-1"></i>{{ __('Create/edit users') }}</li>
                        <li><i class="bi bi-check text-success me-1"></i>{{ __('Access all regions/channels') }}</li>
                        <li><i class="bi bi-check text-success me-1"></i>{{ __('Manage periods') }}</li>
                        <li><i class="bi bi-check text-success me-1"></i>{{ __('Full system access') }}</li>
                    </ul>
                </div>
                
                <div>
                    <h6 class="text-info">
                        <i class="bi bi-person-check me-1"></i>{{ __('Manager') }}
                    </h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li><i class="bi bi-check text-success me-1"></i>{{ __('Manage targets') }}</li>
                        <li><i class="bi bi-check text-success me-1"></i>{{ __('View reports') }}</li>
                        <li><i class="bi bi-x text-danger me-1"></i>{{ __('Limited to assigned scope') }}</li>
                        <li><i class="bi bi-x text-danger me-1"></i>{{ __('Cannot manage master data') }}</li>
                        <li><i class="bi bi-x text-danger me-1"></i>{{ __('Cannot create users') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const scopeSection = document.getElementById('scope-section');
    const classificationSection = document.getElementById('classification-section');
    
    function toggleScopeSection() {
        if (roleSelect.value === 'manager') {
            scopeSection.style.display = 'block';
            classificationSection.style.display = 'block';
        } else {
            scopeSection.style.display = 'none';
            classificationSection.style.display = 'none';
        }
    }
    
    roleSelect.addEventListener('change', toggleScopeSection);
    toggleScopeSection(); // Initial check
});

function toggleAllRegions() {
    const allRegionsCheckbox = document.getElementById('all_regions');
    const regionCheckboxes = document.querySelectorAll('.region-checkbox');
    
    regionCheckboxes.forEach(checkbox => {
        checkbox.checked = allRegionsCheckbox.checked;
    });
}

function toggleAllChannels() {
    const allChannelsCheckbox = document.getElementById('all_channels');
    const channelCheckboxes = document.querySelectorAll('.channel-checkbox');
    
    channelCheckboxes.forEach(checkbox => {
        checkbox.checked = allChannelsCheckbox.checked;
    });
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endpush
@endsection