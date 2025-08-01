@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ __('Add New Target') }}</h1>
                    <p class="text-muted mb-0">{{ __('Create a new sales target for a specific salesman') }}</p>
                </div>
                <a href="{{ route('targets.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Targets') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Target Information') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('targets.store') }}" method="POST">
                        @csrf
                        
                        <!-- Period Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year" class="form-label">{{ __('Year') }} *</label>
                                    <select class="form-select @error('year') is-invalid @enderror" id="year" name="year" required>
                                        <option value="">{{ __('Select Year') }}</option>
                                        @for($y = date('Y'); $y <= date('Y') + 2; $y++)
                                            <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="month" class="form-label">{{ __('Month') }} *</label>
                                    <select class="form-select @error('month') is-invalid @enderror" id="month" name="month" required>
                                        <option value="">{{ __('Select Month') }}</option>
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Location Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="region_id" class="form-label">{{ __('Region') }} *</label>
                                    <select class="form-select @error('region_id') is-invalid @enderror" id="region_id" name="region_id" required>
                                        <option value="">{{ __('Select Region') }}</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }}
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
                                    <select class="form-select @error('channel_id') is-invalid @enderror" id="channel_id" name="channel_id" required>
                                        <option value="">{{ __('Select Channel') }}</option>
                                        @foreach($channels as $channel)
                                            <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                                {{ $channel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('channel_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Salesman Selection -->
                        <div class="mb-3">
                            <label for="salesman_id" class="form-label">{{ __('Salesman') }} *</label>
                            <select class="form-select @error('salesman_id') is-invalid @enderror" id="salesman_id" name="salesman_id" required>
                                <option value="">{{ __('Select Salesman') }}</option>
                                @foreach($salesmen as $salesman)
                                    <option value="{{ $salesman->id }}" {{ old('salesman_id') == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->name }} ({{ $salesman->salesman_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('salesman_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Product Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">{{ __('Supplier') }} *</label>
                                    <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                        <option value="">{{ __('Select Supplier') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">{{ __('Category') }} *</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">{{ __('Select Category') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }} ({{ $category->supplier->name ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Target Amount -->
                        <div class="mb-3">
                            <label for="target_amount" class="form-label">{{ __('Target Amount') }} *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('target_amount') is-invalid @enderror" 
                                       id="target_amount" name="target_amount" value="{{ old('target_amount') }}" 
                                       step="0.01" min="0" required>
                                <span class="input-group-text">{{ __('USD') }}</span>
                            </div>
                            @error('target_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('targets.index') }}" class="btn btn-outline-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('Create Target') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-1"></i>{{ __('Quick Help') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info alert-sm">
                        <h6 class="alert-heading">{{ __('Individual Target Creation') }}</h6>
                        <p class="mb-2">{{ __('Use this form to create a single target for a specific salesman, supplier, and category combination.') }}</p>
                        <hr>
                        <p class="mb-0">
                            <strong>{{ __('For bulk targets:') }}</strong><br>
                            <a href="{{ route('targets.index') }}" class="text-decoration-none">
                                {{ __('Use the Target Matrix →') }}
                            </a>
                        </p>
                    </div>

                    @if($activePeriods->isNotEmpty())
                        <div class="alert alert-success alert-sm">
                            <h6 class="alert-heading">{{ __('Active Periods') }}</h6>
                            @foreach($activePeriods as $period)
                                <div class="d-flex justify-content-between">
                                    <span>{{ date('F Y', mktime(0, 0, 0, $period->month, 1, $period->year)) }}</span>
                                    <span class="badge bg-success-subtle text-success">{{ __('Open') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning alert-sm">
                            <h6 class="alert-heading">{{ __('No Active Periods') }}</h6>
                            <p class="mb-0">{{ __('No periods are currently open for target entry. Please contact an administrator.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter categories based on selected supplier
    const supplierSelect = document.getElementById('supplier_id');
    const categorySelect = document.getElementById('category_id');
    const allCategories = Array.from(categorySelect.options);

    supplierSelect.addEventListener('change', function() {
        const selectedSupplierId = this.value;
        
        // Clear current options except the first one
        categorySelect.innerHTML = '<option value="">{{ __("Select Category") }}</option>';
        
        if (selectedSupplierId) {
            // Filter categories for the selected supplier
            const filteredCategories = allCategories.filter(option => {
                return option.value === '' || option.textContent.includes('(') && 
                       option.textContent.split('(')[1].includes(supplierSelect.options[supplierSelect.selectedIndex].text);
            });
            
            filteredCategories.forEach(option => {
                if (option.value !== '') {
                    categorySelect.appendChild(option.cloneNode(true));
                }
            });
        }
    });

    // Filter salesmen based on selected region and channel
    const regionSelect = document.getElementById('region_id');
    const channelSelect = document.getElementById('channel_id');
    const salesmanSelect = document.getElementById('salesman_id');
    const allSalesmen = Array.from(salesmanSelect.options);

    function filterSalesmen() {
        const selectedRegionId = regionSelect.value;
        const selectedChannelId = channelSelect.value;
        
        // Clear current options except the first one
        salesmanSelect.innerHTML = '<option value="">{{ __("Select Salesman") }}</option>';
        
        if (selectedRegionId && selectedChannelId) {
            // In a real implementation, you'd make an AJAX call to get filtered salesmen
            // For now, show all salesmen (you can enhance this later)
            allSalesmen.forEach(option => {
                if (option.value !== '') {
                    salesmanSelect.appendChild(option.cloneNode(true));
                }
            });
        }
    }

    regionSelect.addEventListener('change', filterSalesmen);
    channelSelect.addEventListener('change', filterSalesmen);
});
</script>
@endsection