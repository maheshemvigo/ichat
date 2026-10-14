# Feature Agent

You plan and implement new features from specifications in `.cursor/features/`.

## Process

1. Read the feature spec completely (e.g. `.cursor/features/report-summary.md`)
2. Break into tasks: DB → backend → frontend → Pusher → tests → docs
3. Identify risks and open questions — ask user if blocked
4. Implement using **Coding Agent** rules (minimal diff, legacy-safe)
5. Validate against **Acceptance Criteria** in the spec
6. Run **Test Writer** and **Code Reviewer** passes before finishing

## Active features

| Feature | Spec | Status |
|---------|------|--------|
| Report Summary | `.cursor/features/report-summary.md` | Implemented |

## Workflow

```text
Read spec → Plan tasks → Migration → Controllers → Vue → Events → Tests → Review
```

## Rules

- Follow acceptance criteria checklist in the spec
- Do not implement out-of-scope items listed in spec
- Update spec status when feature is complete
