# Feature: Simulation Summary Report

**Status:** Spec preserved — application code may be removed/re-added per implementation phase  
**Scope:** End Simulation → queue → OpenAI (or fallback) → S3 → per-row download on Simulation History  
**Out of scope:** Top **Download** button on Simulation History (legacy checkbox raw chat export) — unchanged

---

## Business goal

When a super admin ends a simulation, the system generates a structured summary report of all chat threads, stores it privately on S3, and shows a **Download Summary Report** button on each Simulation History row when ready.

---

## Overview

| Step | What happens |
|------|----------------|
| 1 | Admin ends simulation |
| 2 | Queue job generates report (OpenAI or raw-chat fallback) |
| 3 | Report uploaded to **private S3** |
| 4 | S3 object key saved in `simulations.history_summary_url` |
| 5 | Simulation History row shows **Download Summary Report** |
| 6 | Click → Laravel issues **pre-signed S3 URL** → file downloads |

---

## Architecture

```mermaid
flowchart TD
    A[Admin: End Simulation] --> B[GET /leaveroom/{id}]
    B --> C[status=1, history_summary_url=null]
    B --> D[Dispatch GenerateSimulationHistoryReportJob]
    B --> E[Flash pending_report_id + redirect Simulation History]

    D --> F[(jobs table)]
    F --> G[lbs-queue: php artisan queue:work]

    G --> H{LLM_API_KEY set?}
    H -->|Yes| I[LlmStructuredReportService]
    H -->|No| J[SimulationChatHistoryReportService]

    I --> K[OpenAI API - chunk + merge if long chat]
    K --> L[structured_report_document.blade.php]
    J --> M[chat_history_report_document.blade.php]

    L --> N[SimulationReportStorageService → S3]
    M --> N
    N --> O[Update history_summary_url]

    P[Simulation History page] --> Q{URL in DB?}
    Q -->|Yes| R[Download Summary Report button]
    Q -->|No| S[Summary report processing…]

    R --> T[GET /simulation_history/report/{id}]
    T --> U[temporaryDownloadUrl - pre-signed S3]
    U --> V[Browser downloads .doc file]
```

---

## 1. Trigger — End Simulation

**UI:** Admin clicks **End Simulation** in the chat (`PrivateChat.vue`).

**API:** `GET /leaveroom/{simulationId}` → `SimulationController@leaveroom`

**Actions:**

1. Set `simulations.status = 1` (ended — appears on Simulation History)
2. Close tokens / user sessions
3. Clear `simulations.history_summary_url = null` (report not ready)
4. Dispatch `GenerateSimulationHistoryReportJob`
5. Flash session `pending_report_id` (for UI polling without relying on Vue build)
6. Broadcast `ExitSimulation` (Pusher)
7. Super admin redirected to `/simulation_history` (optionally `?pending_report={id}`)

**Controller snippet (`leaveroom`):**

```php
Simulation::where('id', $simulationid)->update([
    'status' => 1,
    'history_summary_url' => null,
]);

GenerateSimulationHistoryReportJob::dispatch($simulationid);

Session::flash('success', 'Simulation end Successfully. Chat history report is being generated.');
Session::flash('pending_report_id', $simulationid);
```

**Vue redirect (`PrivateChat.vue` → `leaveusers`):**

```javascript
window.location = this.url + "/simulation_history/";
// Optional: ?pending_report= + this.simulationid
```

---

## 2. Queue

### Why a queue?

Report generation (OpenAI + S3) can take **5–15+ minutes** for large simulations. It runs in the background so the admin is not blocked.

### Requirements

| Requirement | Value |
|-------------|--------|
| `QUEUE_DRIVER` | `database` |
| `jobs` table | Must exist |
| Queue worker | `lbs-queue` container: `php artisan queue:work database --timeout=900` |

### Job

**Create file:** `app/Jobs/GenerateSimulationHistoryReportJob.php`

| Setting | Value |
|---------|--------|
| Retries | 2 |
| Timeout | 900 seconds (large chats) |

**Logic:**

