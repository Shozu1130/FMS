# Faculty Attendance Monitoring System

## Overview
The Faculty Attendance Monitoring System is a comprehensive solution that allows professors to log their daily attendance with photo verification, while providing administrators with tools to monitor, manage, and analyze attendance data for salary computation.

## Features

### For Professors (Faculty)
- **Dedicated Login Page**: Separate attendance monitoring login at `/attendance/login`
- **Photo Verification**: Camera-based time in/out with photo capture
- **Real-time Dashboard**: View current day's status and recent attendance history
- **Location Tracking**: GPS coordinates for attendance verification
- **Time Management**: Automatic calculation of total hours worked
- **Status Tracking**: Present, late, early departure, half-day, absent statuses

### For Administrators
- **Comprehensive Monitoring**: View all faculty attendance records
- **Filtering & Search**: By date range, faculty, status, etc.
- **Manual Management**: Add, edit, and delete attendance records
- **Export Functionality**: CSV export for reporting
- **Faculty Summary**: Monthly attendance statistics and performance insights
- **Salary Integration**: Attendance data connected to salary grade calculations

## System Architecture

### Models
- **Attendance**: Core attendance model with photo storage and location tracking
- **Faculty**: Enhanced with attendance relationships and monthly statistics
- **SalaryGrade**: Extended with attendance-based salary calculations

### Controllers
- **AttendanceController**: Handles professor attendance operations
- **AttendanceAuthController**: Manages attendance system authentication
- **Admin\AttendanceController**: Admin-side attendance management

### Views
- **attendance/login.blade.php**: Professor attendance login page
- **attendance/dashboard.blade.php**: Professor attendance dashboard
- **admin/attendance/index.blade.php**: Admin attendance overview
- **admin/attendance/create.blade.php**: Create attendance records
- **admin/attendance/show.blade.php**: View attendance details
- **admin/attendance/edit.blade.php**: Edit attendance records
- **admin/attendance/faculty-summary.blade.php**: Faculty performance summary

## Database Schema

### Attendances Table
```sql
CREATE TABLE attendances (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    faculty_id BIGINT NOT NULL,
    date DATE NOT NULL,
    time_in DATETIME NULL,
    time_out DATETIME NULL,
    time_in_photo VARCHAR(255) NULL,
    time_out_photo VARCHAR(255) NULL,
    time_in_location VARCHAR(255) NULL,
    time_out_location VARCHAR(255) NULL,
    total_hours DECIMAL(5,2) DEFAULT 0.00,
    status ENUM('present', 'absent', 'late', 'early_departure', 'half_day') DEFAULT 'present',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_faculty_date (faculty_id, date),
    INDEX idx_faculty_date (faculty_id, date),
    INDEX idx_date (date),
    INDEX idx_status (status),
    
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
);
```

## Routes

### Public Routes
- `GET /attendance/login` - Attendance login page
- `POST /attendance/login` - Process attendance login

### Protected Routes (Faculty)
- `GET /attendance/dashboard` - Attendance dashboard
- `POST /attendance/time-in` - Record time in
- `POST /attendance/time-out` - Record time out
- `GET /attendance/{id}/details` - View attendance details
- `GET /attendance/monthly-stats` - Get monthly statistics

### Admin Routes
- `GET /admin/attendance` - Attendance management index
- `GET /admin/attendance/create` - Create attendance record
- `POST /admin/attendance` - Store attendance record
- `GET /admin/attendance/{id}` - Show attendance details
- `GET /admin/attendance/{id}/edit` - Edit attendance record
- `PUT /admin/attendance/{id}` - Update attendance record
- `DELETE /admin/attendance/{id}` - Delete attendance record
- `GET /admin/attendance/faculty-summary` - Faculty summary view
- `GET /admin/attendance/export` - Export attendance data

## Photo Storage

### Storage Structure
```
storage/app/public/attendance_photos/
├── {faculty_id}/
│   ├── {date}/
│   │   ├── attendance_time_in_{faculty_id}_{date}_{timestamp}.jpg
│   │   └── attendance_time_out_{faculty_id}_{date}_{timestamp}.jpg
```

