<div class="row">
    <div class="col-md-6">
        <h6 class="font-weight-bold">Basic Information</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Date:</strong></td>
                <td>{{ $attendance->date->format('l, F j, Y') }}</td>
            </tr>
            <tr>
                <td><strong>Faculty:</strong></td>
                <td>{{ $attendance->faculty->name }} ({{ $attendance->faculty->professor_id }})</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>{!! $attendance->status_badge !!}</td>
            </tr>
            <tr>
                <td><strong>Total Hours:</strong></td>
                <td>{{ $attendance->formatted_total_hours }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="font-weight-bold">Time Details</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Time In:</strong></td>
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
            </tr>
            <tr>
                <td><strong>Time Out:</strong></td>
                <td>
                    @if($attendance->time_out)
                        <span class="text-info">{{ $attendance->formatted_time_out }}</span>
                        @if($attendance->is_early_departure)
                            <span class="badge bg-info ms-1">Early Departure</span>
                        @endif
                    @else
                        <span class="text-muted">Not logged out</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Location:</strong></td>
                <td>
                    @if($attendance->time_in_location)
                        <small class="text-muted">In: {{ $attendance->time_in_location }}</small><br>
                    @endif
                    @if($attendance->time_out_location)
                        <small class="text-muted">Out: {{ $attendance->time_out_location }}</small>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

@if($attendance->notes)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="font-weight-bold">Notes</h6>
        <div class="card">
            <div class="card-body">
                <p class="mb-0">{{ $attendance->notes }}</p>
            </div>
        </div>
    </div>
</div>
@endif

@if($attendance->time_in_photo || $attendance->time_out_photo)
<div class="row mt-3">
    <div class="col-md-6">
        @if($attendance->time_in_photo)
        <h6 class="font-weight-bold">Time In Photo</h6>
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $attendance->time_in_photo) }}" 
                     alt="Time In Photo" 
                     class="img-fluid rounded" 
                     style="max-height: 200px;">
                <p class="text-muted mt-2 mb-0">Time In: {{ $attendance->formatted_time_in }}</p>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-6">
        @if($attendance->time_out_photo)
        <h6 class="font-weight-bold">Time Out Photo</h6>
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $attendance->time_out_photo)" 
                     alt="Time Out Photo" 
                     class="img-fluid rounded" 
                     style="max-height: 200px;">
                <p class="text-muted mt-2 mb-0">Time Out: {{ $attendance->formatted_time_out }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

<div class="row mt-3">
    <div class="col-12">
        <h6 class="font-weight-bold">Record Information</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Created:</strong></td>
                <td>{{ $attendance->created_at->format('M j, Y g:i A') }}</td>
            </tr>
            <tr>
                <td><strong>Last Updated:</strong></td>
                <td>{{ $attendance->updated_at->format('M j, Y g:i A') }}</td>
            </tr>
        </table>
    </div>
</div>
