# Blue Ridge Bounty - Farmers Market Website

**Capstone Project for WEB-289**  
Created by: Taylor Weston Cooper
Date: April 16, 2025

## Overview

Blue Ridge Bounty is a full-stack web application built for a local farmers market. The platform connects community members with local vendors, manages weekly market schedules, and enables administrative oversight of market operations, vendors, and users.

## Tech Stack

- **Frontend:** HTML, CSS (custom), JavaScript
- **Backend:** PHP (OOP structure)
- **Database:** MySQL
- **Development Tools:** Laragon, phpMyAdmin, VS Code
- **APIs:** Google reCAPTCHA v2
- **Security:** Password hashing (`bcrypt`), session-based auth, CAPTCHA verification
- **Hosting Tools:** GitHub (private repo),

## Features

- **Vendor Registration & Profile Management**
- **User Roles:** Member, Vendor, Admin, Super Admin
- **Vendor RSVP & Market Scheduling**
- **Product Listings with Tags & Categories**
- **Image Upload & Lazy Loading**
- **Admin Panel with Activation/Deactivation**
- **Super Admin Panel with Additional Oversight**
- **Homepage Market Status Management**
- **reCAPTCHA Validation**
- **Breadcrumb Navigation & Back Button History**
- **Soft Deletes on Markets**
- **404 Error Logging**
- **Responsive Design**

## Database Overview

![ERD Diagram](https://dbdiagram.io/d/289DB-updated-67a3c27e263d6cf9a02b8ae2)

- 14 interconnected tables
- Normalized structure for vendors, products, market weeks, users, images, and tags
- All sensitive relationships use `ON DELETE CASCADE` or soft delete strategy where appropriate

## Security Notes

- `config.php`, `db_credentials.php`, and other sensitive files are `.gitignore`d
- All passwords are encrypted using `bcrypt`

## Testing & Feedback

- Multiple rounds of usability testing
- Edge case testing for registration, vendor approval, and RSVP submission
- CAPTCHA validation rigorously tested against bots/spam

## To Do / Known Issues

- Image optimization could be further enhanced with dynamic resizing
- Admin dashboard analytics (optional)
- CSV export for admin data (optional)

## How to Deploy (Local Dev)

1. Clone the repo
2. Create your `.env` and `config.php` files
3. Import the SQL dump into your MySQL server
4. Start your local dev environment with Laragon or XAMPP
5. Access via `localhost/blueridgebounty`

---