### Photo Processing
- Photos are captured via webcam using HTML5 MediaDevices API
- Converted to base64 and sent to server
- Stored as JPEG files with timestamped filenames
- Organized by faculty ID and date for easy retrieval

## Salary Integration

### Attendance-Based Calculations
The system automatically calculates salary deductions based on attendance:

- **Late Arrival**: 10% deduction per late day
- **Early Departure**: 10% deduction per early departure
- **Half Day**: 50% deduction per half day
- **Absent**: 100% deduction (no salary for that day)

### Salary Calculation Method
```php
$finalSalary = $baseSalary * (1 - $totalDeduction);
```

## Usage Instructions

### For Professors

1. **Access Attendance System**
   - Navigate to `/attendance/login`
   - Use faculty credentials to log in

2. **Time In Process**
   - Click "Time In Now" button
   - Allow camera access when prompted
   - Position face clearly in camera view
   - Click "Capture Photo" to take picture
   - Add optional notes
   - Click "Confirm Time In"

3. **Time Out Process**
   - Click "Time Out Now" button
   - Follow same photo capture process
   - System calculates total hours worked
   - Click "Confirm Time Out"

4. **View History**
   - Dashboard shows today's status
   - Recent attendance history (last 30 days)
   - Click "View" to see detailed records

### For Administrators

1. **Access Attendance Management**
   - Navigate to Admin Panel → Attendance Monitoring
   - View all faculty attendance records

2. **Filter Records**
   - Use date range filters
   - Filter by specific faculty
   - Filter by attendance status

3. **Manage Records**
   - Add new attendance records manually
   - Edit existing records
   - Delete incorrect records
   - Export data to CSV

4. **Monitor Performance**
   - View faculty summary for current month
   - Identify attendance issues
   - Track top performers

## Security Features

- **Photo Verification**: Prevents attendance fraud
- **Location Tracking**: Ensures attendance from authorized locations
- **Session Management**: Secure authentication and logout
- **Input Validation**: Server-side validation of all inputs
- **Access Control**: Role-based access to different features

## Technical Requirements

### Server Requirements
- PHP 8.0+
- Laravel 10+
- MySQL 8.0+ or PostgreSQL 12+
- Web server with HTTPS support (for camera access)

### Client Requirements
- Modern web browser with camera support
- JavaScript enabled
- Camera permissions granted
- GPS location access (optional)

### Dependencies
- Bootstrap 5.1+
- Bootstrap Icons 1.8+
- HTML5 MediaDevices API
- Geolocation API

## Installation & Setup

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Create Storage Link** (if not exists)
   ```bash
   php artisan storage:link
   ```

3. **Set Permissions**
   ```bash
   chmod -R 775 storage/app/public/attendance_photos
   ```

4. **Configure Environment**
   - Ensure `FILESYSTEM_DISK=public` in `.env`
   - Set appropriate file upload limits in PHP configuration

## Troubleshooting

### Common Issues

1. **Camera Not Working**
   - Ensure HTTPS is enabled
   - Check browser permissions
   - Verify camera is not in use by other applications

2. **Photos Not Saving**
   - Check storage permissions
   - Verify storage link exists
   - Check disk space availability

3. **Location Not Detecting**
   - Ensure HTTPS is enabled
   - Check browser location permissions
   - Verify GPS is enabled on device

### Performance Optimization

1. **Photo Storage**
   - Implement photo compression
   - Set up automated cleanup of old photos
   - Use CDN for photo delivery

2. **Database Optimization**
   - Add appropriate indexes
   - Implement data archiving for old records
   - Use database partitioning for large datasets

## Future Enhancements

- **Mobile App**: Native mobile applications for iOS/Android
- **Biometric Integration**: Fingerprint or facial recognition
- **Real-time Notifications**: Push notifications for attendance reminders
- **Advanced Analytics**: Machine learning for attendance pattern analysis
- **Integration**: Connect with other HR systems and payroll software

## Support & Maintenance

- Regular database backups
- Monitor storage usage for photos
- Update security patches regularly
- Performance monitoring and optimization
- User training and documentation updates

---

**Note**: This system is designed to be compliant with labor laws and institutional policies. Always ensure proper legal review before implementation in production environments.
