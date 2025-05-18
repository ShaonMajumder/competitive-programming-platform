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
