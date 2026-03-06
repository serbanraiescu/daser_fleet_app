# AGENTS MUST KNOW - FleetLog Project

This file contains critical architectural patterns, naming conventions, and logic rules for the FleetLog project. **READ THIS AT THE START OF EVERY SESSION.**

## 1. Database & Migrations
- **Pattern**: NEVER run direct SQL queries to modify schema. ALWAYS create a new migration file in `fleetlog/migrations/`.
- **Naming**: Use `NNN_description.php` format.
- **Table: `system_settings`**:
  - Columns: `key` (PK), `value` (TEXT). 
  - *Note*: A previous agent mistakenly used `setting_key`/`setting_value`. DO NOT repeat this. Always use `key` and `value`.

## 2. Vehicle Expirations Pattern
When adding a new expiration type (e.g., **Tahograf**, **Casco**, **Cantar**):
1. **Migration**: Add a `DATE` column to the `vehicles` table (e.g., `expiry_tahograf`).
2. **Dashboard**: Update `TenantController::dashboard` query to include the new column and add it to the `GREATEST()` calculation for the alert list.
3. **SMS/Email Alerts**: 
   - Add the new type to the logic in `SuperAdminController::triggerAlerts`.
   - Ensure the `TemplateService` recognizes the new `{expiry_type}`.
4. **UI**: Update vehicle add/edit views.

## 3. SMS Gateway System
- **API Key**: Use `SMS_API_KEY` in `.env` and `sms_gateway_key` in `system_settings`.
- **URL**: Use the root URL `https://fleet.daserdesign.ro/` in the Android app to bypass ModSecurity blocks on the `/api/` folder.
- **Endpoints**: `sms_pending.php` and `sms_confirm.php` are located in the root for bypass and in `/api/` for backup.
- **Logs**: Diagnostic logs are located at `fleetlog/storage/sms_debug.txt`.

## 4. Multi-Tenant Notifications
- **Routing**: SMS/Email alerts must be routed based on the Tenant's contact information:
  1. `notification_phone` / `notification_emails` (Dedicated for alerts).
  2. `contact_phone` / `email` (Fallback).

## 5. UI & Styling
- **CSS**: Uses TailwindCSS.
- **Components**: Use the premium slate/blue dashboard aesthetic.
- **Flash Messages**: Use `$_SESSION['flash_success']` and `$_SESSION['flash_error']`.
