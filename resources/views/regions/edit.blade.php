@extends('layouts.app')

@section('title', __('Edit Region'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ __('Edit Region') }}</h1>
                <a href="{{ route('regions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Regions') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('regions.update', $region) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="region_code" class="form-label">{{ __('Region Code') }}</label>
                            <input type="text" class="form-control" id="region_code" value="{{ $region->region_code }}" readonly>
                            <div class="form-text">
                                <i class="bi bi-lock me-1"></i>{{ __('Region code is auto-generated and cannot be changed') }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Region Name') }} *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $region->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $region->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ __('Active') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Update Region') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 