# CLAUDE.md

This file defines the repository-level instructions for Claude Code, Cursor, and other AI coding assistants. Complete all placeholders and keep this file current as the project evolves.

## 1. Project Overview

<!-- Explain in 2-4 sentences what the project does, who uses it, and why it exists. -->

- **Name:**
- **Type:** <!-- Web app, mobile app, API, CLI, library, service, etc. -->
- **Status:** <!-- Greenfield, active development, maintenance, legacy modernization, etc. -->
- **Primary users:**
- **Business objective:**

## 2. Instruction Priority

When instructions conflict, follow this order unless the task explicitly states otherwise:

1. The current approved requirement or ticket
2. Approved acceptance criteria and Definition of Done
3. Architecture and security decisions
4. This `CLAUDE.md` file
5. Existing repository patterns and conventions
6. General framework best practices

Do not silently resolve material conflicts. Report the conflict, explain the impact, and state the safest assumption.

## 3. Scope and Boundaries

- **In scope:**
- **Out of scope:**
- **Current phase:**
- **Future features not to implement unless explicitly requested:**
- **Known constraints:**
- **External dependencies:**

## 4. Tech Stack

<!-- Be specific about versions where compatibility matters. -->

- **Language(s):**
- **Framework(s):**
- **Frontend:**
- **Backend:**
- **Database:**
- **Cache / queue:**
- **Package manager(s):**
- **Testing tools:**
- **Build tools:**
- **Deployment / hosting:**
- **Observability:**
- **Key dependencies:**

## 5. Setup and Commands

<!-- Keep these commands accurate. Remove commands that do not apply. -->

```bash
# Install dependencies

# Configure local environment

# Run database migrations

# Seed local data

# Run development server

# Run background workers

# Run all tests

# Run a single test

# Run linting

# Run formatting

# Run static analysis / type checking

# Build for production

# Run security or dependency audit
```

## 6. Project Structure

<!-- Document only important or non-obvious locations. -->

```text
/src            -
/tests          -
/docs           -
/config         -
/scripts        -
```

### File Ownership Rules

- Business logic belongs in:
- API or controller logic belongs in:
- Data access belongs in:
- Shared UI components belong in:
- Tests belong in:
- Generated files that must not be edited manually:

## 7. Architecture and Design Rules

- **Architecture style:**
- **Primary request / data flow:**
- **Key modules or bounded contexts:**
- **Shared abstractions:**
- **State-management pattern:**
- **Integration pattern:**
- **Error-handling strategy:**
- **Logging strategy:**
- **Important architecture decisions and rationale:**

### Architecture Guardrails

- Follow existing module boundaries.
- Reuse existing services, components, and abstractions before creating new ones.
- Do not duplicate business logic across controllers, APIs, jobs, or user interfaces.
- Do not introduce a new architectural pattern without explaining the need and impact.
- Record significant architecture changes in the appropriate decision document.

## 8. Code Style and Conventions

<!-- Use concrete, project-specific rules. -->

- **Naming conventions:**
- **File and folder organization:**
- **Import style and ordering:**
- **Formatting standard:**
- **Error-handling pattern:**
- **Validation pattern:**
- **Comments / docstring policy:**
- **Preferred patterns:**
- **Patterns to avoid:**
- **Maximum function or class complexity guidance:**

## 9. Requirements and Assumption Handling

Before implementation:

- Read the relevant requirement, acceptance criteria, architecture notes, and surrounding code.
- Identify the affected modules, APIs, database objects, user roles, and integrations.
- List any material assumptions.
- Do not invent business rules, permissions, status transitions, or data-retention rules.
- If a requirement is incomplete but implementation can safely proceed, use the smallest reversible assumption and clearly report it.
- Do not include future or out-of-scope features unless explicitly requested.

## 10. Mandatory Execution Workflow

For non-trivial tasks, follow this sequence:

1. **Understand** — Summarize the requirement and expected outcome.
2. **Inspect** — Review related code, tests, schemas, and documentation.
3. **Plan** — Identify files, implementation steps, risks, and test coverage.
4. **Implement** — Make the smallest focused change that satisfies the requirement.
5. **Validate** — Run relevant tests, linting, formatting, builds, and checks.
6. **Review** — Inspect the final diff for regressions, security issues, and unrelated changes.
7. **Report** — Provide the completion summary defined below.

Do not mark a task complete when required validation has not been performed. Clearly state any command that could not be run.

## 11. Database and Migration Safety

- All schema changes must use version-controlled migrations.
- Prefer backward-compatible, additive migrations.
- Do not drop, rename, truncate, or rewrite production data without explicit approval.
- Consider existing records, nullability, defaults, indexing, and rollout order.
- Provide a rollback or recovery approach for material schema changes.
- Test migrations on representative data where possible.
- Do not run destructive database commands without confirmation.

## 12. API and Integration Standards

- Preserve backward compatibility unless a breaking change is explicitly approved.
- Do not silently rename or remove fields, endpoints, events, or error codes.
- Use the project's standard response and error formats.
- Validate and authorize all external inputs.
- Define timeouts, retry behaviour, idempotency, and failure handling for external integrations.
- Do not expose internal models or sensitive fields unintentionally.
- Update API or integration documentation when contracts change.

## 13. Security and Privacy Checklist

For every relevant change, review:

