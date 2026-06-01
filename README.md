# Accolta

[![CI](https://github.com/Rizzello/accolta/actions/workflows/ci.yml/badge.svg)](https://github.com/Rizzello/accolta/actions/workflows/ci.yml)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-5.x-FDAE4B)
![License](https://img.shields.io/github/license/Rizzello/accolta)

API-first submission backend for teams that need reliable public intake endpoints.

Accolta lets administrators configure public JSON endpoints for collecting structured submissions from external websites, CMSs, event pages, and product pages.

It is not a visual form builder: public forms are built externally, while Accolta receives, validates, stores, notifies, and helps manage submissions.

## What It Does

- Defines reusable form types with typed fields and validation rules.
- Creates public form endpoints identified by UUID.
- Accepts anonymous JSON submissions from external frontends.
- Validates payloads dynamically from the configured form type.
- Stores submissions as lightweight tickets.
- Sends optional email notifications through the queue.
- Generates public OpenAPI documentation per form.
- Provides a public Swagger UI page per form.
- Lets administrators and associated users manage forms and submissions in Filament.

## What It Does Not Do

- No visual form rendering.
- No configurable CORS management.
- No file uploads.
- No select/list or phone-specific field type.
- No exports.
- No CAPTCHA.
- No advanced ticket workflow.

## Stack

- Laravel 13
- PHP 8.3
- Filament 5
- Livewire 4
- PHPUnit
- Larastan / PHPStan
- Laravel Pint
- Laravel Sail

## Public Endpoints

Submit a public form:

```text
POST /api/forms/{uuid}/submissions
```

Get the dynamic OpenAPI schema:

```text
GET /api/forms/{uuid}/openapi.json
```

Open the public Swagger UI:

```text
GET /forms/{uuid}/swagger
```

## Access Rules

- Administrators can manage everything.
- Non-admin users can see only forms associated with them.
- Non-admin users can see only submissions belonging to their associated forms.
- Form types are managed by administrators only.
- Public submission endpoints do not require authentication.

## Submission Workflow

Submissions can be marked as:

- `new`
- `in_progress`
- `handled`
- `discarded`

Notification status can be:

- `not_required`
- `sent`
- `failed`

Email failures do not block submission creation. The submission remains stored and the notification status is updated accordingly.

## Field Types

Supported public field types:

- `string`
- `text`
- `email`
- `url`
- `number`
- `boolean`

## Development

Run the application with Sail:

```bash
vendor/bin/sail up -d
```

Run migrations and seed initial form types:

```bash
vendor/bin/sail artisan migrate:fresh --seed
```

Run tests:

```bash
vendor/bin/sail artisan test --compact
```

Run static analysis:

```bash
vendor/bin/sail composer analyse
```

Format code:

```bash
vendor/bin/sail composer format
```
