@extends('layouts.app')

@section('title', __('Add Salesman'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ __('Add Salesman') }}</h1>
                <a href="{{ route('salesmen.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Salesmen') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('salesmen.store') }}" method="POST">
                        @csrf
                        
                                                <div class="mb-3">
                            <div class="alert alert-info alert-sm">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ __('Salesman code will be auto-generated when you save') }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="employee_code" class="form-label">{{ __('Employee Code') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <input type="text" class="form-control @error('employee_code') is-invalid @enderror"
                                   id="employee_code" name="employee_code" value="{{ old('employee_code') }}">
                            @error('employee_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }} *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="region_id" class="form-label">{{ __('Region') }} *</label>
                                    <select class="form-select @error('region_id') is-invalid @enderror" 
                                            id="region_id" name="region_id" required>
                                        <option value="">{{ __('Select Region') }}</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }} ({{ $region->region_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="channel_id" class="form-label">{{ __('Channel') }} *</label>
                                    <select class="form-select @error('channel_id') is-invalid @enderror" 
                                            id="channel_id" name="channel_id" required>
                                        <option value="">{{ __('Select Channel') }}</option>
                                        @foreach($channels as $channel)
                                            <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                                {{ $channel->name }} ({{ $channel->channel_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('channel_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="classification" class="form-label">{{ __('Classification') }} *</label>
                            <select class="form-select @error('classification') is-invalid @enderror" 
                                    id="classification" name="classification" required>
                                <option value="">{{ __('Select Classification') }}</option>
                                <option value="food" {{ old('classification') === 'food' ? 'selected' : '' }}>{{ __('Food') }}</option>
                                <option value="non_food" {{ old('classification') === 'non_food' ? 'selected' : '' }}>{{ __('Non-Food') }}</option>
                                <option value="both" {{ old('classification') === 'both' ? 'selected' : '' }}>{{ __('Both') }}</option>
                            </select>
                            @error('classification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Save Salesman') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 