```php
// if (LLM enabled + api_key) → LlmStructuredReportService → storeStructuredReport
// else → SimulationChatHistoryReportService → storeChatHistoryReport
// update simulations.history_summary_url = $s3Key
```

**On failure:** logged to `storage/logs/laravel.log` and `failed_jobs` (after retries)

**Note:** With `QUEUE_DRIVER=database`, Pusher broadcast events also use the `jobs` table. The queue worker must be running for real-time chat.

---

## 3. Collect chat data

**Create file:** `app/Services/SimulationHistoryCollector.php`

1. Find all active `chat_users` for the simulation (`status = 0`)
2. Skip empty threads (no messages)
3. Load `messages` for each thread (admin ↔ participant, both directions)
4. Build plain-text transcript lines:

```text
[2026-06-09 10:15:00] John: Hello team
[2026-06-09 10:16:00] Mary: [Shared document: plan.pdf]
```

Uses `simulations.created_by` as admin user ID (works in queue — no browser session).

**Large simulations:** cap transcript lines sent to LLM (e.g. `simulation_max_llm_lines = 500` in `config/llm_report.php`).

---

## 4. Generate report content

This step takes **all simulation chat messages** (from step 3), combines them with your **report prompt**, sends that to the LLM, and saves the result for the report file (step 5).

### Flow

```text
1. Retrieve full simulation chat transcript (all threads, all messages)
        ↓
2. Load report prompt from config/llm_report.php
        ↓
3. Send to LLM:  [system prompt] + [your summary instructions] + [chat transcript]
        ↓
4. Receive LLM summary text / structured JSON
        ↓
5. Pass result to report template → save as .doc file (step 5 — S3)
```

### Where to set the prompt

**File:** `config/llm_report.php`

| Key | Purpose |
|-----|---------|
| `system_prompt` | Main instruction — **put your summary rules here** |
| `chunk_prompt` | Optional — only if chat is very long and split into chunks |
| `merge_prompt` | Optional — only when merging chunk summaries |

**Service that uses it:** `app/Services/LlmStructuredReportService.php`

```php
// Simplified logic:
$transcript = $collector->collectForSimulation($simulationId);  // all chat content
$userMessage = config('llm_report.system_prompt') . "\n\nSimulation chat:\n" . $transcript;
$response = $this->callLlm($userMessage);  // OpenAI API
// → render + save file in SimulationReportStorageService
```

### Your report summary prompt

Set this in `config/llm_report.php` under `system_prompt`:

```php
'system_prompt' => <<<'PROMPT'
Create a report summary from this simulation chat.

Include:
- Overall chat style and tone
- Each participant's attitude and communication style
- Approved way to answer (how responses should be given in this simulation context)
- Confidence level shown by each participant

Use only information from the transcript. Be factual and professional.
PROMPT,
```

Or in `.env` (if wired in config):

```env
LLM_REPORT_SYSTEM_PROMPT="Create a report summary from this simulation chat. Include overall chat style, each user's attitude, approved way to answer, and confidence level."
```

After changing the prompt:

```bash
docker compose exec app php artisan config:clear
```

### Path A — AI summary (when `LLM_API_KEY` is set)

**Files:**
- `app/Services/LlmStructuredReportService.php` — sends prompt + chat to LLM
- `config/llm_report.php` — **prompt content lives here**

**LLM request shape:**

| Part | Content |
|------|---------|
| System | Your `system_prompt` (summary rules above) |
| User | Full simulation chat transcript from `SimulationHistoryCollector` |

**Long chats (optional chunking):**

| Limit | Default | Config key |
|-------|---------|------------|
| Messages per chunk | 40 | `LLM_CHUNK_MESSAGE_LIMIT` |
| Characters per chunk | 12000 | `LLM_CHUNK_CHAR_LIMIT` |
| Max lines to LLM | 500 | `LLM_SIMULATION_MAX_LLM_LINES` |

- **Short chat** → one LLM call: prompt + full transcript → save
- **Long chat** → chunk transcript → `chunk_prompt` per chunk → `merge_prompt` → save

**Output template:** `resources/views/simulation/reports/structured_report_document.blade.php`

Merge all threads into **one** simulation report (not one section per empty `chat_users` row).