- Authentication
- Authorization and role boundaries
- Input validation and output encoding
- Mass assignment or over-posting
- SQL / command / template injection
- Sensitive data exposure
- Secrets and credentials
- Session and token handling
- Rate limiting and abuse prevention
- File-upload validation
- Audit logging
- Data retention and deletion
- Dependency vulnerabilities

Never log passwords, tokens, API keys, personal data, or other secrets unless explicitly approved and safely redacted.

## 14. Performance and Reliability Checklist

Review where applicable:

- N+1 queries and missing indexes
- Unbounded queries or collections
- Pagination and filtering
- Payload size
- Caching behaviour
- Queue and background-job usage
- Retry storms and duplicate processing
- Polling frequency
- Memory and CPU impact
- Timeouts and graceful degradation
- Concurrency and race conditions

Do not optimize prematurely, but do not introduce obvious scalability or reliability risks.

## 15. Testing Standards

- **Test framework:**
- **Test locations and naming:**
- **Minimum coverage expectation:**
- **Required test types:**
- **Fixtures / factories / mocks:**
- **End-to-end test approach:**

Every material change should include tests for relevant scenarios:

- Happy path
- Validation failure
- Authorization failure
- Boundary and edge cases
- Error or dependency failure
- Regression of the reported issue
- Side effects such as events, jobs, notifications, or persistence

Do not weaken or delete existing tests merely to make a change pass without explaining the reason.

## 16. Validation Commands by Change Type

<!-- Replace placeholders with real commands. -->

| Change type | Required validation |
|---|---|
| Backend logic | Unit / feature tests, lint, static analysis |
| API change | Contract tests, authorization tests, API docs update |
| Database change | Migration up/down check, affected tests, schema docs update |
| Frontend change | Component / UI tests, lint, type check, production build |
| Dependency change | Full test suite, build, security audit |
| Configuration change | Startup check, environment documentation update |

## 17. Git and Pull Request Workflow

- **Branch naming convention:**
- **Commit message format:**
- **PR requirements:**
- **Required reviewers:**
- **Protected branches:** `main`, `production` <!-- Adjust as needed -->

Rules:

- Keep commits focused and explain why the change is needed.
- Do not commit directly to protected branches.
- Do not push, merge, force-push, or create releases unless explicitly requested.
- Do not include unrelated refactors in a feature or bug-fix change.
- Review lock-file changes and generated files before including them.

## 18. Environment and Secrets

- **Environment file location:**
- **Required environment variables:** <!-- Names only, never real values -->
- **Credential-access process:**
- **Local test credentials:**
- **Environment-specific behaviour:**

Rules:

- Never commit real secrets or credentials.
- Update the example environment file when adding a new variable.
- Do not print secret values in logs, test output, documentation, or completion summaries.

## 19. Documentation Requirements

Update the relevant documentation when changing:

| Change | Documentation to update |
|---|---|
| Public API or event contract | API documentation / OpenAPI specification |
| Database schema | Database documentation and migration notes |
| Architecture or module boundaries | Architecture document / ADR |
| Environment configuration | Example env file and setup guide |
| User-visible behaviour | Requirement, user guide, or release note |
| Build or development workflow | README and this file |

## 20. Stop and Approval Conditions

Stop and request explicit approval before:

- Adding or replacing a major dependency
- Introducing a new framework, datastore, queue, or external service
- Making a breaking API or event-contract change
- Performing a destructive or irreversible database migration
- Changing authentication, authorization, encryption, or security boundaries
- Accessing, modifying, or deleting production data
- Changing infrastructure, deployment, billing, or secrets
- Expanding the approved scope

When blocked, report the decision needed, available options, and likely impact.

## 21. Things Claude Should Not Do

- Do not modify generated, vendor, build, or third-party files unless explicitly required.
- Do not install new dependencies without approval.
- Do not run destructive database or filesystem commands without confirmation.
- Do not push directly to remote repositories.
- Do not silently change public APIs, schemas, permissions, or business rules.
- Do not expose or fabricate secrets, credentials, or production data.
- Do not implement placeholder behaviour and present it as complete.
- Do not refactor unrelated areas while completing a scoped task.
- Do not bypass failing tests, security checks, or validation rules.

## 22. Known Gotchas and Risks

<!-- Add project-specific landmines, legacy constraints, fragile integrations, and common mistakes. -->

-

## 23. Definition of Done

A task is complete only when applicable items are satisfied:

- [ ] Requirement and acceptance criteria are addressed.
- [ ] Scope is limited to the requested change.
- [ ] Code follows repository architecture and conventions.
- [ ] Authorization and validation are enforced.
- [ ] Tests are added or updated and pass.
- [ ] Linting, formatting, type checking, and build checks pass.
- [ ] Database migrations are safe and verified.
- [ ] Security and performance impacts are reviewed.
- [ ] Documentation is updated.
- [ ] No secrets, debug code, or unrelated changes are included.
- [ ] Final diff has been reviewed.

## 24. Completion Summary Format

At the end of a task, report:

```markdown
## Completed
- Summary of the implemented outcome

## Files Changed
- `path/to/file` — reason for the change

## Validation
- `command` — passed / failed / not run

## Assumptions
- Any assumptions made

## Risks or Follow-up
- Remaining risks, limitations, or recommended next steps
```

## 25. Useful Context and Links

- **Business requirements:**
- **Architecture docs:**
- **Design docs:**
- **API docs:**
- **Database docs:**
- **Security docs:**
- **Related repositories:**
- **Issue tracker:**
- **Runbooks / deployment docs:**
