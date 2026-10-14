# AI Capability Assessment Document

## Project
London School Chat (`simulation.londonbusinessschool`)

## Purpose
This document states which activities can be done with AI in this project, which are partially possible, and which still require human control, considering the legacy stack limitations.

## Technology Constraints
- Laravel `5.6.*`
- PHP `^7.1.3` (legacy compatibility required)
- Vue `2.5.x`
- Laravel Mix `2.x`
- `node-sass` `4.x` and old Node toolchain constraints
- MySQL `5.7`

These constraints do not block AI usage, but they require version-safe coding and careful validation.

---

## Capability Classification

### A) Fully possible with AI (`YES`)
- `.md` file creation
- Planning of new feature development
- Design architecture and get suggestions
- Design database structure
- Design API
- Explain existing code and codebase
- Identify risk
- Code generation
- Code completion
- Write native DB queries
- Regex generation
- Debug code
- Bug fix
- Mock data for testing
- Code review
- Coding standards check
- Duplicate code detection
- PR summary
- Review comments
- Prepare API documentation
- Prepare technical documentation
- Create Architecture Decision Records
- Code commit (when explicitly requested)
- Utilize AI Skills
- Utilize AI Rules
- Utilize AI Agents
- Utilize AI Hooks
- Utilize AI Commands

### B) Possible with AI + human/environment support (`PARTIAL`)
- Develop UI using UX, MCP
- Code refactoring (safe in small steps; needs regression validation)
- Code conversion (limited by legacy versions)
- Unit testing + acceptance criteria validation (business acceptance is human)
- Integration testing (needs real services/credentials)
- Test coverage analysis (needs coverage instrumentation and baseline)
- Performance review (needs runtime profiling/load data)
- Security and vulnerability review (AI review + human security sign-off)
- Architecture compliance (requires defined architecture rules/checklist)
- Dead code detection (requires runtime/usage confirmation)
- Dependency review (legacy upgrade risk)
- Load testing (needs target environment and traffic model)
- Deployment (AI can assist; human access/approval required)
- Release automation (AI can implement; human governance required)
- Utilize AI Plugin/MCP (depends on configured servers and permissions)

### C) Not AI-only (`NO`)
- Final production release approval
- Compliance/legal certification
- Business sign-off of acceptance outcomes
- Real multi-user classroom validation without human participants

---

## Cursor / Claude Code Point of View

### Utilize AI Skills
Use skills for repeatable workflows: review, security checks, docs, and automation templates.

### Utilize AI Rules
Strongly recommended for this legacy repo. Rules should enforce:
- PHP 7.1-compatible syntax
- Vue 2-compatible patterns
- MySQL 5.7-compatible queries
- Mandatory tests for bug fixes/features

### Utilize AI Agents
Use parallel agents for:
- implementation
- code review
- test writing
- documentation

### Utilize AI Plugin / MCP
Useful for UI/flow validation and external system integrations when configured. Not a full replacement for human UAT.

### Utilize AI Hooks
Recommended to auto-run checks before/after agent edits:
- lint
- test suites
- compatibility checks

### Utilize AI Commands
Create reusable command templates:
- feature implementation checklist
- bug fix + regression test flow
- PR summary and risk checklist

---

## Recommended Operating Model
1. AI prepares feature plan, architecture, API, DB design.
2. AI implements in small, reviewable changes.
3. AI adds unit/feature/integration test scaffolding.
4. AI runs review passes (quality, security, duplication, dead code suspicion).
5. Human validates acceptance criteria and business behavior.
6. AI prepares PR summary and technical documentation.
7. Human approves deployment and release.

---

## Final Conclusion
With the current legacy stack, AI can handle most engineering tasks effectively (planning, coding, debugging, testing support, and documentation).  
Critical outcomes still requiring humans are acceptance sign-off, production deployment authority, compliance decisions, and final release governance.

