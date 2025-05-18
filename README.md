![Laravel CI](https://github.com/shaonmajumder/competitive-programming-platform/actions/workflows/laravel.yml/badge.svg)

## ✅ Continuous Integration (CI) with GitHub Actions

![Laravel CI](https://github.com/shaonmajumder/laravel-judge-system/actions/workflows/laravel.yml/badge.svg)

This project uses **GitHub Actions** for automated testing and validation on every push and pull request to the `main` branch.

---

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

---

### 📁 GitHub Actions Workflow File

You can find the configuration file at: .github/workflows/laravel.yml

---

### 💡 Notes

-   ✅ **No SQLite is used** — tests run using your real **MySQL** and **Redis** services, just like your production/dev environment.
-   🧪 **Test failures or code issues automatically fail the workflow**, blocking broken PRs from merging into `main`.
-   🧹 The environment is isolated and reproducible, ensuring consistency across local and CI runs.

---

Recommendation: Use a Docker sandbox or tools like Firejail or isolate via a containerized worker VM, ideally using:

Write unittest

Domain-Driven Design (DDD) pattern with modules for features.

5. Submission Engine
   Code editor (Monaco) + language selector (C/C++, Java, Python, JS, etc.)
   Judge queue (RabbitMQ/Redis Queue)
   Secure sandbox for code execution (Docker-based runner)

## 🧪 Testing Strategy

-   Unit tests for all services (PHPUnit) - Done
    Integration tests for API + database
    Mocked judge service for end-to-end contest simulations
    Test coverage reports + CI badge

Integration tests:

Write tests that hit your real API endpoints.

Use an in-memory or test database to avoid messing with production data.

Test both success and failure scenarios.

Clean up after tests.

CI setup:

Use GitHub Actions, GitLab CI, or any platform you prefer.

Run tests automatically on each push or pull request.

Report results and optionally fail the build on test failures.
