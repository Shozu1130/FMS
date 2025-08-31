@extends('layouts.attendance')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-clock"></i> Today's Attendance
                </h1>
                <div class="text-muted">
                    <i class="bi bi-calendar"></i> {{ now()->format('l, F j, Y') }}
                    <span class="mx-2">|</span>
                    <i class="bi bi-clock"></i> <span id="current-time"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Today's Attendance Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="border-end">
@if($todayAttendance && $todayAttendance->time_in !== null)
    <h4 class="text-primary mb-1">{{ $todayAttendance->formatted_time_in }}</h4>
@else
    <h4 class="text-primary mb-1">Not logged in</h4>
@endif
                                <p class="text-muted mb-0">Time In</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border-end">
                                <h4 class="text-success mb-1">{{ $todayAttendance && $todayAttendance->time_out ? $todayAttendance->formatted_time_out : 'Not logged out' }}</h4>
                                <p class="text-muted mb-0">Time Out</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <h4 class="text-info mb-1">{{ $todayAttendance ? $todayAttendance->formatted_total_hours : '0:00' }}</h4>
                            <p class="text-muted mb-0">Total Hours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-md-6">
            @if(!$todayAttendance || !$todayAttendance->time_in)
                <div class="card shadow border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">
                            <i class="bi bi-box-arrow-in-right"></i> Time In
                        </h5>
                        <p class="card-text">Record your arrival time with photo verification</p>
                        <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#timeInModal">
                            <i class="bi bi-camera"></i> Time In Now
                        </button>
                    </div>
                </div>
            @else
                <div class="card shadow border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">
                            <i class="bi bi-check-circle"></i> Time In Recorded
                        </h5>
                        <p class="card-text">You logged in at {{ $todayAttendance ? $todayAttendance->formatted_time_in : '' }}</p>
                        <div class="text-muted">
                            <small>Photo: {{ $todayAttendance && $todayAttendance->time_in_photo ? 'Captured' : 'Not captured' }}</small>
                        </div>
                        
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-6">
            @if($todayAttendance && $todayAttendance->time_in && !$todayAttendance->time_out)
                <div class="card shadow border-warning">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">
                            <i class="bi bi-box-arrow-left"></i> Time Out
                        </h5>
                        <p class="card-text">Record your departure time with photo verification</p>
                        <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#timeOutModal">
                            <i class="bi bi-camera"></i> Time Out Now
                        </button>
                        
                    </div>
                </div>
            @elseif($todayAttendance && $todayAttendance->time_out)
                <div class="card shadow border-warning">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">
                            <i class="bi bi-check-circle"></i> Time Out Recorded
                        </h5>
                        <p class="card-text">You logged out at {{ $todayAttendance ? $todayAttendance->formatted_time_out : '' }}</p>
                        <div class="text-muted">
                            <small>Photo: {{ $todayAttendance && $todayAttendance->time_out_photo ? 'Captured' : 'Not captured' }}</small>
                        </div>
                        
                    </div>
                </div>
            @else
                <div class="card shadow border-secondary">
                    <div class="card-body text-center">
                        <h5 class="card-title text-secondary">
                            <i class="bi bi-clock"></i> Time Out
                        </h5>
                        <p class="card-text">Complete your time in first to record time out</p>
                        <button type="button" class="btn btn-secondary btn-lg" disabled>
                            <i class="bi bi-clock"></i> Time Out
                        </button>
                        
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Attendance History -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-week"></i> Recent Attendance History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAttendance as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('M j, Y') }}</td>
                                    <td>
                                        @if($attendance->time_in)
                                            <span class="text-success">{{ $attendance->formatted_time_in }}</span>
                                            @if($attendance->is_late)
                                                <span class="badge bg-warning ms-1">Late</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Not logged in</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->time_out)
                                            <span class="text-info">{{ $attendance->formatted_time_out }}</span>
                                            @if($attendance->is_early_departure)
                                                <span class="badge bg-info ms-1">Early</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Not logged out</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->formatted_total_hours }}</td>
                                    <td>{!! $attendance->status_badge !!}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attendanceDetailModal" data-attendance="{{ $attendance->id }}">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="bi bi-inbox"></i> No attendance records found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Time In Modal -->
