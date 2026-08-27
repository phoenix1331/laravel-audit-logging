# Laravel Audit Logging

A transparent, observer-driven audit trail for Eloquent models, written to MongoDB while the primary application data lives in MySQL.

---

## What this demonstrates

Most audit logging implementations leak into the code that doesn't care about auditing: controllers end up calling `AuditLog::create(...)` right after every `save()`, which means every new mutation path is one missed line away from a silent gap in the trail. This project takes a different approach: audit logging is wired entirely through Eloquent's model events, so **the controller has zero audit-specific code**. A model implements `AuditableInterface`, gets its observer registered once, and every `created`, `updated`, and `deleted` event on that model is captured automatically from then on, including a field-level from/to change set for updates.

The other idea worth calling out is the split datastore: relational data (the `Post` model) lives in MySQL as normal, while the audit trail itself lives in MongoDB as flexible, schemaless documents. Audit events don't have a fixed shape (a CREATE event looks nothing like an UPDATE diff), which is exactly the kind of data MongoDB's document model handles more naturally than a rigid relational table with a JSON blob column bolted on.

Core concepts covered:

- Eloquent observers as a cross-cutting concern, kept out of business logic
- A contract-driven opt-in model (`AuditableInterface`) so any model can become auditable in three steps
- Mixed persistence: MySQL for application data, MongoDB for the audit trail
- Capturing request context (IP, user agent, route name, authenticated user) transparently via a bound singleton service
- An extension point (`organisation_id` resolution) left deliberately stubbed to show where multi-tenant context would plug in

---

## Tech stack

- **Laravel 13** on PHP 8.4
- **MySQL 8.0** for application data (`posts`)
- **MongoDB 7.0** (`mongodb/laravel-mongodb`) for the `audit_logs` collection
- **Docker + FrankenPHP** as the runtime, via `docker-compose.yml`
- **Laravel Pint** for code style, enforced by a pre-commit hook
- **osv-scanner** for dependency vulnerability scanning, also enforced by the pre-commit hook

---

## Getting started

### Prerequisites

- Docker and Docker Compose
- [`osv-scanner`](https://github.com/google/osv-scanner) installed locally (`go install github.com/google/osv-scanner/cmd/osv-scanner@latest`) - required by the pre-commit hook

### Setup

```bash
cp .env.example .env
make build
make up
make migrate
```

Then open **http://localhost:8000** in your browser.

| Command | Description |
|---|---|
| `make up` | Start containers in the background |
| `make down` | Stop and remove containers |
| `make build` | Build the app image |
| `make shell` | Open a shell in the app container |
| `make migrate` | Run database migrations |
| `make seed` | Seed the database |
| `make test` | Run the test suite |
| `make logs` | Tail app container logs |
| `make fresh` | Drop all tables and re-run migrations with seeding |

### Ports

Port 3306 was already in use on the host by another local MySQL instance, so this project's MySQL is exposed on a different host port. Adjust `docker-compose.yml` if any of these clash with something else already running:

| Service | Host port | Container port |
|---|---|---|
| App (FrankenPHP) | 8000 | 8000 |
| MySQL | 3307 | 3306 |
| MongoDB | 27017 | 27017 |

---

## Environment variables

| Key | Description | Default |
|---|---|---|
| `APP_URL` | Base URL of the app | `http://localhost:8000` |
| `DB_CONNECTION` | Default database connection | `mysql` |
| `DB_HOST` | MySQL host (Docker service name) | `mysql` |
| `DB_PORT` | MySQL port (container-internal) | `3306` |
| `DB_DATABASE` | MySQL database name | `audit_logging` |
| `DB_USERNAME` | MySQL username | `root` |
| `DB_PASSWORD` | MySQL password | *(empty)* |
| `MONGODB_URI` | MongoDB connection string (Docker service name) | `mongodb://mongodb:27017` |
| `MONGODB_DATABASE` | MongoDB database name | `audit_logging` |

---

## Endpoints

The app provides a simple web interface for managing posts:

| URL | Action |
|---|---|
| `GET /posts` | List all posts |
| `GET /posts/create` | New post form |
| `POST /posts` | Submit new post (triggers a CREATE audit log) |
| `GET /posts/{id}` | Show a post and its audit log timeline |
| `GET /posts/{id}/edit` | Edit post form |
| `PUT /posts/{id}` | Submit edit (triggers an UPDATE audit log with field diffs) |
| `DELETE /posts/{id}` | Delete post (triggers a DELETE audit log) |



---

## Architecture notes

```
Browser Form Submit
    |
    v
PostController          (no audit code - completely transparent)
    |  Post::create() / update() / delete()
    v
Eloquent Model           (implements AuditableInterface)
    |  fires created / updated / deleted events
    v
AuditObserver            (listens to Eloquent model events)
    |  builds event_data with from/to change set
    v
AuditLogger service      (extracts request context)
    |  ip_address, user_agent, request_route, user_id
    v
AuditLog::create()       (MongoDB Eloquent model)
    |
    v
MongoDB  ->  audit_logging.audit_logs collection
```

### Making a model auditable

Three steps to add audit logging to any Eloquent model:

**1. Implement `AuditableInterface`:**

```php
use App\Contracts\AuditableInterface;
use App\Enums\AuditLogAction;
use App\Enums\AuditLogType;

class Article extends Model implements AuditableInterface
{
    public function getAuditType(): AuditLogType
    {
        return AuditLogType::ARTICLE;
    }

    public function getAuditActions(): array
    {
        return [
            AuditLogAction::CREATE,
            AuditLogAction::UPDATE,
            AuditLogAction::DELETE,
        ];
        // Omit an action to disable that event, e.g. no DELETE logging
    }
}
```

**2. Add a case to `AuditLogType`** if the model doesn't already have one.

**3. Register the observer** in `AuditServiceProvider::boot()`:

```php
public function boot(): void
{
    Post::observe(AuditObserver::class);
    Article::observe(AuditObserver::class); // add this line
}
```

No changes to the controller are needed.

### Why the `organisation_id` stub

`AuditLogger::resolveOrganisationId()` parses the `Origin` header for a subdomain but always returns `null`. It's left this way deliberately, this is a single-tenant demo, but the extension point is real: a multi-tenant app would resolve the subdomain to an `Organisation` and scope every audit log to it, which is exactly the kind of context that's easy to forget if audit logging isn't centralised like this.

### Why the pre-commit hook matters here

Every commit runs Pint (style) and osv-scanner (dependency vulnerabilities) before it's allowed through. For a project whose entire value proposition is "here's a reliable trail of what happened," it would be a bit self-defeating to ship it with unreviewed dependency vulnerabilities or inconsistent code that's harder to audit at the source level.
