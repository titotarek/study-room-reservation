# Study Room Reservation System

Course: Web Development 1  
Term: 2.2  

---

# Project Overview

This project is a **Dockerized Study Room Reservation System** designed for a university environment.

Students can:

- View available study rooms
- Check available time slots
- Create reservations
- Edit or cancel their own reservations
- View their reservation history

Administrators can:

- Manage rooms (Create / Update / Delete)
- Manage room time slots
- View all reservations
- Delete reservations when necessary

The system demonstrates the implementation of:

- MVC architecture
- REST-style API endpoints
- Authentication and authorization
- JavaScript-based dynamic UI updates
- Secure database interaction
- Layered application architecture

---

# How to Run the Project

Make sure **Docker** and **Docker Compose** are installed.

From the project root directory run:

```bash
docker-compose up
```

Open the application in your browser:

```
http://localhost:8080
```

phpMyAdmin is available at:

```
http://localhost:8081
```

---

# Login Credentials

### Admin

Email:

```
admin@example.com
```

Password:

```
admin123
```

### Student

Email:

```
student@example.com
```

Password:

```
123456
```

---

# Database

A database export is included in the root directory:

```
database.sql
```

It can be imported using **phpMyAdmin** if needed.

The database includes the following main tables:

- users
- rooms
- reservations
- timeslots

These tables are relational and enforce logical relationships between entities.

---

# Application Architecture

The project follows a **layered MVC architecture**.

```
Controller → Service → Repository → Database
```

Directory structure:

```
src/
 ├── Controllers
 ├── Services
 ├── Repositories
 ├── Interfaces
 ├── ViewModels
 ├── Views
 └── Config
```

This structure separates responsibilities between layers and keeps the application maintainable and scalable.

---

## Controllers

Controllers handle HTTP requests and responses.

Examples:

```
src/Controllers/AuthController.php
src/Controllers/RoomController.php
src/Controllers/ReservationController.php
src/Controllers/AdminController.php
src/Controllers/ApiController.php
```

Controllers receive requests, call services, and return views or JSON responses.

---

## Services

Services contain business logic and coordinate application behaviour.

Examples:

```
src/Services/AuthService.php
src/Services/RoomService.php
src/Services/ReservationService.php
src/Services/TimeSlotService.php
```

Services act as the application logic layer between controllers and repositories.

---

## Repositories

Repositories handle **database access using PDO**.

Examples:

```
src/Repositories/UserRepository.php
src/Repositories/RoomRepository.php
src/Repositories/ReservationRepository.php
src/Repositories/TimeSlotRepository.php
```

Repositories isolate database logic from controllers and services.

---

## Interfaces

Repository interfaces define contracts between services and repositories.

Examples:

```
src/Repositories/IUserRepository.php
src/Repositories/IRoomRepository.php
src/Repositories/IReservationRepository.php
src/Repositories/ITimeSlotRepository.php
```

Using interfaces ensures loose coupling and improves maintainability.

---

## ViewModels

ViewModels prepare data for presentation and prevent business logic inside views.

Examples:

```
src/ViewModels/RoomViewModel.php
src/ViewModels/ReservationViewModel.php
src/ViewModels/TimeSlotViewModel.php
```

They transform data into a format suitable for the UI.

---

## Views

Views are responsible only for rendering the user interface.

Examples:

```
src/Views/login.php
src/Views/rooms_list.php
src/Views/reservation_form.php
src/Views/admin/
```

Views use **Tailwind CSS** for responsive styling and layout.

---

# Routing

Routing is implemented using **FastRoute**.

Routing configuration is located in:

```
public/index.php
```

All routes are defined centrally and mapped to controller methods.

Example request flow:

```
HTTP Request
     ↓
FastRoute Router
     ↓
Controller
     ↓
Service
     ↓
Repository
     ↓
Database
```

---

# Security Measures

## Authentication

User authentication is handled using sessions.

Relevant files:

```
src/Controllers/AuthController.php
src/Services/AuthService.php
```

Features implemented:

- Session-based login
- Session regeneration

```
session_regenerate_id(true)
```

This prevents session fixation attacks.

---

## Authorization

Admin-only routes are protected.

Example file:

```
src/Controllers/AdminController.php
```

Access control checks the user role:

```
role === 'admin'
```

Unauthorized users are redirected.

---

## Password Security

Passwords are stored in a single `password` column and are hashed before verification.

Relevant files:

```
database.sql
src/Services/AuthService.php
src/Repositories/UserRepository.php
```

The authentication flow uses:

```
password_verify()
```

Seeded student and admin accounts in the SQL dump use hashed passwords.

---

## SQL Injection Protection

All database queries use **PDO prepared statements**.

Example pattern:

```
$stmt = $pdo->prepare($query);
$stmt->execute($params);
```

Prepared statements prevent SQL injection attacks.

---

## XSS Protection

User-generated output is escaped using:

```
htmlspecialchars()
```

This prevents malicious JavaScript execution in the browser.

---

## Server-Side Validation

User input is validated before saving to the database.

Examples:

- Required fields
- Correct data types
- Valid reservation times
- Reservation ownership verification

Users cannot modify or delete reservations belonging to other users.

---

# API Implementation

The application exposes API endpoints that return **JSON data**.

Example controller:

```
src/Controllers/ApiController.php
```

Current public endpoint:

```
/api/available-slots?room_id=4&date=2026-03-21
```

Admin AJAX endpoint:

```
/admin/slots/by-room?room_id=4
```

Example JSON response:

```json
{
  "success": true,
  "slots": [
    {
      "id": 142,
      "start_time": "09:00:00",
      "end_time": "12:00:00",
      "display_time": "09:00 - 12:00"
    }
  ]
}
```

These endpoints are used by the frontend JavaScript to dynamically update slot availability and slot management views.

---

# JavaScript Functionality

JavaScript improves the user experience through dynamic interactions.

Examples:

- Dynamic loading of time slots
- Modal dialogs
- Asynchronous API requests
- Updating UI without page refresh

JavaScript communicates with the API using:

```
fetch()
```

All JavaScript files are located in:

```
public/assets/js/
```

Most JavaScript behavior is kept in separate files and is used for API calls, modal dialogs, and dynamic slot updates.

---

# GDPR Compliance

The application follows **basic GDPR-oriented principles** for this coursework project.

Measures implemented:

- Only necessary personal data is stored (name and email)
- No sensitive personal data is collected
- Users can manage their own reservations
- The public API endpoint returns room slot availability data
- Sessions protect authenticated access

---

# WCAG Accessibility Considerations

The application considers **basic WCAG accessibility guidelines**.

Accessibility features include:

- Semantic HTML structure
- Proper `<label>` elements for form inputs
- Accessible form controls
- Responsive design for different devices
- Keyboard-accessible form controls
- Clear button states
- Dialog semantics on confirmation and management modals using `role="dialog"` and `aria-modal="true"`
- Alert messaging for validation and destructive confirmation states
- `Escape` key support for closing modals
- Focus return after closing important modals

The interface adapts to:

- Mobile devices
- Tablets
- Laptops
- Desktop screens

Implementation examples:

```
src/Views/reservation_form.php
src/Views/my_reservations.php
src/Views/admin/rooms.php
public/assets/js/my-reservations.js
public/assets/js/admin-rooms.js
public/assets/js/admin-rooms-tabs.js
```

Note:

- Manual accessibility testing was performed for keyboard navigation and modal interaction
- More advanced automated WCAG auditing could still be added

---

# Project Structure

Key directories:

```
src/
- Controllers → handle HTTP requests
- Services → contain business logic
- Repositories → handle database communication
- Interfaces → define repository contracts
- ViewModels → prepare data for views
- Views → render the user interface

public/
- index.php → application entry point
- assets/js → JavaScript files
- assets/css → styling
```

This structure ensures a clear separation between business logic, data access, and presentation.

---

# Additional Technical Notes

The project uses **Docker** to provide a consistent development environment.

Docker services include:

- PHP
- MySQL
- phpMyAdmin

The layered MVC architecture ensures:

- Maintainability
- Scalability
- Separation of concerns

---

# Manual Testing

Manual browser testing was performed on the main flows of the application.

Tested flows:

- Login as student
- Create reservation
- Edit reservation
- Cancel reservation
- Login as admin
- Create room
- Delete room with confirmation dialog
- Create, update, and delete time slots
- View and delete reservations from the admin panel

This confirms the main functional paths of the project are working from the user interface.

---

# Known Limitations

Some improvements could be made in future versions:

- Additional unit testing could be added
- Role-based middleware could further improve authorization
- More advanced accessibility testing and focus trapping could be implemented
- Additional API endpoints could expose more reservation data

These improvements would further increase the robustness and scalability of the system.