### Path B — Raw chat fallback (no API key)

**Create file:** `app/Services/SimulationChatHistoryReportService.php`  
**Template:** `resources/views/simulation/reports/chat_history_report_document.blade.php`

Used when `LLM_REPORT_ENABLED=false` or `LLM_API_KEY` is empty — saves raw chat only, no LLM summary.

---

## 5. Upload to S3

**Create file:** `app/Services/SimulationReportStorageService.php`

| Method | When |
|--------|------|
| `storeStructuredReport()` | AI summary path |
| `storeChatHistoryReport()` | Raw chat fallback |
| `temporaryDownloadUrl()` | Pre-signed download URL |

**Process:**

1. Render Blade template → HTML (Word-compatible `.doc`)
2. Upload to S3 as **private**
3. Return S3 object key (stored in DB)

**S3 path example:**

```text
simulation-history-reports/258/simulation-258-summary-20260609-031649.doc
```

**DB column:** `simulations.history_summary_url` stores the **S3 key**, not a public URL.

**Pre-signed download:** include `ResponseContentDisposition: attachment` and `application/octet-stream` so the file downloads instead of opening inline in the browser.

---

## 6. Database

**Migration:** `database/migrations/2026_06_03_000001_add_history_summary_url_to_simulations_table.php`

```sql
ALTER TABLE `simulations`
  ADD COLUMN `history_summary_url` VARCHAR(512) NULL DEFAULT NULL AFTER `status`;
```

Also ensure `jobs` and `failed_jobs` tables exist when using `QUEUE_DRIVER=database`.

| Column / table | Purpose |
|----------------|---------|
| `history_summary_url` | S3 object key for summary report |
| `jobs` | Database queue |
| `failed_jobs` | Failed queue jobs |

---

## 7. Routes

```php
Route::get('/simulation_history/report/{id}', 'SimulationController@simulation_history_report_download');
Route::get('/simulation_history/report-status/{id}', 'SimulationController@simulation_history_report_status');
```

**Report status (JSON polling):**

```php
return response()->json([
    'ready' => $simulation && !empty($simulation->history_summary_url),
    'download_url' => /* url('/simulation_history/report/' . $id) when ready */,
]);
```

---

## 8. Simulation History UI

**Page:** `resources/views/simulation/simulation_history.blade.php`  
**Styles:** `public/userassets/css/style.css` (`.historyAccord` block)

### Row layout (required — do not use flex on the row header)

The summary report adds a **Download Summary Report** link on the right of each row. That link is **absolutely positioned** inside `.history-row-header`. The simulation title, checkbox, and accordion toggle must keep the **original pre-feature HTML structure**.

**Do not:**

- Move the row checkbox **outside** `<h2>` into a separate flex column
- Apply `display: flex` + `min-width: 0` on the title column (`.history-row-title`)
- Replace the nested `<h2>` → checkbox + `<button>` layout with a flat flex row

**Why:** A flex refactor (`display: flex` on `.history-row-header`, checkbox as `flex: 0 0 auto`, title as `flex: 1 1 auto; min-width: 0`) **collapses simulation names to zero width**. The page still loads data (e.g. 64 rows from the controller) but the UI shows only the checkbox and “Summary report processing…” — titles and chat-user lists appear blank.

**Correct structure (preserve this pattern):**

```html
<div class="card-header history-row-header" id="headingOne{{ $key }}">
    <h2 class="mb-0">
        <ul class="list-unstyled checklist checklist2" style="position: relative; z-index: 111;">
            <!-- row checkbox -->
        </ul>
        <button class="btn collapsed history-row-toggle" ...>
            <ul class="list-unstyled checklist checklist2">
                <!-- simulation name, admin, date -->
            </ul>
            <span class="ang">...</span>
        </button>
    </h2>
    <span class="history-report-link"><!-- download or processing --></span>
</div>
```

**Required CSS (minimal):**

```css
.historyAccord .history-row-header {
  position: relative;
  min-height: 54px;
}

.historyAccord .history-row-toggle {
  width: 100%;
  text-align: left;
  padding-right: 210px; /* room for report link on the right */
}

.historyAccord .history-report-link {
  position: absolute;
  top: 24px;
  right: 68px;
  z-index: 112;
  white-space: nowrap;
}
```

