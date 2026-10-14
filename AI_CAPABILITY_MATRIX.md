# AI Capability Matrix for London School Chat

This document evaluates what AI can and cannot do for this project, considering the current technical constraints:

- Backend: Laravel 5.6, PHP 7.1+/7.4 runtime
- Frontend: Vue 2, Laravel Mix 2, legacy Node toolchain
- Infra: Docker, MySQL 5.7, queue worker, Pusher, optional OpenAI + S3

Status legend:

- `YES` = AI can do this effectively in this repository
- `PARTIAL` = AI can help, but still needs human/environment support
- `NO` = Not realistic to do only with AI in this project setup

---

## 1) Capability Assessment (Your Requested Points)

| Capability | Status | Notes for This Project |
|---|---|---|
| `.md` file creation | YES | AI can create project docs, plans, runbooks, ADRs, test plans. |
| Planning of new feature development | YES | AI can break down features into tasks, risks, dependencies, milestones. |
| Design architecture and get suggestions | YES | AI can propose service boundaries, queue/event patterns, and trade-offs. |
| Design database structure | YES | AI can design tables, relations, indexes, migrations for MySQL 5.7. |
| Design API | YES | AI can design REST routes, payloads, validation, and error contracts. |
| Develop UI using UX, MCP | PARTIAL | AI can generate Vue/Blade UI and UX suggestions. MCP/browser automation can validate flows, but final UX acceptance is human. |
| Explain existing code & codebase | YES | AI can explain controllers, services, queue flow, routes, templates. |
| Identify risk | YES | AI can identify technical, operational, security, and delivery risks. |
| Code generation | YES | Strong fit for Laravel controllers/services, Vue components, SQL. |
| Code completion | YES | Strong fit in legacy PHP/Vue code too. |
| Code refactoring | PARTIAL | AI can refactor safely in chunks; full legacy refactor still needs regression testing. |
| Code conversion | PARTIAL | AI can convert patterns (jQuery -> Vue helpers, raw SQL -> query builder), but large framework upgrades are risky. |
| Write native DB queries | YES | AI can write MySQL 5.7 compatible SQL and optimize joins/index usage. |
| Regex generation | YES | AI can generate and test regex for validation/parsing tasks. |
| Debug code | YES | AI can inspect logs, trace call flow, reproduce and isolate defects. |
| Bug fix | YES | AI can patch backend/frontend bugs and add regression tests. |
| Unit testing + acceptance criteria validation | PARTIAL | AI can write PHPUnit tests and map acceptance criteria, but business acceptance still needs humans. |
| Integration testing | PARTIAL | AI can script and run integration tests locally; real integrations need valid credentials/services. |
| Mock data for testing | YES | AI can generate seed/factory data and edge-case datasets. |
| Test coverage analysis | PARTIAL | AI can analyze reports and suggest gaps; needs instrumentation and current test baseline. |
| Code review | YES | AI can perform static review for defects, regressions, missing tests. |
| Performance review | PARTIAL | AI can identify bottlenecks and add profiling points; real load/perf numbers need runtime tests. |
| Security & vulnerability review | PARTIAL | AI can do secure-code review and dependency checks; formal pentest/compliance is not AI-only. |
| Architecture compliance | PARTIAL | AI can check code against defined architecture rules if rules are written clearly. |
| Coding standards check | YES | AI can enforce standards and suggest cleanups in PRs. |
| Duplicate code detection | YES | AI can detect duplication and propose shared abstractions. |
| Dead code detection | PARTIAL | AI can identify likely dead code, but safe removal needs runtime/usage verification. |
| Dependency review | PARTIAL | AI can review outdated/vulnerable deps; upgrading legacy deps may break build/runtime. |
| PR summary | YES | AI can generate concise summaries, testing notes, and risk statements. |
| Review comments | YES | AI can draft responses/resolutions and propose follow-up commits. |
| Load testing | PARTIAL | AI can prepare k6/JMeter scripts; meaningful load tests need target environment and infra. |
| Prepare API documentation | YES | AI can generate endpoint docs and usage examples from code/routes. |
| Prepare technical documentation | YES | AI can create architecture, operations, troubleshooting docs. |
| Create Architecture Decision Records | YES | AI can generate ADRs with options, decision, consequences. |
| Code commit | YES | AI can stage and commit when explicitly requested by user. |
| Deployment | PARTIAL | AI can prepare scripts/checklists; actual production deploy requires human access/approvals. |
| Release automation | PARTIAL | AI can implement pipelines and release scripts, then humans validate and govern rollout. |

