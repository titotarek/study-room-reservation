# Study Room Reservation System

Course: Web Development 1  
Term: 2.2  

## Project Overview

This project is a Dockerized Study Room Reservation System for a university campus.

Students can:
- View available study rooms
- Create reservations
- Edit or cancel their own reservations
- View their reservation history

Administrators can:
- Manage rooms (CRUD)
- View all reservations
- Delete reservations if necessary

The project demonstrates the use of MVC architecture, routing, authentication, API communication, and secure data handling.

## How to Run the Project

Make sure Docker and Docker Compose are installed.

In the project root directory, run:

```bash
docker-compose up

Open the application at:

http://localhost:8080

phpMyAdmin is available at:

http://localhost:8081

Login Credentials
Admin

Email: admin@example.com

Password: admin123

Student

Email: student@example.com

Password: password123

Database

A database export is included in the root directory as:

database.sql

It can be imported using phpMyAdmin if needed.

Architecture & Design

The application follows a layered MVC architecture:

Controllers handle HTTP requests and responses

Services contain business logic

Repositories handle database access using PDO

ViewModels map data to views

Views are responsible for presentation

Routing

Routing is implemented using FastRoute in:

public/index.php

All routes are defined centrally and mapped to controller methods.

Dependency Structure

Controllers depend on Services

Services depend on Repository interfaces

Repositories handle PDO database communication

This ensures a clear separation of concerns and reduces coupling.

Security Measures
Authentication & Authorization

Sessions are used to store authenticated users

session_regenerate_id() prevents session fixation

Admin routes require role === 'admin'

Unauthorized users are redirected

Password Security

Passwords are hashed using password_hash()

Passwords are verified using password_verify()

Plain-text passwords are never stored

SQL Injection Protection

All database queries use PDO prepared statements

Parameters are safely bound

XSS Protection

Output is escaped using htmlspecialchars() in views

Server-Side Validation

Required fields are validated before saving

Reservation logic validates time slots

Users cannot edit or delete reservations belonging to others

API & JavaScript

The application includes API endpoints that return JSON data.

JavaScript is used to:

Dynamically load time slots without refreshing the page

Communicate with API endpoints using fetch()

Update UI components dynamically

All JavaScript is located in /public/assets/js/ and is not embedded directly in views.

GDPR Compliance

The application considers GDPR principles:

Passwords are securely hashed

Only necessary personal data is stored (name, email)

Users can view and delete their own reservations

No sensitive personal data is collected

No personal data is exposed via public API endpoints

WCAG Accessibility Considerations

The application includes:

Semantic HTML structure

Proper <label> elements for form inputs

Clear button states

Responsive layout for mobile, tablet, and desktop

Sufficient color contrast

Keyboard-accessible forms

Additional Notes

The application uses Docker for consistent development environments

Global error and exception handling is implemented

MVC structure ensures maintainability and scalability