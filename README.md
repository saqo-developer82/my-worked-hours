# Worked Hours Management System

A Laravel 11 web application for tracking and managing worked hours on various tasks. This application allows you to record, filter, and export your work time data efficiently.

## Features

### Task Management
- **View Tasks**: Display all worked tasks in a paginated table (10 records per page), sorted by date in descending order
- **Add Tasks**: Create new task entries with:
  - Task title (required)
  - Hours (optional, default: 0)
  - Minutes (optional, default: 0, max: 59)
  - Date (optional, format: YYYY-MM-DD)
- **Bulk Insert**: Add multiple tasks at once using a textarea with multiline format
- **Edit Tasks**: Update existing task records
- **Delete Tasks**: Remove task records with confirmation

### Filtering & Search
- **Task Filter**: Search tasks by name using case-insensitive LIKE search
- **Date Filtering**: Filter by:
  - Single date
  - Date interval (start date and end date)
  - Date interval takes precedence over single date when both are provided

### Total Hours Calculation
- **Automatic Totals**: Total worked hours are automatically calculated and displayed based on current filters
- **Real-time Updates**: Totals update automatically when filters are applied
- **Smart Conversion**: Excess minutes are automatically converted to hours (e.g., 65 minutes = 1 hour 5 minutes)

### Data Export
- **Excel Export**: Export worked hours data to Excel format (.xlsx)
- **Date Range Selection**: Choose start and end dates for export
- **Grouped Data**: Data is grouped by task with total hours and minutes per task
- **Summary Row**: Excel file includes a total row showing overall hours and minutes
- **Formatted Duration**: Duration displayed in readable format (e.g., "5h:30m", "45m", "8h")

## Requirements

- PHP >= 8.2
- MySQL Database
- Composer
- Node.js >= 18.x and npm
- Laravel 11

## Installation

1. **Clone the repository** (if applicable) or navigate to the project directory:
   ```bash
   cd /var/www/MyWorkedHours
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**:
   ```bash
   npm install
   ```

4. **Configure environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**:
   Edit `.env` file and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=my_personal_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations**:
   ```bash
   php artisan migrate
   ```

   The migration will create the `worked_hours` table with the following structure:
   - `id` (primary key, auto-increment)
   - `task` (text)
   - `hours` (integer, default: 0)
   - `minutes` (integer, default: 0)
   - `date` (date, nullable)
   - `created_at` (timestamp)
   - `updated_at` (timestamp)

7. **Build frontend assets**:
   ```bash
   npm run build
   ```
   
   For development with hot reload:
   ```bash
   npm run dev
   ```

8. **Start the development server**:
   ```bash
   php artisan serve
   ```

   The application will be available at `http://localhost:8000`

## Usage

### Adding Tasks

1. Click "Add New Task(s)" button on the main page
2. Fill in the form:
   - **Task Title**: Enter the task name (required)
   - **Hours**: Enter worked hours (optional, default: 0)
   - **Minutes**: Enter worked minutes (optional, default: 0, max: 59)
   - **Date**: Select the date (optional, format: YYYY-MM-DD)
3. For bulk insert, use the textarea with format:
   - Full format: `Task Title, Hours, Minutes, Date (YYYY-MM-DD)`
   - Simple format: `Task Title` (uses defaults: 0 hours, 0 minutes, current date)
4. Click "Submit" to save

### Filtering Data

1. Use the filter form on the main page:
   - **Task**: Enter task name to search (case-insensitive)
   - **Date (Single)**: Select a specific date
   - **Start Date / End Date**: Select a date range
2. Click "Filter" to apply filters
3. Click "Clear" to remove all filters

### Viewing Totals

Total worked hours are automatically displayed at the top of the list, showing:
- Formatted duration (e.g., "5h:30m")
- Raw values in parentheses
- Current filter context

### Editing Tasks

1. Click "Edit" button on any task row
2. Modify the task details
3. Click "Update" to save changes

### Deleting Tasks

1. Click "Delete" button on any task row
2. Confirm the deletion in the popup dialog

### Exporting Data

1. Click "Export Data" button on the main page
2. Select start and end dates (default: last 7 days)
3. Click "Export" to download the Excel file
4. The exported file will contain:
   - Headers: "TASKS/WORK" and "Duration"
   - Grouped tasks with total hours and minutes
   - Total row at the bottom

## Project Structure

```
MyWorkedHours/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── WorkedHourController.php
│   │   └── Requests/
│   │       ├── StoreWorkedHourRequest.php
│   │       ├── UpdateWorkedHourRequest.php
│   │       └── ExportWorkedHourRequest.php
│   ├── Models/
│   │   └── WorkedHour.php
│   ├── Repositories/
│   │   ├── WorkedHourRepositoryInterface.php
│   │   └── WorkedHourRepository.php
│   ├── Services/
│   │   └── WorkedHourService.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   └── migrations/
│       └── 2025_11_16_072619_create_worked_hours_table.php
├── resources/
│   ├── css/
│   │   ├── app.css
│   │   └── worked-hours.css
│   ├── js/
│   │   ├── app.js
│   │   ├── bootstrap.js
│   │   └── worked-hours.js
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       └── worked-hours/
│           ├── partials/
│           │   ├── _header.blade.php
│           │   ├── _alerts.blade.php
│           │   ├── _filters.blade.php
│           │   ├── _date-group.blade.php
│           │   ├── _task-row.blade.php
│           │   └── _empty-state.blade.php
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           └── export.blade.php
├── public/
│   └── build/
│       └── (compiled assets)
├── routes/
│   └── web.php
└── vite.config.js
```

## Architecture

The application follows a clean architecture pattern:

- **Controllers**: Handle HTTP requests and responses
- **Services**: Contain business logic
- **Repositories**: Handle database operations
- **Requests**: Validate form input
- **Models**: Represent database entities

### Frontend Architecture

The frontend is organized using a modular approach:

- **Blade Partials**: Views are broken down into reusable partial components:
  - `_header.blade.php`: Page header with title, buttons, and totals
  - `_alerts.blade.php`: Success and error alert messages
  - `_filters.blade.php`: Filter form component
  - `_date-group.blade.php`: Date group card with collapsible task list
  - `_task-row.blade.php`: Individual task row component
  - `_empty-state.blade.php`: Empty state message

- **Asset Management**: Frontend assets are managed with Vite:
  - `resources/css/worked-hours.css`: Custom styles for worked hours pages
  - `resources/js/worked-hours.js`: JavaScript for date pickers and UI interactions
  - Assets are compiled and optimized using Vite build system

## Technologies Used

- **Laravel 11**: PHP web framework
- **MySQL**: Database
- **Bootstrap 5**: Frontend CSS framework
- **Bootstrap Icons**: Icon library
- **Vite**: Frontend build tool and asset bundler
- **Flatpickr**: Date picker library
- **PhpSpreadsheet**: Excel file generation
- **Blade**: Laravel templating engine

## Routes

- `GET /` - List all worked hours
- `GET /worked-hours/create` - Show create form
- `POST /worked-hours` - Store new task(s)
- `GET /worked-hours/{id}/edit` - Show edit form
- `PUT /worked-hours/{id}` - Update task
- `DELETE /worked-hours/{id}` - Delete task
- `GET /worked-hours/export` - Show export form
- `POST /worked-hours/export` - Process export and download Excel file