---

## 2) What Is Limited by Legacy Stack (Important)

Primary constraints in this repository:

- Laravel `5.6.*` and PHP `^7.1.3` reduce compatibility with modern packages and tooling.
- Frontend build is tied to older Node ecosystem (`laravel-mix` 2, `node-sass` 4, Vue 2).
- Some newer static analysis, test, security, and CI plugins may require newer PHP/Node.
- Major upgrades (Laravel/Vue/Node) are possible, but should be treated as separate migration programs.

Impact:

- AI can still deliver feature work and bug fixes effectively.
- AI-generated changes must be version-aware (avoid syntax/features unsupported by old runtime).
- Testing and dependency upgrades should be incremental and pinned for compatibility.

---

## 3) Cursor / Claude Code Perspective

### Utilize AI Skills

`YES` for process acceleration. Recommended skill usage patterns:

- Use skills for repeatable workflows (review, security review, docs, automation).
- Keep project-specific playbooks in markdown and reuse through prompts.
- For this repo, prioritize skills around legacy-safe coding and verification checklists.

### Utilize AI Rules

`YES` and strongly recommended.

- Create rules for Laravel 5.6/PHP 7.1 compatibility constraints.
- Enforce no unsupported syntax, no modern-only APIs, and strict migration patterns.
- Add code-style, testing-minimum, and security-review requirements per change.

### Utilize AI Agents

`YES`.

- Main coding agent for implementation.
- Specialized review agents for bug/security checks.
- Exploration agents for large codebase understanding.
- Use parallel agents for review + test writing + docs preparation.

### Utilize AI Plugin / MCP

`PARTIAL`.

- MCP/browser tools can validate UI flows and gather evidence.
- Depends on available configured MCP servers and permissions.
- Cannot replace all manual QA (especially auth-gated, multi-user, real-time behavior).

### Utilize AI Hooks

`YES`.

- Add hooks to run lint/tests/checklists automatically on agent events.
- Good for preventing regressions in legacy projects where mistakes are expensive.

### Utilize AI Commands

`YES`.

- Build command templates: test commands, queue checks, docker checks, doc generation.
- Create team-standard command prompts for common tasks (bugfix, test-first, review, PR prep).

---

## 4) Practical “AI-First but Safe” Operating Model

Use this for every new feature in this project:

1. AI creates feature plan + risk list.
2. AI proposes DB/API/UI design compatible with Laravel 5.6 + Vue 2.
3. AI implements in small commits (service/controller/view/test).
4. AI writes unit + feature tests and mocks external dependencies.
5. AI performs code review + security review + standards check.
6. Human validates acceptance criteria and critical user journeys.
7. AI prepares PR summary and release notes.
8. Human approves deployment/release.

---

## 5) Recommended Next Setup (High Value)

1. Add project AI rules for legacy compatibility (`PHP 7.1 safe`, `Vue 2 safe`, `MySQL 5.7 safe`).
2. Add hooks to run:
   - PHP syntax/lint
   - PHPUnit target suites
   - basic static checks
3. Create command library:
   - “new feature template”
   - “bugfix + regression test template”
   - “security review template”
   - “PR summary template”
4. Add baseline test suite for critical flows:
   - end simulation
   - queue report generation
   - summary download
   - private message send/fetch

---

## 6) Final Decision Summary

For this project, with current legacy constraints:

- Fully AI-enabled: planning, coding, refactoring (controlled), docs, review, test generation, PR preparation.
- AI + human required: integration tests, UX acceptance, performance validation, security sign-off, deployment/release.
- Not AI-only: production approvals, business acceptance, and formal compliance certification.