After Blade or CSS changes: `docker compose exec app php artisan view:clear` and hard-refresh the browser.

### Per simulation row

| Condition | UI |
|-----------|-----|
| `history_summary_url` set | Green **Download Summary Report** button (top-right of row) |
| Empty URL **and** row is the pending simulation (`pending_report_id` / `?pending_report=` / `data-poll-report="1"`) | Gray **Summary report processing…** (polled until ready) |
| Empty URL **and** not pending (e.g. ended before feature) | No report link on that row |

### Polling (auto-update button)

After End Simulation, poll `/simulation_history/report-status/{id}` every 5 seconds (up to ~15 minutes for large reports).

**Start polling when any of:**

- URL query `?pending_report={id}`
- Session flash `pending_report_id` (set in `leaveroom`)
- Row marked `data-poll-report="1"`

When `ready=true`, replace “processing…” with the download link without a full page refresh.

### Top buttons (unchanged)

| Button | Action |
|--------|--------|
| **Download** | Raw chat export — `POST /simulation_history_download` |
| **Delete** | Remove selected simulations from history |

---

## 9. Environment variables

```env
QUEUE_DRIVER=database

LLM_REPORT_ENABLED=true
LLM_API_KEY=sk-your-openai-key
LLM_API_URL=https://api.openai.com/v1/chat/completions
LLM_MODEL=gpt-4o-mini
LLM_CHUNK_MESSAGE_LIMIT=40
LLM_CHUNK_CHAR_LIMIT=12000
LLM_TIMEOUT=120
LLM_REPORT_S3_PREFIX=simulation-history-reports
LLM_REPORT_SIGNED_URL_MINUTES=60

AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=ichat-lbs
```

After changing `.env`:

```bash
docker compose restart app queue
docker compose exec app php artisan config:clear
```

---

## 10. Implementation order

1. Migration + `jobs` / `failed_jobs` tables
2. `config/llm_report.php` + `.env` variables
3. `SimulationHistoryCollector`
4. `SimulationChatHistoryReportService` (fallback — test without OpenAI first)
5. `SimulationReportStorageService`
6. `GenerateSimulationHistoryReportJob`
7. Controller + routes (`leaveroom`, report download, report status)
8. `LlmStructuredReportService` + `structured_report_document.blade.php`
9. Simulation History UI + polling JS
10. Docker `queue` service + `PrivateChat.vue` redirect
11. Acceptance testing (see §11)

---

## 11. Acceptance criteria

- [ ] End Simulation sets `status=1` and clears `history_summary_url`
- [ ] Job dispatched to database queue on `leaveroom`
- [ ] Admin redirected to Simulation History after end
- [ ] Row shows “Summary report processing…” **only on the pending row** while that job runs
- [ ] Polling updates button to **Download Summary Report** without manual refresh
- [ ] `history_summary_url` populated with S3 key after job completes
- [ ] Download uses pre-signed S3 URL with attachment disposition
- [ ] Large simulations complete within job timeout (900s) or fail gracefully to `failed_jobs`
- [ ] Without OpenAI key: raw chat fallback report still generated and downloadable
- [ ] Simulation History row layout preserves checkbox-inside-`<h2>` structure (§8 — no flex collapse)
- [ ] Legacy top **Download** (checkbox export) still works unchanged

---

## 12. Key files

| File | Role |
|------|------|
| `app/Jobs/GenerateSimulationHistoryReportJob.php` | Queue orchestration |
| `app/Services/SimulationHistoryCollector.php` | Load messages from DB |
| `app/Services/LlmStructuredReportService.php` | OpenAI + chunking |
| `app/Services/SimulationChatHistoryReportService.php` | Raw chat fallback |
| `app/Services/SimulationReportStorageService.php` | Render + S3 + pre-signed URL |
| `config/llm_report.php` | Prompts, limits, S3 prefix |
| `resources/views/simulation/reports/structured_report_document.blade.php` | AI report HTML |
| `resources/views/simulation/reports/chat_history_report_document.blade.php` | Raw chat report HTML |
| `resources/views/simulation/simulation_history.blade.php` | History UI + polling |
| `public/userassets/css/style.css` | `.historyAccord` row layout (see §8 — no flex collapse) |
| `app/Http/Controllers/SimulationController.php` | `leaveroom`, download, report-status |
| `routes/web.php` | Routes |
| `docker-compose.yml` | `queue` service |
| `resources/assets/js/components/PrivateChat.vue` | End Simulation redirect |

