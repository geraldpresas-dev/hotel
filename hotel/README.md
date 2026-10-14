# Sunrise Hotel Management System

A hotel booking system for my Web Application Development final project.
Guests can search for rooms and book them, and the hotel admin manages rooms and bookings.

- **Live site:** https://[firstname-lastname].infinityfreeapp.com
- **GitHub:** https://github.com/[your-username]/hotel-management
- **Student:** [Firstname Lastname]

## Features
- Guest registration and login (passwords hashed with `password_hash()`)
- Two roles: **admin** and **guest**
- **Guest:** search available rooms by dates and number of guests, book a room, view own bookings, cancel a booking
- **Admin:** dashboard with numbers, add / edit / delete rooms (CRUD), view all bookings, check-in / check-out / cancel guests
- A room cannot be double booked (the system checks if the dates overlap)
- Total price is computed automatically (nights x price)
- Booking page shows the total and room availability live (JavaScript fetch / AJAX)
- Validation on the client (JavaScript / HTML5) and on the server (PHP)
- Responsive design using Bootstrap 5 (sidebar turns into a menu on mobile)

## Technologies
HTML5, CSS3, Bootstrap 5, Bootstrap Icons, JavaScript, PHP, MySQL

## Security
- Prepared statements (PDO) to stop SQL injection
- `htmlspecialchars()` on output to stop XSS
- Passwords hashed, session ID regenerated at login
- Admin pages check the role in PHP, and guests can only cancel their own bookings
- The server checks the dates and availability again even if JavaScript is turned off

## Test accounts

| Role  | Username | Password |
|-------|----------|----------|
| Admin | admin    | admin123 |
| Guest | juan     | user123  |
| Guest | maria    | user123  |

## How to set up
1. Create a MySQL database (phpMyAdmin).
2. Import `database/schema.sql`.
3. Open `config.php` and type your database host, name, username and password.
4. Upload all files to the `htdocs` folder of your hosting (or XAMPP `htdocs` for local testing).
5. Open the site and login.

## Database
- `users` (id, username, full_name, password, role)
- `rooms` (id, room_number, type, price, capacity)
- `bookings` (id, user_id, room_id, check_in, check_out, total_price, status, created_at)

One user can have many bookings and one room can have many bookings.
Booking status can be `booked`, `checked_in`, `checked_out` or `cancelled`.
Two bookings overlap when `check_in < other check_out AND check_out > other check_in`.

## Files
- `config.php` - database connection and helper functions
- `header.php` / `footer.php` - shared layout (sidebar)
- `register.php`, `login.php`, `logout.php` - authentication
- `rooms.php`, `book.php`, `check_availability.php` - guest room search and booking
- `my_bookings.php`, `cancel_booking.php` - guest bookings
- `dashboard.php` - admin dashboard
- `admin_rooms.php`, `room_form.php`, `room_delete.php` - admin room CRUD
- `admin_bookings.php`, `booking_status.php` - admin booking management
- `script.js`, `style.css` - JavaScript and CSS
