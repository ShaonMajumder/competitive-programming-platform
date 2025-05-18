![Laravel CI](https://github.com/shaonmajumder/competitive-programming-platform/actions/workflows/laravel.yml/badge.svg)

## ✅ Continuous Integration (CI) with GitHub Actions

This project uses **GitHub Actions** for automated testing and validation on every push and pull request to the `main` branch.

### 🔄 What Happens in CI?

On each push or pull request to `main`:

1. **GitHub Actions is triggered automatically.**
2. The CI workflow sets up:
    - ✅ PHP 8.0
    - 🐬 MySQL 5.7 (via Docker service container)
    - 🚀 Redis (via Docker service container)
3. Steps executed:
    - Composer dependencies installed
    - Laravel application key generated
    - Storage and cache directories made writable
    - **Database migrations are executed**
    - PHPUnit tests run using **your actual MySQL and Redis setup**

### 📁 GitHub Actions Workflow File

You can find the configuration file at: .github/workflows/laravel.yml

### 💡 Notes

-   ✅ **No SQLite is used** — tests run using your real **MySQL** and **Redis** services, just like your production/dev environment.
-   🧪 **Test failures or code issues automatically fail the workflow**, blocking broken PRs from merging into `main`.
-   🧹 The environment is isolated and reproducible, ensuring consistency across local and CI runs.

---

## 📊 Code Coverage

Coverage reports via PHPUnit + Xdebug

**View the report:**

**Text:**

```bash
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
```

**HTML:**

```bash
php artisan test --coverage-html=storage/coverage-report
```

**Online report:** [Codecov Report](https://app.codecov.io/gh/ShaonMajumder/competitive-programming-platform)

---

### 💡 Notes

-   ✅ **No SQLite is used** — tests run using your real **MySQL** and **Redis** services, just like your production/dev environment.
-   🧪 **Test failures or code issues automatically fail the workflow**, blocking broken PRs from merging into `main`.
-   🧹 The environment is isolated and reproducible, ensuring consistency across local and CI runs.

---

## 🧪 Testing Strategy

-   Unit tests for all services (PHPUnit) - Done
    Integration tests for API + database
    Mocked judge service for end-to-end contest simulations
    Test coverage reports + CI badge

## 🧪 Testing Strategy

A solid test strategy ensures system reliability and developer confidence.

### ✅ Unit Testing

-   Written using **PHPUnit**
-   Covers:
    -   Core services
    -   Business logic
    -   Utility classes
-   Mocking used where necessary to isolate dependencies

### ✅ Integration Testing

-   Hits real **API endpoints**
-   Uses **real MySQL & Redis services** (Dockerized)
-   Verifies actual system behavior including:
    -   Database transactions
    -   Queued jobs
    -   Auth flows
-   Ensures both **success and failure paths** are tested
-   Database is reset or migrated cleanly between test runs

### ✅ Judge Simulation

-   End-to-end tests simulate actual code submission flows
-   The Judge service is mocked or stubbed for test environments
-   Supports future plug-in of a sandboxed Docker runner for full code evaluation

### ✅ Code Coverage (Planned)

-   PHPUnit coverage reports can be added
-   GitHub CI badge support via `coverage.php` or `Xdebug`

---

### 🛡️ Best Practices Followed

-   Domain-Driven Design (DDD) pattern with feature-based module separation
-   CI/CD ready: GitHub Actions config validates every change : CD pending
-   Docker-based infrastructure mimics production closely
-   Sandbox recommendation: avoid knowing .env and system params : pending

5. Submission Engine
   Code editor (Monaco) + language selector (C/C++, Java, Python, JS, etc.)
   Judge queue (RabbitMQ/Redis Queue)
   Secure sandbox for code execution (Docker-based runner)

Integration tests:

Write tests that hit your real API endpoints.

Use an in-memory or test database to avoid messing with production data.

Test both success and failure scenarios.

Clean up after tests.

CI setup:

Use GitHub Actions, GitLab CI, or any platform you prefer.

Run tests automatically on each push or pull request.

Report results and optionally fail the build on test failures.

## Code Coverage report

XDEBUG_MODE=coverage ./vendor/bin/phpunit --cov
erage-text
php artisan test --coverage-html=storage/coverage-report
if you want to see - https://app.codecov.io/gh/ShaonMajumder/competitive-programming-platform
