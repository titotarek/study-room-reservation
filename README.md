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
src/Interfaces/IUserRepository.php
src/Interfaces/IRoomRepository.php
src/Interfaces/IReservationRepository.php
src/Interfaces/ITimeSlotRepository.php
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
src/Views/auth/
src/Views/rooms/
src/Views/reservations/
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

Passwords are securely hashed using:

```
password_hash()
```

Passwords are verified using:

```
password_verify()
```

Plain-text passwords are **never stored**.

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

Example endpoints:

```
/api/rooms
/api/timeslots
```

Example JSON response:

```json
[
  {
    "room_id": 1,
    "room_name": "Room A",
    "capacity": 6
  }
]
```

These endpoints are used by the frontend JavaScript to dynamically update the user interface.

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

JavaScript is **not embedded inside HTML views**, ensuring separation of concerns.

---

# GDPR Compliance

The application follows **basic GDPR principles**.

Measures implemented:

- Only necessary personal data is stored (name and email)
- Passwords are securely hashed
- No sensitive personal data is collected
- Users can manage their own reservations
- API endpoints do not expose personal data
- Sessions protect authenticated access

---

# WCAG Accessibility Considerations

The application considers **WCAG accessibility guidelines**.

Accessibility features include:

- Semantic HTML structure
- Proper `<label>` elements for form inputs
- Accessible form controls
- Responsive design for different devices
- Sufficient color contrast
- Keyboard-accessible forms
- Clear button states

The interface adapts to:

- Mobile devices
- Tablets
- Laptops
- Desktop screens

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

Global error and exception handling are implemented to improve application stability.

Example:

```
set_error_handler()
set_exception_handler()
```

The layered MVC architecture ensures:

- Maintainability
- Scalability
- Separation of concerns

---

# Known Limitations

Some improvements could be made in future versions:

- Additional unit testing could be added
- Role-based middleware could further improve authorization
- More advanced accessibility testing could be implemented
- Additional API endpoints could expose more reservation data

These improvements would further increase the robustness and scalability of the system.