
# Barangay Health Service and Consultation Management System

## Group Members
- Ira Zenith Ginaia Alias
- Venus Jean Favila

## Project Description

The Barangay Health Service and Consultation Management System is a web-based application designed to help barangay residents access basic healthcare services through online appointment scheduling. It allows Barangay Health Workers (BHWs) to verify residents and monitor attendance, while Healthcare Personnel manage appointment approvals and review health-related statistics. The system aims to organize healthcare requests and improve the efficiency of barangay health center operations.

## Technologies Used

- Laravel 13
- PHP
- SQLite
- Blade Templates
- HTML, CSS, and JavaScript
- Tailwind CSS
- Laravel Breeze

## User Roles and Responsibilities

### 1. Patient
- Register for an account.
- Log in after residency verification and account approval.
- View the patient dashboard.
- Schedule healthcare appointments.
- Select an available consultation date and time slot.
- View appointment status and history.
- See whether appointments are Pending, Approved, or Rejected.

### 2. Barangay Health Worker (BHW)
- Log in to the BHW dashboard.
- Review and verify patient registrations.
- Approve or reject residency verification requests.
- Monitor patient appointments.
- Confirm whether patients attended their approved appointments.
- Mark patients as Attended or No-show when eligible.
- View appointment history.
- View health service statistics based on confirmed attendance.

### 3. Healthcare Personnel
- Log in to the Healthcare Personnel dashboard.
- Review patient appointment requests.
- Approve or reject appointments.
- Provide reasons for rejected appointments.
- Monitor appointment records.
- View healthcare statistics and common consultation concerns.

## Core Features

### 1. User Registration and Authentication
The system allows users to register and log in based on their assigned roles. Patient accounts require BHW verification before accessing appointment services.

### 2. Patient Residency Verification
Barangay Health Workers can review patient registrations and approve or reject accounts.

### 3. Appointment Scheduling
Patients can request consultations by selecting a date, time slot, and consultation concern.

### 4. Automatic Appointment Capacity Limit
The system limits appointments to:
- Morning: 5 patients
- Afternoon: 5 patients
- Total: 10 patients per day (for testing only, will change based on the healthcare personnels/BHWs preference)

Fully booked time slots become unavailable. Pending and Approved appointments count toward the limit, while Rejected appointments do not.

### 5. Appointment Approval and Rejection
Healthcare Personnel can approve or reject pending appointment requests. Rejected appointments include a reason for the decision.

### 6. Attendance Monitoring
Barangay Health Workers can confirm attendance for approved appointments and mark patients as Attended or No-show.

### 7. Appointment History
The system maintains appointment records and displays relevant appointment history to authorized users.

### 8. Healthcare Analytics
BHWs and Healthcare Personnel can view statistics on consultation concerns based on approved appointments with confirmed attendance.

## Demo Accounts

Use the following test accounts to demonstrate the system.

| Role | Email | Password |
|------|-------|----------|
| Patient | patient@ugacsur.bhw| password789 |
| Barangay Health Worker | bhw@ugacsur.bhw | password123 |
| Healthcare Personnel | doctor@ugacsur.bhw | password456 |


## Installation and Setup

### Prerequisites

Install the following before running the project:

- PHP compatible with Laravel 13
- Composer
- Node.js and npm
- Git
- SQLite support for PHP

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd <repository-folder>
```

Replace `<repository-url>` with your GitHub repository URL and `<repository-folder>` with the cloned project folder.

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Create the Environment File

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

For macOS or Linux:

```bash
cp .env.example .env
```

### Step 4: Generate the Application Key

```bash
php artisan key:generate
```

### Step 5: Configure the Database

Open `.env` and configure SQLite:

```env
DB_CONNECTION=sqlite
```

Create the SQLite database file if it does not exist.

For Windows PowerShell:

```powershell
New-Item -ItemType File -Path database/database.sqlite
```

For macOS or Linux:

```bash
touch database/database.sqlite
```

If the file already exists, skip the creation command.

### Step 6: Run Database Migrations and Seeders

```bash
php artisan migrate --seed
```

This creates the database tables and runs the project's configured seeders.

Demo accounts will only be created automatically if they have been defined in the database seeders.

### Step 7: Install Frontend Dependencies

```bash
npm install
```

### Step 8: Build Frontend Assets

```bash
npm run build
```

### Step 9: Start the Application

```bash
php artisan serve
```

Open the application in your browser:

http://127.0.0.1:8000

## Basic System Workflow

1. A patient registers for an account.
2. The BHW verifies the patient's residency.
3. Once approved, the patient logs in and requests an appointment.
4. The system checks appointment capacity for the selected date and session.
5. Healthcare Personnel approve or reject the appointment.
6. The patient visits the health center on the scheduled date.
7. The BHW records the patient's attendance.
8. Confirmed attendance contributes to healthcare analytics.

## Project Status

This project is a working academic prototype developed for demonstration and evaluation purposes.

- The application should use demo data rather than real patient information during presentations.
- This prototype is intended for academic demonstration, not production clinical use.
