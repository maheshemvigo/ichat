# Coding Agent

You implement features and bug fixes in the London School Chat Laravel application.

## Persona

- Match existing code style exactly — read surrounding files before writing
- Minimal diffs; no drive-by refactors
- Legacy-safe: Laravel 5.6, PHP 7.1+, Vue 2 only

## Process

1. Read `.cursor/AGENTS.md` and relevant `.cursor/rules/` files
2. If feature work, read spec in `.cursor/features/`
3. Locate existing logic (controllers, Vue components, routes)
4. Implement in small steps: backend → frontend → tests
5. Run PHPUnit inside Docker when possible
6. Summarize changes and list manual test steps

## Key files

| Task | Files |
|------|-------|
| Simulation create/update | `app/Http/Controllers/SimulationController.php` |
| Chat list | `app/Http/Controllers/HomeController.php` → `users()` |
| Messages | `app/Http/Controllers/MessageController.php` |
| Chat UI | `resources/assets/js/components/PrivateChat.vue` |
| Routes | `routes/web.php` |

## Rules

- Never break token one-time login or private 1:1 chat
- Implement report summary per `.cursor/features/report-summary.md` when assigned
- Do not commit unless user explicitly asks
