@extends('layouts.admin')

@section('title', 'Edit Ex-Student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Edit Ex-Student</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.ex-students') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left" width="16" height="16"></i>
                        Back to Ex-Students
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Ex-Student Information</h5>
                </div>
                <div class="card-body">
                    @if(isset($errors) && $errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.ex-students.update', $exStudent) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" 
                                           value="{{ old('student_id', $exStudent->student_id) }}" required 
                                           placeholder="Enter student ID">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name', $exStudent->name) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email', $exStudent->email) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="{{ old('phone', $exStudent->phone) }}" placeholder="+60123456789">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="program_short" class="form-label">Program (Short) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="program_short" name="program_short" 
                                           value="{{ old('program_short', $exStudent->program_short ?? $exStudent->short_program_name) }}" required
                                           placeholder="e.g., Bachelor of Science">
                                    <div class="form-text">Short program name for certificate display (e.g., "Bachelor of Science")</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="program_full" class="form-label">Program (Full) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="program_full" name="program_full" 
                                           value="{{ old('program_full', $exStudent->program_full ?? $exStudent->full_program_name) }}" required
                                           placeholder="e.g., Bachelor of Science (Hons) in Information & Communication Technology">
                                    <div class="form-text">Full program name for certificate body (e.g., "Bachelor of Science (Hons) in ICT")</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cgpa" class="form-label">CGPA</label>
                                    <input type="number" class="form-control" id="cgpa" name="cgpa" 
                                           value="{{ old('cgpa', $exStudent->cgpa) }}" step="0.01" min="0" max="4" 
                                           placeholder="3.75">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="graduation_year" class="form-label">Graduation Year <span class="text-danger">*</span></label>
                                    <select class="form-select" id="graduation_year" name="graduation_year" required>
                                        <option value="">Select Year</option>
                                        @for($year = date('Y'); $year >= 2010; $year--)
                                            <option value="{{ $year }}" {{ old('graduation_year', $exStudent->graduation_year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="graduation_month" class="form-label">Graduation Month <span class="text-danger">*</span></label>
                                    <select class="form-select" id="graduation_month" name="graduation_month" required>
                                        <option value="">Select Month</option>
                                        @for($month = 1; $month <= 12; $month++)
                                            <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" 
                                                    {{ old('graduation_month', $exStudent->graduation_month) == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="graduation_day" class="form-label">Graduation Day <span class="text-danger">*</span></label>
                                    <select class="form-select" id="graduation_day" name="graduation_day" required>
                                        <option value="">Select Day</option>
                                        @for($day = 1; $day <= 31; $day++)
                                            <option value="{{ $day }}" {{ old('graduation_day', $exStudent->graduation_day ?? 1) == $day ? 'selected' : '' }}>
                                                {{ $day }} ({{ $day === 1 ? 'First' : ($day === 2 ? 'Second' : ($day === 3 ? 'Third' : ($day === 21 ? 'Twenty-first' : ($day === 22 ? 'Twenty-second' : ($day === 23 ? 'Twenty-third' : ($day === 31 ? 'Thirty-first' : $day . 'th')))))) }})
                                            </option>
                                        @endfor
                                    </select>
                                    <div class="form-text">Day of month for graduation date (will be formatted as "Tenth day of June 2011")</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Preview Graduation Date</label>
                                    <div class="form-control bg-light" id="graduation_date_preview" style="min-height: 38px; padding: 0.375rem 0.75rem; display: flex; align-items: center;">
                                        @if($exStudent->graduation_year && $exStudent->graduation_month && ($exStudent->graduation_day ?? 1))
                                            <strong>{{ $exStudent->formatted_graduation_date }}</strong>
                                        @else
                                            <span class="text-muted">Select year, month, and day to preview</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Information -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6>QR Code Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Certificate Number</label>
                                            <input type="text" class="form-control" value="{{ $exStudent->certificate_number }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Verification URL</label>
                                            <input type="text" class="form-control" value="{{ $exStudent->getVerificationUrl() }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary" onclick="generateQR({{ $exStudent->id }})">
                                                <i data-feather="qrcode" width="16" height="16"></i>
                                                Generate QR Code
                                            </button>
                                            <a href="{{ route('admin.ex-students.download-qr', $exStudent) }}" class="btn btn-outline-success">
                                                <i data-feather="download" width="16" height="16"></i>
                                                Download QR Code
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Records Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6>Academic Records</h6>
                            </div>
                            <div class="card-body">
                                <!-- Structured entry (Year/Semester/Subject rows) -->
                                <div class="mb-3">
                                    <label class="form-label">Structured Entry (Year / Semester / Subject)</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle" id="recordsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 110px;">Year</th>
                                                    <th style="width: 110px;">Semester</th>
                                                    <th style="width: 120px;">Code</th>
                                                    <th>Subject Name</th>
                                                    <th style="width: 90px;">Mark</th>
                                                    <th style="width: 100px;">Grade</th>
                                                    <th style="width: 110px;">Points</th>
                                                    <th style="width: 60px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="text" class="form-control form-control-sm" placeholder="e.g., 2008/09"></td>
                                                    <td>
                                                        <select class="form-select form-select-sm">
                                                            <option value="">-</option>
                                                            <option value="1">Year 1</option>
                                                            <option value="2">Year 2</option>
                                                            <option value="3">Year 3</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control form-control-sm" placeholder="BICT101"></td>
                                                    <td><input type="text" class="form-control form-control-sm" placeholder="Subject name"></td>
                                                    <td><input type="number" class="form-control form-control-sm" placeholder="78" step="1" min="0" max="100"></td>
                                                    <td><input type="text" class="form-control form-control-sm" placeholder="B+"></td>
                                                    <td><input type="number" class="form-control form-control-sm" placeholder="3.00" step="0.01" min="0" max="4"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary" id="addRowBtn" onclick="addRecordRow()">
                                        <i data-feather="plus" width="16" height="16"></i> Add Subject Row
                                    </button>
                                </div>
                                
                                <!-- JSON input hidden for simplicity -->
                            </div>
                        </div>

                        <!-- Certificate Data Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6>Certificate Data</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="certificate_data_json" class="form-label">Certificate Data (JSON)</label>
                                    <textarea class="form-control" id="certificate_data_json" name="certificate_data_json" 
                                              rows="4">{{ old('certificate_data_json', json_encode($exStudent->certificate_data, JSON_PRETTY_PRINT)) }}</textarea>
                                    <div class="form-text">Enter certificate data in JSON format.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save" width="16" height="16"></i>
                                    Update Ex-Student
                                </button>
                                <a href="{{ route('admin.ex-students') }}" class="btn btn-secondary">
                                    <i data-feather="x" width="16" height="16"></i>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code for Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Generating QR Code...</span>
                    </div>
                </div>
                <div id="qrInfo" class="mt-3" style="display: none;">
                    <h6>Student Information</h6>
                    <p><strong>Student ID:</strong> <span id="qrStudentId"></span></p>
                    <p><strong>Login URL:</strong> <span id="qrLoginUrl"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadQrBtn" style="display: none;">
                    <i data-feather="download" width="16" height="16"></i> Download QR Code
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentExStudentId = null;

function generateQR(exStudentId) {
    currentExStudentId = exStudentId;
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
    
    // Show loading
    document.getElementById('qrCodeContainer').innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Generating QR Code...</span>
        </div>
    `;
    document.getElementById('qrInfo').style.display = 'none';
    document.getElementById('downloadQrBtn').style.display = 'none';
    
    // Generate QR code
    fetch(`/admin/ex-students/${exStudentId}/generate-qr`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Display QR code at full resolution
            document.getElementById('qrCodeContainer').innerHTML = `
                <img src="${data.qr_code_url}" alt="QR Code" class="img-fluid" style="max-width: 500px; width: 100%; height: auto;">
                <p class="mt-2 small text-muted">Right-click to save as PNG for printing</p>
            `;
            
            // Show info
            document.getElementById('qrStudentId').textContent = data.student_id;
            document.getElementById('qrLoginUrl').textContent = data.login_url;
            document.getElementById('qrInfo').style.display = 'block';
            document.getElementById('downloadQrBtn').style.display = 'inline-block';
        } else {
            document.getElementById('qrCodeContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i data-feather="alert-circle" width="20" height="20"></i>
                    ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('qrCodeContainer').innerHTML = `
            <div class="alert alert-danger">
                <i data-feather="alert-circle" width="20" height="20"></i>
                Failed to generate QR code. Please try again.
            </div>
        `;
    });
}

// Download QR code
document.getElementById('downloadQrBtn').addEventListener('click', function() {
    if (currentExStudentId) {
        window.open(`/admin/ex-students/${currentExStudentId}/download-qr`, '_blank');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    if (window.feather && typeof feather.replace === 'function') {
        feather.replace();
    }
    
    // Ordinal day conversion
    function getOrdinalDay(day) {
        const ordinals = {
            1: 'First', 2: 'Second', 3: 'Third', 4: 'Fourth', 5: 'Fifth',
            6: 'Sixth', 7: 'Seventh', 8: 'Eighth', 9: 'Ninth', 10: 'Tenth',
            11: 'Eleventh', 12: 'Twelfth', 13: 'Thirteenth', 14: 'Fourteenth', 15: 'Fifteenth',
            16: 'Sixteenth', 17: 'Seventeenth', 18: 'Eighteenth', 19: 'Nineteenth', 20: 'Twentieth',
            21: 'Twenty-first', 22: 'Twenty-second', 23: 'Twenty-third', 24: 'Twenty-fourth', 25: 'Twenty-fifth',
            26: 'Twenty-sixth', 27: 'Twenty-seventh', 28: 'Twenty-eighth', 29: 'Twenty-ninth', 30: 'Thirtieth',
            31: 'Thirty-first'
        };
        return ordinals[day] || day;
    }
    
    // Month names
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    
    // Update graduation date preview
    function updateGraduationDatePreview() {
        const year = document.getElementById('graduation_year').value;
        const month = document.getElementById('graduation_month').value;
        const day = document.getElementById('graduation_day').value;
        const preview = document.getElementById('graduation_date_preview');
        
        if (year && month && day) {
            const monthName = monthNames[parseInt(month) - 1];
            const ordinalDay = getOrdinalDay(parseInt(day));
            preview.innerHTML = `<strong>${ordinalDay} day of ${monthName} ${year}</strong>`;
            preview.classList.remove('bg-light', 'text-muted');
            preview.classList.add('bg-info', 'text-white');
        } else {
            preview.innerHTML = '<span class="text-muted">Select year, month, and day to preview</span>';
            preview.classList.remove('bg-info', 'text-white');
            preview.classList.add('bg-light', 'text-muted');
        }
    }
    
    // Add event listeners for graduation date fields
    document.getElementById('graduation_year').addEventListener('change', updateGraduationDatePreview);
    document.getElementById('graduation_month').addEventListener('change', updateGraduationDatePreview);
    document.getElementById('graduation_day').addEventListener('change', updateGraduationDatePreview);
    
    // Initial preview update
    updateGraduationDatePreview();
    
    // Parse JSON inputs
    const form = document.querySelector('form');
    // Structured rows handling
    const tableBody = document.querySelector('#recordsTable tbody');
    const addBtn = document.getElementById('addRowBtn');
    if (addBtn) addBtn.addEventListener('click', () => addRecordRow());

    window.addRecordRow = function(row={}) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" placeholder="e.g., 2008/09" value="${row.year||''}"></td>
            <td>
                <select class="form-select form-select-sm">
                    <option value="">-</option>
                    <option value="1" ${row.semester=='1'?'selected':''}>Year 1</option>
                    <option value="2" ${row.semester=='2'?'selected':''}>Year 2</option>
                    <option value="3" ${row.semester=='3'?'selected':''}>Year 3</option>
                </select>
            </td>
            <td><input type="text" class="form-control form-control-sm" placeholder="BICT101" value="${row.code||''}"></td>
            <td><input type="text" class="form-control form-control-sm" placeholder="Subject name" value="${row.name||''}"></td>
            <td><input type="number" class="form-control form-control-sm" placeholder="78" value="${row.mark||''}" step="1" min="0" max="100"></td>
            <td><input type="text" class="form-control form-control-sm" placeholder="B+" value="${row.grade||''}"></td>
            <td><input type="number" class="form-control form-control-sm" placeholder="3.00" value="${row.points||''}" step="0.01" min="0" max="4"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button></td>`;
        tableBody.appendChild(tr);
        const removeBtn = tr.querySelector('.remove-row');
        if (removeBtn) removeBtn.addEventListener('click', ()=> tr.remove());
    }

    // Prefill existing records if available
    try {
        const existing = @json($exStudent->academic_records ?? []);
        const flat = [];
        Object.keys(existing||{}).forEach(year => {
            Object.keys(existing[year]||{}).forEach(semKey => {
                const sem = (semKey.match(/(\d+)/)||[])[1] || '1';
                (existing[year][semKey]||[]).forEach(r => flat.push({year, semester: sem, code:r.code, name:r.name, mark:r.mark, grade:r.grade, points:r.points}));
            });
        });
        if (flat.length) flat.forEach(r => addRecordRow(r)); else addRecordRow();
    } catch (e) { addRecordRow(); }

    form.addEventListener('submit', function(e) {
        // Build academic_records from structured rows
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        const grouped = {};
        rows.forEach(tr => {
            const cells = tr.querySelectorAll('input,select');
            const [yearEl, semEl, codeEl, nameEl, markEl, gradeEl, pointsEl] = cells;
            const year = (yearEl.value||'').trim();
            const sem = (semEl.value||'').trim() || '1';
            const rec = {
                code: (codeEl.value||'').trim(),
                name: (nameEl.value||'').trim(),
                mark: markEl.value ? parseFloat(markEl.value) : null,
                grade: (gradeEl.value||'').trim(),
                points: pointsEl.value ? parseFloat(pointsEl.value) : null,
            };
            if (!rec.code && !rec.name) return;
            const ykey = year || 'Year';
            if (!grouped[ykey]) grouped[ykey] = {};
            if (!grouped[ykey][`semester_${sem}`]) grouped[ykey][`semester_${sem}`] = [];
            grouped[ykey][`semester_${sem}`].push(rec);
        });
        // Always attach hidden field (even if empty)
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'academic_records';
        hidden.value = JSON.stringify(grouped);
        form.appendChild(hidden);
        
        // Parse academic records JSON
        const arJsonEl = document.getElementById('academic_records_json');
        if (arJsonEl) {
            const academicRecordsText = arJsonEl.value;
            if (academicRecordsText.trim()) {
                try {
                    const academicRecords = JSON.parse(academicRecordsText);
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'academic_records';
                    hiddenInput.value = JSON.stringify(academicRecords);
                    form.appendChild(hiddenInput);
                } catch (error) {
                    alert('Invalid JSON format in Academic Records field');
                    e.preventDefault();
                    return;
                }
            }
        }
        
        // Parse certificate data JSON
        const certJsonEl = document.getElementById('certificate_data_json');
        if (certJsonEl) {
            const certificateDataText = certJsonEl.value;
            if (certificateDataText.trim()) {
                try {
                    const certificateData = JSON.parse(certificateDataText);
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'certificate_data';
                    hiddenInput.value = JSON.stringify(certificateData);
                    form.appendChild(hiddenInput);
                } catch (error) {
                    alert('Invalid JSON format in Certificate Data field');
                    e.preventDefault();
                    return;
                }
            }
        }
    });
});
</script>
@endpush
