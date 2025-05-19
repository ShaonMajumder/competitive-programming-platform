# Competitive Programming Platform

![Laravel CI](https://github.com/ShaonMajumder/competitive-programming-platform/actions/workflows/laravel.yml/badge.svg)
[![codecov](https://codecov.io/gh/ShaonMajumder/competitive-programming-platform/branch/main/graph/badge.svg)](https://codecov.io/gh/ShaonMajumder/competitive-programming-platform)

A full-featured platform for compiling, executing, and judging code submissions across multiple languages — ideal for coding contests or training environments.

### 🎯 What This Platform Offers

-   Submission & Evaluation Engine: Code is submitted to a backend judge system via a queue, where it is compiled and executed.
-   Live contest simulation: Ideal for timed competitions and learning environments.
-   Scalable backend: Designed with future growth and sandbox security in mind.

## 🛠️ Submission Engine Overview

**Figure:** running C++, python code
![CI Demo](screenshots/cp_2025-05-19%2012-25-57.gif)

## ✅ Continuous Integration (CI) with GitHub Actions

**Figure:** GitHub Actions running PHPUnit tests, when pushed to main branch
![CI Demo](screenshots/ci_2025-05-19%2012-25-57.gif)

🔄 On each push/PR to main, GitHub Actions sets up PHP, MySQL, Redis, installs dependencies, configures Laravel, runs migrations & PHPUnit tests—failing on issues to block broken PRs.

➡️ Config: .github/workflows/laravel.yml

---

## 🧰 Tech Stack

| Area             | Technologies Used                                |
| ---------------- | ------------------------------------------------ |
| **Frontend**     | ReactJS, Monaco Editor                           |
| **Backend**      | Laravel (PHP), RESTful APIs                      |
| **Queue System** | Redis Queue (RabbitMQ planned)                   |
| **Database**     | MySQL                                            |
| **Testing**      | PHPUnit (Unit & Integration), Codecov, GitHub CI |
| **Sandboxing**   | Docker-based (Secure Code Execution - Planned)   |
| **CI/CD**        | GitHub Actions (CI Done, CD Planned)             |

---

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

### 📊 Code Coverage (Partially Done)

GitHub CI badge support via `coverage.php` or `Xdebug`
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

## 🛡️ Engineering Best Practices

-   ✅ Follows Domain-Driven Design (DDD) — feature-based module separation
-   ✅ CI-ready with GitHub Actions integration (works with every push) : CD pending
-   ✅ Uses Dockerized services to reflect production setup
-   🔒 Plans to integrate secure sandbox execution (Docker-based)
-   🧪 End-to-end simulation tests ensure contest flows are validated
-   Docker-based infrastructure mimics production closely

## 🧠 Development Notes (WIP)

-   🚧 Continuous Deployment (CD) setup pending & Demo gif
-   upcoming UX demo
-   best practices demo
-   ✅ Unit testing for core services
-   🧹 Static Analysis with PHPStan
-   🛠️ Integration testing in progress (DB setup ongoing)
-   Mocked judge service for end-to-end contest simulations : pending
-   🔄 Judge service simulation partially complete [java,nodejs,go]
-   📊 Code coverage + badge support (partially done)
-   Judge queue (RabbitMQ Queue) - currently laravel-queue used
-   Secure sandbox for code execution (Docker-based runner)
-   Use an in-memory or test database to avoid messing with production data.
-   Test both success and failure scenarios.
-   Clean up after tests in integration tests.

## 👨‍💻 Built & Maintained By

**Shaon Majumder**  
Senior Software Engineer  
Open source contributor | Laravel ecosystem expert | System design advocate  
🔗 [LinkedIn](https://linkedin.com/in/shaonmajumder) • [Portfolio](https://github.com/ShaonMajumder)