<div class="modal fade" id="timeInModal" tabindex="-1" aria-labelledby="timeInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="timeInModalLabel">
                    <i class="bi bi-box-arrow-in-right"></i> Time In
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('attendance.time-in') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="time_in_photo" class="form-label">Photo Verification <span class="text-danger">*</span></label>
                                <div class="camera-container">
                                    <video id="camera" autoplay playsinline style="width: 100%; height: 300px; background: #000;"></video>
                                    <canvas id="canvas" style="display: none;"></canvas>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-primary" id="capture-btn">
                                            <i class="bi bi-camera"></i> Capture Photo
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="retake-btn" style="display: none;">
                                            <i class="bi bi-arrow-clockwise"></i> Retake
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="time_in_photo_data" id="time_in_photo_data">
                                <input type="hidden" name="time_in_location" id="time_in_location">
                                <div class="form-text">Please ensure your face is clearly visible in the photo.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Current Information</label>
                                <div class="card">
                                    <div class="card-body">
                                        <p><strong>Date:</strong> {{ now()->format('l, F j, Y') }}</p>
                                        <p><strong>Time:</strong> <span id="current-time-display"></span></p>
                                        <p><strong>Location:</strong> <span id="location-display">Detecting...</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="submit-time-in" disabled>
                        <i class="bi bi-check-circle"></i> Confirm Time In
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Time Out Modal -->
<div class="modal fade" id="timeOutModal" tabindex="-1" aria-labelledby="timeOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="timeOutModalLabel">
                    <i class="bi bi-box-arrow-left"></i> Time Out
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('attendance.time-out') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="time_out_photo" class="form-label">Photo Verification <span class="text-danger">*</span></label>
                                <div class="camera-container">
                                    <video id="camera-out" autoplay playsinline style="width: 100%; height: 300px; background: #000;"></video>
                                    <canvas id="canvas-out" style="display: none;"></canvas>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-warning" id="capture-btn-out">
                                            <i class="bi bi-camera"></i> Capture Photo
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="retake-btn-out" style="display: none;">
                                            <i class="bi bi-arrow-clockwise"></i> Retake
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="time_out_photo_data" id="time_out_photo_data">
                                <input type="hidden" name="time_out_location" id="time_out_location">
                                <div class="form-text">Please ensure your face is clearly visible in the photo.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Current Information</label>
                                <div class="card">
                                    <div class="card-body">
                                        <p><strong>Date:</strong> {{ now()->format('l, F j, Y') }}</p>
                                        <p><strong>Time:</strong> <span id="current-time-display-out"></span></p>
                                        <p><strong>Location:</strong> <span id="location-display-out">Detecting...</span></p>
                                        <p><strong>Time In:</strong> {{ $todayAttendance ? $todayAttendance->formatted_time_in : 'Not logged in' }}</p>
                                        <p><strong>Hours Worked:</strong> <span id="hours-worked">Calculating...</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes_out" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="notes_out" name="notes" rows="3" placeholder="Any additional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="submit-time-out" disabled>
                        <i class="bi bi-check-circle"></i> Confirm Time Out
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Attendance Detail Modal -->
<div class="modal fade" id="attendanceDetailModal" tabindex="-1" aria-labelledby="attendanceDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceDetailModalLabel">
                    <i class="bi bi-calendar-day"></i> Attendance Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="attendance-detail-content">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    document.getElementById('current-time').textContent = timeString;
    document.getElementById('current-time-display').textContent = timeString;
    document.getElementById('current-time-display-out').textContent = timeString;
}

setInterval(updateTime, 1000);
updateTime();

