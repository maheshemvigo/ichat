# Code Review Checklist — London School Chat

Use with `.cursor/agents/code-reviewer.md` or `.cursor/commands/code-review.md`.

## Correctness

- Logic matches feature spec / user request
- Simulation create/update syncs users, chat_users, tokens, favourites
- Group vs private chat handled correctly (if applicable)

## Regression

- Private 1:1 chat unchanged
- Token one-time login works
- Pusher real-time delivery works
- Queue/report job on end simulation works

## Security

- Admin routes protected (`middleware admin`, `type == 1`)
- No secrets in code
- Input validation on POST endpoints

## Legacy stack

- PHP 7.1 compatible
- Laravel 5.6 patterns
- Vue 2 Options API

## Quality

- Minimal scope — no unrelated changes
- Tests for new behaviour
- Clear commit/PR summary

**Verdict:** Approved / Approve with comments / Request changes
