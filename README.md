# Real-Time Bus Seat Booking Engine Simulation (CI4)

> A learning-oriented concurrency-safe booking engine simulation inspired by real-world reservation systems.

This project demonstrates how real-world booking systems  
(railway, airline, movie ticket, ecommerce inventory)  
prevent double booking during simultaneous requests using:

- ACID transactions
- Row-level locking
- `FOR UPDATE`
- Concurrency control
- Transaction rollback
- Parallel booking simulation
- Booking audit logs

---

# Problem Statement

When multiple users attempt to book the same seat simultaneously,  
race conditions can occur leading to double booking.

This project demonstrates how database transactions and row-level  
locking solve concurrency problems in real-time booking systems.

The project intentionally focuses on transaction isolation and concurrency handling rather than full business workflows.

---

# Features

- Dynamic bus seat rendering
- Interactive seat selection
- AJAX-based booking flow
- ACID transaction handling
- Row-level locking using `FOR UPDATE`
- Concurrency-safe booking
- Parallel booking simulation
- Transaction audit logging
- Real-time booking conflict prevention

---

# Tech Stack

- PHP 8+
- CodeIgniter 4
- MySQL (InnoDB)
- Bootstrap 5
- JavaScript (AJAX)
- Spark CLI

---

# Key Technical Concepts Demonstrated

- ACID Transactions
- Isolation Level Handling
- Row-Level Locking
- `FOR UPDATE` Query Locking
- Race Condition Prevention
- Transaction Rollback
- Concurrent Request Simulation
- Parallel cURL Requests
- Audit Logging
- Request Serialization

---

# Screenshots

## Frontend Booking UI

<img width="1366" height="643" alt="Bus Seat Booking" src="https://github.com/user-attachments/assets/7557d301-2ceb-40de-9309-4eea4439941d" />

Example:
- Dynamic seat rendering
- Available/Booked seat indicators
- Interactive seat selection

---

## Concurrent Booking Simulation

<img width="716" height="611" alt="real-time-booking-testing-command" src="https://github.com/user-attachments/assets/79332d58-4e9d-4cf3-9a82-0124bd40d07e" />

Example output:

```bash
php spark simulate:booking 1 20
```

Expected Result:

- 1 booking succeeds
- remaining requests fail safely
- no double booking occurs

---

# Simulation Command Usage

Run concurrent booking simulation:

```bash
php spark simulate:booking 1 20
```

Explanation:

- `1` = seat_id
- `20` = concurrent booking requests

This command simulates multiple users attempting to book the same seat simultaneously.

---

# Real-World Systems Using Similar Concepts

- Railway ticket booking
- Airline reservation systems
- Movie ticket booking
- Ecommerce inventory reservation
- Flash sale systems
- Payment transaction systems

---

# Booking Flow Architecture

```text
User selects seat
        ↓
AJAX booking request
        ↓
BEGIN TRANSACTION
        ↓
SELECT ... FOR UPDATE
        ↓
Row-level lock acquired
        ↓
Seat availability re-check
        ↓
Booking confirmation
        ↓
COMMIT / ROLLBACK
```

---

# Concurrent Booking Result

During simultaneous booking attempts:

- First transaction acquires row lock
- Remaining requests wait in queue
- After commit, queued requests re-check seat status
- Already-booked requests safely rollback

This prevents race conditions and double booking.

---

# Future Improvements

- Seat hold timeout system
- Payment gateway simulation
- Live seat refresh using WebSockets
- Deadlock simulation
- Distributed locking
- Queue-based booking architecture
- Redis reservation layer

---

# Resume-Friendly Project Summary

Developed a concurrency-safe real-time bus booking simulation system using CodeIgniter 4 implementing ACID transactions, row-level locking, transaction auditing, and parallel booking simulation to prevent race conditions during simultaneous seat reservations.