// Get location
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        const location = `${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
        document.getElementById('location-display').textContent = location;
        document.getElementById('location-display-out').textContent = location;
        document.getElementById('time_in_location').value = location;
        document.getElementById('time_out_location').value = location;
    }, function(error) {
        document.getElementById('location-display').textContent = 'Location unavailable';
        document.getElementById('location-display-out').textContent = 'Location unavailable';
    });
}

// Camera functionality for Time In
let stream = null;
let capturedImage = null;

document.getElementById('capture-btn').addEventListener('click', function() {
    const video = document.getElementById('camera');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);
    
    capturedImage = canvas.toDataURL('image/jpeg');
    document.getElementById('time_in_photo_data').value = capturedImage;
    
    // Show retake button and enable submit
    document.getElementById('retake-btn').style.display = 'inline-block';
    document.getElementById('capture-btn').style.display = 'none';
    document.getElementById('submit-time-in').disabled = false;
    
    // Stop camera
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
});

document.getElementById('retake-btn').addEventListener('click', function() {
    startCamera();
    document.getElementById('retake-btn').style.display = 'none';
    document.getElementById('capture-btn').style.display = 'inline-block';
    document.getElementById('submit-time-in').disabled = true;
    capturedImage = null;
    document.getElementById('time_in_photo_data').value = '';
});

// Camera functionality for Time Out
let streamOut = null;
let capturedImageOut = null;

document.getElementById('capture-btn-out').addEventListener('click', function() {
    const video = document.getElementById('camera-out');
    const canvas = document.getElementById('canvas-out');
    const context = canvas.getContext('2d');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);
    
    capturedImageOut = canvas.toDataURL('image/jpeg');
    document.getElementById('time_out_photo_data').value = capturedImageOut;
    
    // Show retake button and enable submit
    document.getElementById('retake-btn-out').style.display = 'inline-block';
    document.getElementById('capture-btn-out').style.display = 'none';
    document.getElementById('submit-time-out').disabled = false;
    
    // Stop camera
    if (streamOut) {
        streamOut.getTracks().forEach(track => track.stop());
    }
});

document.getElementById('retake-btn-out').addEventListener('click', function() {
    startCameraOut();
    document.getElementById('retake-btn-out').style.display = 'none';
    document.getElementById('capture-btn-out').style.display = 'inline-block';
    document.getElementById('submit-time-out').disabled = true;
    capturedImageOut = null;
    document.getElementById('time_out_photo_data').value = '';
});

// Ensure camera starts when modal is shown
document.getElementById('timeOutModal').addEventListener('shown.bs.modal', function() {
    startCameraOut();
    // Calculate hours worked
    const timeIn = new Date('{{ $todayAttendance && $todayAttendance->time_in ? $todayAttendance->time_in : "" }}');
    const now = new Date();
    if (timeIn && !isNaN(timeIn.getTime())) {
        const hours = (now - timeIn) / (1000 * 60 * 60);
        document.getElementById('hours-worked').textContent = hours.toFixed(2) + ' hours';
    } else {
        document.getElementById('hours-worked').textContent = '0.00 hours';
    }
});

// Add form submit validation to ensure photo is captured
document.querySelector('#timeOutModal form').addEventListener('submit', function(event) {
    if (!document.getElementById('time_out_photo_data').value) {
        event.preventDefault();
        alert('Please capture a photo before submitting Time Out.');
    }
});

// Start camera functions
function startCamera() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(mediaStream) {
            stream = mediaStream;
            document.getElementById('camera').srcObject = mediaStream;
        })
        .catch(function(error) {
            console.error('Camera access denied:', error);
            alert('Camera access is required for attendance verification.');
        });
}

function startCameraOut() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(mediaStream) {
            streamOut = mediaStream;
            document.getElementById('camera-out').srcObject = mediaStream;
        })
        .catch(function(error) {
            console.error('Camera access denied:', error);
            alert('Camera access is required for attendance verification.');
        });
}

// Start cameras when modals are shown
document.getElementById('timeInModal').addEventListener('shown.bs.modal', function() {
    startCamera();
});

document.getElementById('timeOutModal').addEventListener('shown.bs.modal', function() {
    startCameraOut();
    // Calculate hours worked
    const timeIn = new Date('{{ $todayAttendance && $todayAttendance->time_in ? $todayAttendance->time_in : "" }}');
    const now = new Date();
    if (timeIn && !isNaN(timeIn.getTime())) {
        const hours = (now - timeIn) / (1000 * 60 * 60);
        document.getElementById('hours-worked').textContent = hours.toFixed(2) + ' hours';
    } else {
        document.getElementById('hours-worked').textContent = '0.00 hours';
    }
});

// Load attendance details
document.getElementById('attendanceDetailModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const attendanceId = button.getAttribute('data-attendance');
    
    // Load attendance details via AJAX
    fetch(`/attendance/${attendanceId}/details`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('attendance-detail-content').innerHTML = html;
        });
});


</script>
@endpush
