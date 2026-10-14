# SDLC Pipeline — Manual Install (Cursor)

Git plugin install is not available in all Cursor versions. This project includes a **manual copy** of the plugin.

**Source:** https://github.com/nithinemvigo/sdlc-pipeline  
**Version:** 2.1.0

## Installed locations

| Path | Contents |
|------|----------|
| `.cursor/plugins/sdlc-pipeline/agents/` | 14 SDLC specialist agents |
| `.cursor/plugins/sdlc-pipeline/commands/pipeline.md` | Full pipeline orchestrator |
| `.cursor/commands/pipeline.md` | Shortcut for Cursor command picker |

## How to use in Cursor

### Important — workspace root

Slash commands must be at **workspace root**:

```text
London School Chat/.cursor/commands/pipeline.md   ← Cursor reads this
```

NOT only inside `simulation.londonbusinessschool/.cursor/commands/`.

### Option A — Pipeline slash command

1. Reload Cursor (`Ctrl+Shift+P` → Reload Window)
2. Open **Agent chat** (not command palette)
3. Type **`/`** in the message box → select **pipeline**

If not listed, use `@` mention:

```text
@.cursor/commands/pipeline.md

Implement group chat per simulation.londonbusinessschool/.cursor/features/group-chat.md
```

### Option B — Individual SDLC agents

```text
@.cursor/plugins/sdlc-pipeline/agents/planner.md
@.cursor/plugins/sdlc-pipeline/agents/implementer.md
@.cursor/features/group-chat.md

Plan and implement group chat for this Laravel project.
```

## Update plugin later

```bash
git clone --depth 1 https://github.com/nithinemvigo/sdlc-pipeline.git /tmp/sdlc-pipeline
cp -r /tmp/sdlc-pipeline/agents/* .cursor/plugins/sdlc-pipeline/agents/
cp /tmp/sdlc-pipeline/commands/pipeline.md .cursor/plugins/sdlc-pipeline/commands/
cp /tmp/sdlc-pipeline/commands/pipeline.md .cursor/commands/pipeline.md
```

Then reload Cursor window.

## Note on dependencies

Full `/pipeline` expects **superpowers** skills (Claude Code plugin). In Cursor, use project agents (`.cursor/agents/`) if pipeline halts on missing superpowers.

Recommended for this project without superpowers:

```text
@.cursor/features/group-chat.md
@.cursor/agents/feature-agent.md
@.cursor/agents/coding-agent.md
@.cursor/agents/test-writer.md
```
