# London School Chat — Cursor Agents

**Read first:** `simulation.londonbusinessschool/CURSOR.md` (master project context — business + workflow + technical).

Agent personas for AI-assisted work in this repository. Invoke by referencing the agent file in chat (e.g. `@.cursor/agents/coding-agent.md`).

## Project

London School Chat — Laravel 5.6 simulation platform with private chat, Pusher real-time, Vue 2 frontend, MySQL 5.7.

## Available Agents

| Agent | File | Use when |
|-------|------|----------|
| Coding Agent | `.cursor/agents/coding-agent.md` | Implement features, bug fixes, refactors |
| Test Writer | `.cursor/agents/test-writer.md` | Write PHPUnit unit/feature tests |
| Debug Agent | `.cursor/agents/debug-agent.md` | Trace errors, fix bugs, read logs |
| Code Reviewer | `.cursor/agents/code-reviewer.md` | Review changes before commit/PR |
| Doc Writer | `.cursor/agents/doc-writer.md` | Technical docs, API docs, comments |
| Feature Agent | `.cursor/agents/feature-agent.md` | Plan/implement new features from spec |

## Feature Specs

| Feature | Spec file |
|---------|-----------|
| Report Summary | `.cursor/features/report-summary.md` |

## SDLC Pipeline Plugin (manual install)

Git plugin install may not appear in Cursor Settings. This repo includes a manual copy:

| Path | Purpose |
|------|---------|
| `.cursor/plugins/sdlc-pipeline/` | Full plugin (agents + pipeline command) |
| `.cursor/commands/pipeline.md` | Run `/pipeline` workflow |
| `.cursor/plugins/sdlc-pipeline/INSTALL.md` | Install/update instructions |

**Use pipeline for report summary:**

```text
@.cursor/commands/pipeline.md
@.cursor/features/report-summary.md

Implement or verify simulation report summary feature
```

If `/pipeline` stops (missing superpowers plugin), use project agents below instead.

## Cursor Configuration

| Path | Purpose |
|------|---------|
| `.cursor/rules/` | Persistent coding rules (`.mdc`) |
| `.cursor/hooks.json` | Agent event hooks |
| `.cursor/hooks/` | Hook shell scripts |
| `.cursor/agents/` | Agent personas |
| `.cursor/features/` | Feature specifications |
| `.cursor/commands/` | Reusable prompt templates |
| `.cursor/plugins/sdlc-pipeline/` | SDLC pipeline plugin (manual) |

## Legacy Stack (always respect)

- Laravel `5.6.*`, PHP `^7.1.3`
- Vue `2.5.x`, Laravel Mix `2.x`
- MySQL `5.7`
- No PHP 8 syntax, no Vue 3, no Vite

## Docker Commands

```bash
docker compose up -d
docker compose exec app php artisan test
docker compose exec app php artisan migrate
docker compose logs queue -f
```

## Key Code Locations

| Area | Path |
|------|------|
| Simulation CRUD | `app/Http/Controllers/SimulationController.php` |
| Chat messages | `app/Http/Controllers/MessageController.php` |
| Chat user list | `app/Http/Controllers/HomeController.php` |
| Chat UI | `resources/assets/js/components/PrivateChat.vue` |
| Routes | `routes/web.php` |
| Report queue | `app/Jobs/GenerateSimulationHistoryReportJob.php` |
