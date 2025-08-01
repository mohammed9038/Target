@extends('layouts.app')

@section('title', __('Add Supplier'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ __('Add Supplier') }}</h1>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Suppliers') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Supplier Name') }} *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>{{ __('Supplier code will be auto-generated') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="classification" class="form-label">{{ __('Classification') }} *</label>
                            <select class="form-select @error('classification') is-invalid @enderror" 
                                    id="classification" name="classification" required>
                                <option value="">{{ __('Select Classification') }}</option>
                                <option value="food" {{ old('classification') === 'food' ? 'selected' : '' }}>{{ __('Food') }}</option>
                                <option value="non_food" {{ old('classification') === 'non_food' ? 'selected' : '' }}>{{ __('Non-Food') }}</option>
                            </select>
                            @error('classification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Save Supplier') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 