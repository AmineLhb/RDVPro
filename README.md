# RDVPro — Multi-Tenant Appointment & CRM SaaS

RDVPro is a production-ready Laravel 11 multi-tenant SaaS application designed for service-based businesses (clinics, salons, gyms, coaches). It provides database-per-tenant isolation using the **stancl/tenancy** package.

---

## Core Architecture

1. **Central Application** (`admin.rdvpro.test`):
   - Managed by a **SuperAdmin** only.
   - Central database (`rdvpro_central`) registers tenants and domain mapping records.
   - Actions: Create/Suspend/Reactivate tenants and manage domains.

2. **Tenant Application** (`{subdomain}.rdvpro.test`):
   - Each tenant has an isolated database (prefixed with `tenant`).
   - Managed by the business owner (Tenant Admin) who accesses their private appointments, customers, services, templates, and config settings.

---

## Local Installation & Setup

### Prerequisites
- PHP 8.2+
- MySQL (configured under MAMP, typically port `3306`)
- Composer

### 1. Database Creation
Create the central database `rdvpro_central` in your MySQL server.

### 2. Configure Environment (`.env`)
Ensure your database credentials match your local MAMP settings. Example:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rdvpro_central
DB_USERNAME=root
DB_PASSWORD=root

# Queue driver (sync runs provisioning synchronously for easy local testing)
QUEUE_CONNECTION=sync
```

### 3. Run Migrations & Seeders
Execute the migrations to set up the central database and automatically provision the `bahja` tenant with its isolated database and sample data:
```bash
php artisan migrate
php artisan db:seed
```

### 4. Configure Virtual Hosts (Local Domain Setup)
To test the wildcard subdomain routing locally, map the domains in your Windows `hosts` file:
Open `C:\Windows\System32\drivers\etc\hosts` as Administrator and add:
```text
127.0.0.1 admin.rdvpro.test
127.0.0.1 bahja.rdvpro.test
```

Then configure a Virtual Host in MAMP pointing to `C:\MAMP\htdocs\RDVPro\public`:
```apache
<VirtualHost *:8000>
    DocumentRoot "C:/MAMP/htdocs/RDVPro/public"
    ServerName rdvpro.test
    ServerAlias *.rdvpro.test
</VirtualHost>
```

---

## Running the Application Locally
Run the Laravel development server:
```bash
php artisan serve --port=8000
```

### Credentials
- **Central SuperAdmin Area**: [http://admin.rdvpro.test:8000](http://admin.rdvpro.test:8000)
  - E-mail: `admin@rdvpro.ma`
  - Password: `password`

- **Tenant Admin Area (Salon Bahja)**: [http://bahja.rdvpro.test:8000](http://bahja.rdvpro.test:8000)
  - E-mail: `karim@bahja.ma`
  - Password: `password`

---

## Features & Business Logic
- **Overlapping Booking Check**: Prevent double-booking for the same staff resource.
- **Working Hours Check**: Validate that appointment requests fall entirely inside the staff member's working hours.
- **Dynamic Render Templates**: Set WhatsApp, SMS, and Email templates with tags (`{name}`, `{business}`, `{date}`, `{time}`, etc.) and view them in a simulated live-render view.
- **Simulated Dispatch Viewer**: Every notification sent is stored in the `notification_logs` table of the tenant, viewable directly from the Tenant Admin dashboard.
- **PDF & Excel Reports**: Export revenue trends, booking statuses, top services, and staff stats.

---

## Customer History & Records (Privacy & CNDP Compliance)

RDVPro includes a comprehensive record-keeping and file attachment system for customers, accessible only to authenticated tenant staff.

### 1. Private Storage & Access Control
- **No Public Assets**: All uploaded files (X-rays, styling references, patient history) are saved directly in Laravel's private directory: `storage/app/records/{tenant_id}/`.
- **Authenticated Serve Route**: Files are served exclusively through the `/admin/attachments/{id}/serve` controller route. This route enforces that the requesting user belongs to the active tenant subdomain and has administrator/staff credentials.

### 2. Morocco CNDP Compliance (Law 09-08)
When using RDVPro for clinics or medical practitioners, strict local data-protection rules apply:
- **Physical Isolation**: Tenant isolation is physically achieved via a database-per-tenant architecture. No patient data or attachment files are shared across subdomains.
- **Access Traceability Log**: Every file interaction is captured in the system activity log (`storage/logs/laravel.log`) under the `[Activity Log]` tag. This includes:
  - Who uploaded / viewed / renamed / deleted a file (Staff user ID, email).
  - Exact file details (ID, original name, size, type).
  - Datetime of action.
- **Encryption at Rest**: For CNDP-compliant medical hosting:
  - Production servers should run full-disk encryption (e.g., BitLocker or LUKS).
  - MySQL databases should utilize Transparent Data Encryption (TDE) to protect customer record JSON columns (`entry_data`) at rest.
  - SSL/TLS is strictly enforced for all data transmission.

