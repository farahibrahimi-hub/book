You are a senior software architect and Laravel expert.

I want you to design a small but well-structured monolithic web application using Laravel 12 with Blade templating. The project must follow clean architecture principles and include realistic business logic (not just basic CRUD).

## Project Idea:

A **Library Reservation System** where users can reserve books, but reservations are handled at the level of physical copies, not just books.

## Objectives:

* Keep the project simple in scope, but strong in design and logic.
* Focus on clean code, scalability, and maintainability.
* Avoid overengineering, but do not oversimplify.

---

## Core Features:

### 1. Authentication:

* Users can register and login.
* Use Laravel Breeze with Blade.

### 2. Roles:

* Admin:

  * Manage books and copies
  * View all reservations
* User:

  * Browse books
  * Reserve available copies
  * View personal reservation history

Use a proper roles/permissions system.

---

## Domain Modeling (Important):

### Entities:

* User
* Book
* Copy
* Reservation
* Queue (optional but recommended)
* Penalty (optional)

### Requirements:

* A Book can have multiple Copies.
* A Copy belongs to one Book.
* A Reservation is linked to a specific Copy (NOT a Book).
* A User can have multiple Reservations.
* If no copies are available, users can join a waiting Queue.

---

## Business Logic:

### Reservation Flow:

* When a user requests a book:

  * If a copy is available → create reservation.
  * If no copies available → add user to queue.

### Reservation Expiration:

* Reservations expire after a fixed time (e.g., 24 hours).
* Expired reservations should:

  * Release the copy
  * Assign it to the next user in queue (if exists)

### Penalty System:

* If a user does not complete a reservation in time:

  * Apply a temporary penalty (e.g., cannot reserve for X days)

### Concurrency:

* Prevent multiple users from reserving the same copy at the same time.

---

## Technical Requirements:

### Architecture:

* Do NOT put business logic in controllers.
* Use:

  * Service classes (e.g., ReservationService)
  * Action classes (e.g., ReserveBookAction)
* Use Policies for authorization.

### Database:

* Design proper migrations with relationships and constraints.
* Use enums for statuses (reservation status, copy status).

### Queue & Jobs:

* Use Laravel Jobs for:

  * Handling expiration of reservations
  * Processing queue logic

### Scheduling:

* Use Laravel Scheduler to check expired reservations periodically.

---

## Packages:

* Use Laravel Breeze for authentication.
* Use a roles/permissions package (e.g., Spatie).
* Use Laravel Debugbar for development.

---

## Deliverables:

1. Database schema design (tables + fields + relationships)
2. Eloquent model relationships
3. List of services and actions with responsibilities
4. Key workflows (reservation, queue handling, expiration)
5. Suggested folder structure
6. Potential edge cases and how to handle them

---

## Constraints:

* Keep it monolithic (no microservices).
* Use Blade (no SPA frameworks).
* Keep UI simple, focus on backend logic.
* Avoid unnecessary complexity.

---

## Goal:

This project should demonstrate strong backend engineering skills, including:

* Proper domain modeling
* Clean architecture
* Real-world business logic handling
* Maintainable Laravel code structure
