@extends('layouts.app')

@section('title', 'Bootstrap Test')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-5">Bootstrap Functionality Test</h1>
            
            <!-- Test 1: Basic Grid System -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Test 1: Grid System</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="alert alert-primary">Column 1</div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-success">Column 2</div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning">Column 3</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Test 2: Buttons -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Test 2: Buttons</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary me-2">Primary</button>
                    <button class="btn btn-secondary me-2">Secondary</button>
                    <button class="btn btn-success me-2">Success</button>
                    <button class="btn btn-danger me-2">Danger</button>
                    <button class="btn btn-warning me-2">Warning</button>
                    <button class="btn btn-info me-2">Info</button>
                    <button class="btn btn-outline-primary me-2">Outline Primary</button>
                </div>
            </div>
            
            <!-- Test 3: Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Test 3: Table</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td>john@example.com</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>jane@example.com</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Test 4: Forms -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Test 4: Forms</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Enter name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" placeholder="Enter email">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" rows="3" placeholder="Enter message"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select</label>
                            <select class="form-select">
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="check1">
                            <label class="form-check-label" for="check1">
                                Check me out
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
            
            <!-- Test 5: Alerts -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Test 5: Alerts</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-primary" role="alert">
                        This is a primary alert!
                    </div>
                    <div class="alert alert-success" role="alert">
                        This is a success alert!
                    </div>
                    <div class="alert alert-warning" role="alert">
                        This is a warning alert!
                    </div>
                    <div class="alert alert-danger" role="alert">
                        This is a danger alert!
                    </div>
                </div>
            </div>
            
            <!-- Test 6: Modal -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Test 6: Modal</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testModal">
                        Open Modal
                    </button>
                </div>
            </div>
            
            <!-- Test 7: Dropdown -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Test 7: Dropdown</h5>
                </div>
                <div class="card-body">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Dropdown button
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Test Results -->
            <div class="card">
                <div class="card-header">
                    <h5>Test Results</h5>
                </div>
                <div class="card-body">
                    <div id="test-results">
                        <p class="text-muted">Running tests...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>This is a test modal to verify Bootstrap functionality.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const results = document.getElementById('test-results');
    let testResults = [];
    
    // Test 1: Check if Bootstrap CSS is loaded
    const bootstrapCSS = document.querySelector('link[href*="bootstrap"]');
    testResults.push({
        name: 'Bootstrap CSS',
        status: bootstrapCSS ? 'PASS' : 'FAIL',
        message: bootstrapCSS ? 'Bootstrap CSS loaded successfully' : 'Bootstrap CSS not found'
    });
    
    // Test 2: Check if Bootstrap JS is loaded
    const bootstrapJS = typeof bootstrap !== 'undefined';
    testResults.push({
        name: 'Bootstrap JS',
        status: bootstrapJS ? 'PASS' : 'FAIL',
        message: bootstrapJS ? 'Bootstrap JS loaded successfully' : 'Bootstrap JS not found'
    });
    
    // Test 3: Check if jQuery is loaded (for compatibility)
    const jQuery = typeof $ !== 'undefined';
    testResults.push({
        name: 'jQuery',
        status: jQuery ? 'PASS' : 'FAIL',
        message: jQuery ? 'jQuery loaded successfully' : 'jQuery not found'
    });
    
    // Test 4: Check if Feather icons are loaded
    const feather = typeof feather !== 'undefined';
    testResults.push({
        name: 'Feather Icons',
        status: feather ? 'PASS' : 'FAIL',
        message: feather ? 'Feather icons loaded successfully' : 'Feather icons not found'
    });
    
    // Display results
    let html = '<div class="row">';
    testResults.forEach(test => {
        const badgeClass = test.status === 'PASS' ? 'bg-success' : 'bg-danger';
        html += `
            <div class="col-md-6 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span>${test.name}</span>
                    <span class="badge ${badgeClass}">${test.status}</span>
                </div>
                <small class="text-muted">${test.message}</small>
            </div>
        `;
    });
    html += '</div>';
    
    results.innerHTML = html;
    
    console.log('Bootstrap Test Results:', testResults);
});
</script>
@endpush

