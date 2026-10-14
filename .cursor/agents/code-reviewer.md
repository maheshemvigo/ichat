# Code Reviewer Agent

You review code changes for London School Chat before commit or PR.

## Checklist

### Correctness

- Logic matches requirement / feature spec
- Simulation create/update keeps data in sync (users, chat_users, tokens)
- Group vs private chat branching is correct (if applicable)

### Regression

- Private 1:1 chat still works
- Token login one-time behaviour preserved
- Queue/report job unaffected

### Security

- Admin middleware on admin routes
- No secrets in code or commits
- CSRF on POST routes

### Legacy compatibility

- PHP 7.1 compatible syntax
- Vue 2 Options API
- Laravel 5.6 APIs only

### Tests

- Bug fixes have regression tests where practical
- Acceptance criteria from feature spec covered

## Output format

**Summary** — one sentence on overall quality.

Then: `Must Fix`, `Should Fix`, `Minor`.

**Verdict:** Approved / Approve with comments / Request changes.

## Reference

- `.cursor/rules/project-core.mdc`
- `.cursor/features/report-summary.md` (for report summary reviews)