---

## 13. Troubleshooting

### History list empty — only checkbox and “Summary report processing…” visible

**Symptom:** Simulation History page loads but row titles and chat-user checkboxes are missing; you may see one checkbox and “Summary report processing…” with a large blank area. Backend still returns completed simulations (e.g. `Simulation::where('status', 1)->count()` is correct).

**Root cause:** The report feature restructured each history row with a **flex layout** — checkbox moved outside `<h2>`, titles placed in a shrinking flex column (`flex: 1 1 auto; min-width: 0`). That collapsed simulation names to zero width while the absolutely positioned report link and checkbox remained visible.

**Fix:**

1. Restore the **original row HTML** — checkbox inside `<h2>`, title inside the collapse `<button>` (see §8).
2. Remove flex layout from `.history-row-header` / `.history-row-title` / `.history-row-checkbox` in `style.css`; keep only `position: relative` on the header and absolute positioning for `.history-report-link`.
3. Show “Summary report processing…” **only** for the pending simulation (just ended), not every row with `history_summary_url = NULL`.
4. Clear compiled views: `docker compose exec app php artisan view:clear`
5. Hard-refresh the browser (`Ctrl+Shift+R`).

**Verify backend is fine (optional):**

```bash
docker compose exec app php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo App\Simulation::where('status', 1)->count() . ' completed simulations';
"
```

If the count is > 0 but the UI is blank, the issue is layout/CSS — not the controller or database query.

### Chat 500 after enabling queue

**Cause:** `jobs` table missing.  
**Fix:** Create `jobs` / `failed_jobs` tables.

### “Summary report processing…” never changes

| Check | Action |
|-------|--------|
| Queue worker running | `docker compose ps queue` |
| Job errors | `docker compose logs queue -f` |
| `history_summary_url` in DB | Query `simulations` |
| OpenAI / AWS keys | Verify `.env`, restart `app` + `queue` |
| Polling JS | Session flash `pending_report_id` or `data-poll-report` |

### Report opens in browser instead of downloading

Pre-signed URL must include attachment headers in `SimulationReportStorageService::temporaryDownloadUrl()`.

### Empty `jobs` table after processing

**Normal.** Laravel deletes job rows after success. Check `history_summary_url` and S3 instead.

### Old simulations show no report link

Simulations ended before the feature was enabled have `history_summary_url = NULL`. They do **not** show “Summary report processing…” (only the simulation just ended does). Only new End Simulation events generate reports. To backfill, dispatch `GenerateSimulationHistoryReportJob` manually for a given simulation ID.

---

## 14. Quick verification

1. `QUEUE_DRIVER=database` in `.env`
2. `jobs` + `failed_jobs` tables exist
3. `lbs-queue` container running
4. `LLM_API_KEY` and AWS credentials set
5. End a test simulation
6. `docker compose logs queue -f` → `Processed: GenerateSimulationHistoryReportJob`
7. DB: `history_summary_url` populated
8. Simulation History → **Download Summary Report** → file downloads

---

## 15. Summary vs History Download

| | **Summary Report** (per row) | **History Download** (top button) |
|--|------------------------------|-----------------------------------|
| **Trigger** | Automatic after End Simulation | Manual — select checkboxes + Download |
| **Content** | AI structured report (or raw chat fallback) | Raw messages only |
| **Generation** | Server + queue + S3 | Browser JavaScript on demand |
| **Storage** | S3 private | None |

Both produce HTML-as-Word (`.doc`), not binary `.docx`.

**Technical pattern:** `leaveroom` → queue job → collect messages → generate report → S3 → `history_summary_url` → history page download via pre-signed URL.
