# PulseCheck - Concept Overview

> A self-hosted uptime monitoring platform for websites and APIs.

![Status Badge](https://img.shields.io/badge/status-concept-blue.svg)
![Stage](https://img.shields.io/badge/stage-planning-lightgrey.svg)

---

## Project Vision

PulseCheck is a monitoring system designed to help developers and small teams detect downtime and performance issues in real time.

The goal is simple:

- Reduce blind spots
- Shorten downtime response time
- Provide clear service health visibility

---

## Problem Statement

Managing multiple websites and APIs often leads to delayed incident awareness due to manual checking.

PulseCheck solves this by providing:

- Continuous automated health checks
- Centralized service monitoring dashboard
- Fast incident detection and alerting
- Historical reliability tracking

---

## Architecture Approach

PulseCheck follows a **SPA + API architecture**:

- Frontend: Vue 3 SPA with Vue Router
- Backend: Laravel 11 REST API

### Flow

Vue SPA (Frontend)
↓
API Requests (Axios / Fetch)
↓
Laravel 11 API (Backend)
↓
Queue Workers + Scheduler (Redis)
↓
PostgreSQL Database

---

## Planned Tech Stack

| Category          | Technology                           |
| ----------------- | ------------------------------------ |
| Backend Framework | Laravel 11 (API-only)                |
| Frontend          | Vue.js 3 + Vue Router + Tailwind CSS |
| Database          | PostgreSQL (AWS RDS)                 |
| Queue System      | Redis                                |
| Background Jobs   | Laravel Queue Workers                |
| Scheduling        | Laravel Scheduler (cron)             |
| Email Alerts      | AWS SES                              |
| Deployment        | AWS EC2 + RDS (Free Tier)            |
| Containerization  | Docker + Docker Compose              |

---

## Project Structure (SPA Architecture)

pulsecheck/
|
|-- app/
| |-- Http/
| | |-- Controllers/
| | | |-- Api/
| | | | |-- MonitorController.php
| | | | |-- IncidentController.php
| | | | `-- StatusController.php
|   |   |   |
|   |   |   `-- Web/
| | | `-- AppController.php
|   |
|   |-- Jobs/
|   |   |-- CheckMonitorJob.php
|   |   `-- SendAlertJob.php
| |
| `-- Models/
|       |-- Monitor.php
|       |-- MonitorCheck.php
|       `-- Incident.php
|
|-- routes/
| |-- api.php
| |-- web.php
| `-- console.php
|
|-- resources/
|   |-- js/
|   |   |-- router/
|   |   |   `-- index.js
| | |-- pages/
| | | |-- Dashboard.vue
| | | |-- Monitors.vue
| | | `-- Incidents.vue
|   |   |
|   |   |-- components/
|   |   `-- app.js
| |
| |-- views/
| | `-- app.blade.php
|   |
|   `-- css/
| `-- app.css
|
|-- docker-compose.yml
|-- Dockerfile
|-- .env
`-- package.json

---

## Core Product Capabilities

- Continuous uptime monitoring for websites and APIs
- Response time tracking
- Incident detection via failure thresholds
- Automated alerting system
- Historical reliability analytics
- Public status page support

---

## Intended User Flow

1. User logs into Vue SPA dashboard
2. User adds a monitor (website/API)
3. Laravel scheduler runs health checks
4. Results are stored in database
5. Failures trigger incidents automatically
6. Alerts are sent via AWS SES
7. Dashboard updates via API

---

## Conceptual Architecture

- Presentation Layer: Vue SPA dashboard + status page
- API Layer: Laravel REST API
- Monitoring Layer: Scheduler + Queue Workers
- Data Layer: PostgreSQL storage

---

## Key Design Decision

### No Blade UI Rendering

Blade is only used for SPA entry point.

Vue Router handles all navigation and UI rendering.

---

## Example Use Case

A freelance developer manages multiple client services:

- Websites (medium priority)
- APIs (high priority)
- Portfolio (low priority)

PulseCheck automatically:

- Detects downtime early
- Creates incidents
- Sends alerts
- Tracks recovery history

---

## Development Roadmap

### Phase 1 - Foundation

- Define models and API structure
- Setup SPA architecture

### Phase 2 - Monitoring Engine

- Health check scheduler
- Queue-based execution
- Failure detection rules

### Phase 3 - Alert System

- Email notifications
- Incident lifecycle logic

### Phase 4 - Dashboard UX

- Vue dashboard pages
- Charts and analytics
- Status views

### Phase 5 - Production Readiness

- Docker setup
- AWS deployment
- Security + scaling

---

## Success Criteria

- Faster downtime detection
- Clear incident visibility
- Reduced alert noise
- Actionable historical insights

---

## Project Status

PulseCheck is currently in **concept and planning stage**.

Implementation will begin once architecture and API design are finalized.
