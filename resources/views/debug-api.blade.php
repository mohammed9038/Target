@extends('layouts.app')

@section('title', 'API Debug')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">API Debug Dashboard</h1>
        <div class="badge bg-warning text-dark px-3 py-2">
            <i class="bi bi-bug me-1"></i>Debug Mode
        </div>
    </div>
    
    <!-- User Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Current User Info</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Username:</strong> {{ auth()->user()->username }}
                </div>
                <div class="col-md-2">
                    <strong>Role:</strong> 
                    <span class="badge {{ auth()->user()->isAdmin() ? 'bg-danger' : 'bg-info' }}">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
                <div class="col-md-3">
                    <strong>Regions:</strong> 
                    @if(auth()->user()->regions->count() > 0)
                        @foreach(auth()->user()->regions as $region)
                            <span class="badge bg-primary-subtle text-primary me-1">{{ $region->name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">None</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>Channels:</strong>
                    @if(auth()->user()->channels->count() > 0)
                        @foreach(auth()->user()->channels as $channel)
                            <span class="badge bg-info-subtle text-info me-1">{{ $channel->name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">None</span>
                    @endif
                </div>
                <div class="col-md-2">
                    <strong>Classification:</strong> 
                    @if(auth()->user()->classification)
                        <span class="badge bg-warning text-dark">{{ ucfirst(auth()->user()->classification) }}</span>
                    @else
                        <span class="text-muted">None</span>
                    @endif
                </div>
            </div>
            @if(auth()->user()->isManager())
            <div class="row mt-2">
                <div class="col-12">
                    <div class="alert alert-info alert-sm">
                        <strong>Manager Filtering Rules:</strong>
                        <ul class="mb-0 mt-1">
                            <li><strong>Regions:</strong> Only your assigned regions ({{ auth()->user()->regions->pluck('name')->join(', ') ?: 'None' }})</li>
                            <li><strong>Channels:</strong> Only your assigned channels ({{ auth()->user()->channels->pluck('name')->join(', ') ?: 'None' }})</li>
                            <li><strong>Suppliers:</strong> 
                                @if(auth()->user()->classification && auth()->user()->classification !== 'both')
                                    Only {{ auth()->user()->classification }} classification suppliers
                                @else
                                    All suppliers (classification = both)
                                @endif
                            </li>
                            <li><strong>Categories:</strong> Only categories from suppliers with your classification</li>
                            <li><strong>Salesmen:</strong> Only salesmen in your region/channel/classification</li>
                            <li><strong>Target Matrix:</strong> Only combinations of salesmen + suppliers where both match your classification</li>
                            <li><strong>Reports:</strong> Only targets where salesman and supplier classifications match yours</li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif
            </div>
        </div>
    </div>
    
    <!-- API Test Sections -->
    <div class="row">
        <!-- Authentication Test -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-shield-check me-1"></i>Authentication Test</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary btn-sm" onclick="testAuth()">Test Auth Status</button>
                    <div id="auth-result" class="mt-3"></div>
                </div>
            </div>
        </div>
        
        <!-- Dependent Filters Test -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-funnel me-1"></i>Dependent Filters Test</h6>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100" role="group">
                        <button class="btn btn-outline-primary btn-sm" onclick="testEndpoint('/api/deps/regions', 'regions-result')">Test Regions</button>
                        <button class="btn btn-outline-primary btn-sm" onclick="testEndpoint('/api/deps/channels', 'channels-result')">Test Channels</button>
                        <button class="btn btn-outline-primary btn-sm" onclick="testEndpoint('/api/deps/suppliers', 'suppliers-result')">Test Suppliers</button>
                        <button class="btn btn-outline-primary btn-sm" onclick="testEndpoint('/api/deps/categories', 'categories-result')">Test Categories</button>
                        <button class="btn btn-outline-primary btn-sm" onclick="testEndpoint('/api/deps/salesmen', 'salesmen-result')">Test Salesmen</button>
                    </div>
                    <div id="deps-results" class="mt-3">
                        <div id="regions-result"></div>
                        <div id="channels-result"></div>
                        <div id="suppliers-result"></div>
                        <div id="categories-result"></div>
                        <div id="salesmen-result"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Targets API Test -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-target me-1"></i>Targets API Test</h6>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100" role="group">
                        <button class="btn btn-outline-success btn-sm" onclick="testEndpoint('/api/targets', 'targets-all-result')">Test All Targets</button>
                        <button class="btn btn-outline-success btn-sm" onclick="testTargetsWithCurrentPeriod()">Test Current Period</button>
                        <button class="btn btn-outline-success btn-sm" onclick="testTargetMatrix()">Test Target Matrix</button>
                        <button class="btn btn-outline-info btn-sm" onclick="testEndpoint('/debug-matrix', 'debug-matrix-result')">Debug Matrix Data</button>
                        <button class="btn btn-outline-success btn-sm" onclick="testEndpoint('/api/targets?year=2024', 'targets-2024-result')">Test 2024 Targets</button>
                    </div>
                    <div id="targets-results" class="mt-3">
                        <div id="targets-all-result"></div>
                        <div id="targets-current-result"></div>
                        <div id="targets-matrix-result"></div>
                        <div id="debug-matrix-result"></div>
                        <div id="targets-2024-result"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reports API Test -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-1"></i>Reports API Test</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-outline-info btn-sm" onclick="testEndpoint('/api/reports/summary', 'reports-result')">Test Reports Summary</button>
                    <div id="reports-result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Results Display -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-terminal me-1"></i>Test Results Log</h6>
        </div>
        <div class="card-body">
            <div id="results-log" style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 15px; font-family: monospace; font-size: 12px;">
                <div class="text-muted">Test results will appear here...</div>
            </div>
            <div class="mt-2">
                <button class="btn btn-secondary btn-sm" onclick="clearLog()">Clear Log</button>
                <button class="btn btn-primary btn-sm" onclick="runAllTests()">Run All Tests</button>
            </div>
        </div>
    </div>
</div>

<script>
function log(message, type = 'info') {
    const logDiv = document.getElementById('results-log');
    const timestamp = new Date().toLocaleTimeString();
    const colorClass = type === 'error' ? 'text-danger' : type === 'success' ? 'text-success' : 'text-info';
    
    logDiv.innerHTML += `<div class="${colorClass}">[${timestamp}] ${message}</div>`;
    logDiv.scrollTop = logDiv.scrollHeight;
}

function clearLog() {
    document.getElementById('results-log').innerHTML = '<div class="text-muted">Test results will appear here...</div>';
}

async function testEndpoint(url, resultElementId = null) {
    log(`Testing: ${url}`, 'info');
    
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });
        
        const result = {
            status: response.status,
            statusText: response.statusText,
            url: url
        };
        
        if (response.ok) {
            try {
                result.data = await response.json();
                log(`✓ ${url} - Success (${response.status})`, 'success');
                log(`  Data: ${JSON.stringify(result.data).substring(0, 200)}...`, 'info');
            } catch (e) {
                result.data = await response.text();
                log(`✓ ${url} - Success (${response.status}) - Text response`, 'success');
            }
        } else {
            try {
                result.error = await response.json();
                log(`✗ ${url} - Error (${response.status}): ${JSON.stringify(result.error)}`, 'error');
            } catch (e) {
                result.error = await response.text();
                log(`✗ ${url} - Error (${response.status}): ${result.error}`, 'error');
            }
        }
        
        // Display in specific result element if provided
        if (resultElementId) {
            const element = document.getElementById(resultElementId);
            if (element) {
                const isSuccess = response.status === 200;
                element.innerHTML = `
                    <div class="alert ${isSuccess ? 'alert-success' : 'alert-danger'} alert-sm p-2 mt-2">
                        <strong>${response.status} ${response.statusText}</strong><br>
                        ${isSuccess ? 
                            `<small>Count: ${Array.isArray(result.data) ? result.data.length : (result.data?.data?.length || 'N/A')}</small>
                             ${Array.isArray(result.data) && result.data.length > 0 ? 
                                `<br><small class="text-muted">Sample: ${result.data[0].name || result.data[0].username || 'N/A'}</small>` : 
                                ''
                             }` :
                            `<small>${JSON.stringify(result.error || result.statusText)}</small>`
                        }
                    </div>
                `;
            }
        }
        
        return result;
        
    } catch (error) {
        log(`✗ ${url} - Network Error: ${error.message}`, 'error');
        
        if (resultElementId) {
            const element = document.getElementById(resultElementId);
            if (element) {
                element.innerHTML = `
                    <div class="alert alert-danger alert-sm p-2 mt-2">
                        <strong>Network Error</strong><br>
                        <small>${error.message}</small>
                    </div>
                `;
            }
        }
        
        return { error: error.message };
    }
}

async function testAuth() {
    const result = await testEndpoint('/api/test-auth', 'auth-result');
    if (result.data) {
        document.getElementById('auth-result').innerHTML = `
            <div class="alert alert-info alert-sm p-2">
                <strong>Auth Status:</strong> ${result.data.authenticated ? 'Authenticated' : 'Not Authenticated'}<br>
                <strong>User:</strong> ${result.data.user || 'None'}<br>
                <strong>Guard:</strong> ${result.data.guard || 'None'}
            </div>
        `;
    }
}

async function testTargetsWithCurrentPeriod() {
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    const url = `/api/targets?year=${currentYear}&month=${currentMonth}`;
    await testEndpoint(url, 'targets-current-result');
}

async function testTargetMatrix() {
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    const url = `/api/targets/matrix?year=${currentYear}&month=${currentMonth}`;
    await testEndpoint(url, 'targets-matrix-result');
}

async function runAllTests() {
    log('=== Running All Tests ===', 'info');
    
    await testAuth();
    await testEndpoint('/api/deps/regions', 'regions-result');
    await testEndpoint('/api/deps/channels', 'channels-result');
    await testEndpoint('/api/deps/suppliers', 'suppliers-result');
    await testEndpoint('/api/deps/categories', 'categories-result');
    await testEndpoint('/api/deps/salesmen', 'salesmen-result');
    await testEndpoint('/api/targets', 'targets-all-result');
    await testTargetsWithCurrentPeriod();
    await testTargetMatrix();
    await testEndpoint('/api/reports/summary', 'reports-result');
    
    log('=== All Tests Completed ===', 'success');
}

// Auto-run auth test on page load
window.onload = function() {
    testAuth();
};
</script>

<style>
.alert-sm {
    font-size: 0.875rem;
}
.btn-group-vertical .btn {
    margin-bottom: 2px;
}
</style>
@endsection