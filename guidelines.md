# Project Guidelines: Accountant

This document outlines the architectural patterns, coding standards, and best practices for the Accountant project.

## 1. Tech Stack Overview
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 (Composition API) with Inertia.js
- **Styling**: Tailwind CSS & Shadcn UI
- **Database**: SQLite (Development), PostgreSQL (Production target)
- **State Management**: Inertia (Server-side state) & Vue `ref`/`reactive` (Client-side state)

## 2. Backend Conventions (Laravel)

### 2.1 Controllers
- Use **Resource Controllers** for standard CRUD operations.
- Leverage **Inertia::render()** to return views with data.
- Keep controllers thin. Business logic should reside in Models or Service classes if it becomes complex.
- Use **Form Requests** for all validation logic (e.g., `TaxInformationRequest`).

### 2.2 Models
- **Mass Assignment**: Define `$fillable` attributes explicitly.
- **Type Casting**: Use the `casts()` method for attribute casting (dates, decimals, booleans, arrays).
- **Relationships**: Define clear Eloquent relationships (e.g., `User` hasMany `TaxReturn`).
- **Slugs**: Use `Spatie\Sluggable` for models that need URL-friendly identifiers (e.g., `Service`, `BlogPost`).
- **Media**: Use `Spatie\MediaLibrary` for file attachments (e.g., `TaxReturn` documents).

### 2.3 Database
- **Migrations**: Always use descriptive migration names and ensure they are reversible.
- **Naming**: Tables should be plural (`tax_returns`), and foreign keys should follow the `singular_table_id` convention (`user_id`).

### 2.4 API & Routing
- Use named routes for all links.
- Group routes by authentication requirements (using `auth` middleware).

## 3. Frontend Conventions (Vue 3 + Inertia)

### 3.1 Components
- Use **SFC (Single File Components)** with `<script setup>`.
- **Organization**:
    - `resources/js/Pages`: Views rendered by Inertia.
    - `resources/js/components`: Shared UI components (includes Shadcn UI in `ui/` subdirectory).
    - `resources/js/Layouts`: Common application layouts (e.g., `AppLayout.vue`).

### 3.2 State & Data Fetching
- Rely on **Inertia props** for initial data.
- Use `router.get()` or `router.post()` for navigation and data submission to preserve Inertia's SPA feel.
- Use `preserveState: true` and `replace: true` for filtering/searching to maintain scroll position and clean URLs.

### 3.3 UI & Styling
- Use **Tailwind CSS** utility classes for styling.
- Leverage **Shadcn UI** components for complex UI elements (Tables, Pagination, Inputs).
- Follow the project's existing color palette and spacing.

## 4. Coding Standards

### 4.1 PHP
- Follow **PSR-12** coding standards.
- Use `laravel/pint` for automated code formatting (`composer lint`).
- Declare strict types where possible.

### 4.2 JavaScript/TypeScript
- Use **ESLint** for code quality.
- Prefer `const` over `let`.
- Use descriptive variable and function names (camelCase).

## 5. Testing
- **Framework**: Pest PHP.
- **Location**: `tests/Feature` for integration/HTTP tests and `tests/Unit` for isolated logic.
- Aim for high coverage on critical paths like tax return submission and authentication.

## 6. Project Specific Logic
- **Tax Returns**: Multi-step submission process involves creating/updating `ClientProfile`, `TaxReturn`, `IncomeSource`, and uploading documents via Spatie MediaLibrary.
- **Bookings**: Driven by an `Availability` system where users book time slots.
- **Blog**: Includes a revision system to track changes to posts.
