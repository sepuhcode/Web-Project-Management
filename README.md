SIS Dashboard
Overview

SIS Dashboard is a web-based project management system designed to manage project workflows including installations, programming, scheduling, documentation, and reporting.

The system is built using PHP and MySQL with a procedural architecture.

Features
Project Management
Installation Tracking
Programming Task Management
Scheduling System
Progress Reporting
Documentation Upload
Troubleshooting Logs
Tech Stack
PHP (Procedural)
MySQL
HTML/CSS
JavaScript (minimal)
Project Structure
sis-dashboard/
│
├── *.php                  # Core application pages (mixed logic & view)
├── config.php             # Database configuration
├── styles.css             # Main styling
├── upload-enhanced.css    # Upload UI styling
│
├── Database/
│   └── team_project.sql   # Database schema
│
├── uploads/               # Uploaded files storage
Database Setup
Import the database:
Database/team_project.sql
Configure connection:
config.php
How to Run
Place project in:
htdocs/ (XAMPP) or www/ (Laragon)
Start Apache & MySQL
Open in browser:
http://localhost/sis-dashboard/
Current Limitations
No MVC architecture (logic and view are tightly coupled)
No centralized routing
Code duplication across modules
Limited input validation and security handling
Recommended Improvements
Refactor into MVC architecture
Implement routing system
Use prepared statements for database queries
Create reusable components (header, footer)
Improve file upload security
Optimize code structure for scalability
Author

Developed as a team project for system integration and project management learning purposes.
