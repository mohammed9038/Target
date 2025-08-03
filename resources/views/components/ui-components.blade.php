{{-- Reusable UI Components for Target Management System --}}

{{-- Enhanced Button Component --}}
@php
    $btnClass = 'btn btn-enhanced ' . ($variant ?? 'btn-primary');
    $icon = $icon ?? null;
    $loading = $loading ?? false;
@endphp

<button type="{{ $type ?? 'button' }}" 
        class="{{ $btnClass }} {{ $class ?? '' }}"
        @isset($onclick) onclick="{{ $onclick }}" @endisset
        @if($loading) disabled @endif
        {{ $attributes }}>
    @if($loading)
        <span class="spinner-border spinner-border-sm me-2"></span>
    @elseif($icon)
        <i class="{{ $icon }} me-2"></i>
    @endif
    {{ $slot }}
</button>

{{-- Enhanced Card Component --}}
@php
    $cardClass = 'card card-enhanced ' . ($class ?? '');
@endphp

<div class="{{ $cardClass }}" {{ $attributes }}>
    @isset($header)
        <div class="card-header">
            @if(isset($headerIcon))
                <i class="{{ $headerIcon }} me-2"></i>
            @endif
            {{ $header }}
        </div>
    @endisset
    
    <div class="card-body {{ $bodyClass ?? '' }}">
        {{ $slot }}
    </div>
    
    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>

{{-- Loading Skeleton Component --}}
<div class="skeleton-container {{ $class ?? '' }}">
    @for($i = 0; $i < ($count ?? 3); $i++)
        <div class="skeleton skeleton-text {{ $size ?? 'medium' }}"></div>
    @endfor
</div>

{{-- Stats Card Component --}}
@php
    $statClass = 'stat-card ' . ($class ?? '');
    $valueClass = 'stat-value ' . ($valueColor ?? 'text-primary');
@endphp

<div class="{{ $statClass }}">
    @isset($icon)
        <div class="stat-icon mb-2">
            <i class="{{ $icon }} fs-3 {{ $iconColor ?? 'text-primary' }}"></i>
        </div>
    @endisset
    
    <div class="{{ $valueClass }}" id="{{ $valueId ?? '' }}">
        {{ $value ?? '-' }}
    </div>
    
    <div class="stat-label">
        {{ $label }}
    </div>
    
    @isset($trend)
        <div class="stat-trend mt-1">
            <small class="{{ $trend > 0 ? 'text-success' : 'text-danger' }}">
                <i class="bi bi-{{ $trend > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                {{ abs($trend) }}%
            </small>
        </div>
    @endisset
</div>

{{-- Enhanced Form Input Component --}}
@php
    $inputClass = 'form-control form-control-enhanced ' . ($class ?? '');
    $labelClass = 'form-label form-label-enhanced ' . ($labelClass ?? '');
@endphp

<div class="mb-3">
    @isset($label)
        <label for="{{ $id }}" class="{{ $labelClass }}">
            @isset($labelIcon)
                <i class="{{ $labelIcon }} me-1"></i>
            @endisset
            {{ $label }}
            @if($required ?? false)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endisset
    
    @if($type === 'select')
        <select class="{{ $inputClass }}" id="{{ $id }}" name="{{ $name ?? $id }}" 
                @if($required ?? false) required @endif
                {{ $attributes }}>
            @if($placeholder ?? false)
                <option value="">{{ $placeholder }}</option>
            @endif
            {{ $slot }}
        </select>
    @else
        <input type="{{ $type ?? 'text' }}" 
               class="{{ $inputClass }}" 
               id="{{ $id }}" 
               name="{{ $name ?? $id }}"
               value="{{ $value ?? old($id) }}"
               placeholder="{{ $placeholder ?? '' }}"
               @if($required ?? false) required @endif
               {{ $attributes }}>
    @endif
    
    @isset($help)
        <div class="form-text">{{ $help }}</div>
    @endisset
    
    @error($id)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

{{-- Enhanced Table Component --}}
<div class="table-responsive">
    <table class="table table-enhanced {{ $class ?? '' }}" {{ $attributes }}>
        @isset($headers)
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th scope="col">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endisset
        
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

{{-- Toast Notification Component --}}
<div class="toast align-items-center text-white bg-{{ $type ?? 'info' }} border-0" 
     role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body">
            @isset($icon)
                <i class="{{ $icon }} me-2"></i>
            @endif
            {{ $message }}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>

{{-- Modal Component --}}
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog {{ $size ?? '' }}">
        <div class="modal-content">
            @isset($title)
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $id }}Label">
                        @isset($titleIcon)
                            <i class="{{ $titleIcon }} me-2"></i>
                        @endisset
                        {{ $title }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            @endisset
            
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>

{{-- Progress Bar Component --}}
@php
    $progressClass = 'progress ' . ($class ?? '');
    $barClass = 'progress-bar ' . ($barClass ?? 'bg-primary');
@endphp

<div class="{{ $progressClass }}" style="height: {{ $height ?? '8px' }};">
    <div class="{{ $barClass }}" 
         role="progressbar" 
         style="width: {{ $value ?? 0 }}%;" 
         aria-valuenow="{{ $value ?? 0 }}" 
         aria-valuemin="0" 
         aria-valuemax="100">
        @if($showLabel ?? false)
            {{ $value ?? 0 }}%
        @endif
    </div>
</div>

{{-- Badge Component --}}
@php
    $badgeClass = 'badge ' . ($variant ?? 'bg-primary') . ' ' . ($class ?? '');
@endphp

<span class="{{ $badgeClass }}" {{ $attributes }}>
    @isset($icon)
        <i class="{{ $icon }} me-1"></i>
    @endisset
    {{ $slot }}
</span>

{{-- Alert Component --}}
@php
    $alertClass = 'alert alert-' . ($type ?? 'info') . ' ' . ($class ?? '');
@endphp

<div class="{{ $alertClass }}" role="alert" {{ $attributes }}>
    @if($dismissible ?? false)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
    
    @isset($icon)
        <i class="{{ $icon }} me-2"></i>
    @endif
    
    @isset($title)
        <h6 class="alert-heading">{{ $title }}</h6>
    @endisset
    
    {{ $slot }}
</div>