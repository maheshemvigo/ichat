# Requirement: Simulation Group Chat

| Field | Value |
|-------|-------|
| **Feature ID** | LBS-CHAT-001 |
| **Status** | Draft — pending stakeholder approval |
| **Priority** | High |
| **Requested by** | LBS / project team |
| **Related technical spec** | *(to create in `.cursor/features/` when approved)* |
| **Related plan** | `docs/plans/group-chat.md` *(to create after approval of this requirement)* |
| **Master context** | `CURSOR.md` |

---

## 1. Business Problem

Today, London School Chat supports **private 1:1 messaging only** between the facilitator (admin acting as roles such as CEO, CFO) and each participant terminal. There is **no shared room** where all simulation members can see and respond to the same conversation.

In classroom simulations, facilitators often need to:

- Make announcements visible to **everyone** at once
- Allow participants to **see each other's contributions** in a common space
- Run open discussion alongside private role-play threads

The admin **broadcast** feature sends duplicate messages into separate private threads — it does not create a true group conversation where all members interact in one timeline.

---

## 2. Business Objective

Add **one group chat room per simulation** so that all members of that simulation (facilitator + all terminal participants) can read and send messages in a shared chat, **in addition to** existing private chats.

---

## 3. Users Affected

| User | Impact |
|------|--------|
| **Super admin / sub-admin (facilitator)** | Sees "Group Chat" in chat list; can send/receive group messages |
| **Simulation participants (terminals)** | Same — access via existing token login; no new link |
| **Super admin (history/reports)** | Group messages should appear in session history / reports where applicable |

**Not affected:** Users outside the simulation; no new login or token process.

---

## 4. User Stories

### US-1 — Automatic group room on simulation create

**As a** facilitator  
**I want** a group chat room to be created automatically when I create a simulation  
**So that** I do not need separate setup for a shared discussion space.

**Acceptance hints:**
- One group room per simulation
- Created on simulation create and recreated correctly on simulation update

---

### US-2 — Facilitator uses group chat

**As a** facilitator  
**I want** to open "Group Chat" from my chat list  
**So that** I can send messages visible to all participants in the simulation.

**Acceptance hints:**
- Group Chat appears in chat list (preferably at top)
- Messages deliver in real time
- Sender name is visible

---

### US-3 — Participant uses group chat

**As a** simulation participant (terminal user)  
**I want** to see and use Group Chat after logging in with my existing token  
**So that** I can join the shared discussion without a new link or PIN.

**Acceptance hints:**
- No separate token for group chat
- Can read and send messages
- Can see other participants' messages (with names)

---

### US-4 — Private chat unchanged

**As a** facilitator or participant  
**I want** existing private 1:1 chats to work exactly as today  
**So that** role-play per terminal/role is not disrupted.

**Acceptance hints:**
- Private threads still work
- Token one-time login unchanged
- Admin broadcast to selected private threads still works

---

### US-5 — Group chat in session history

**As a** super admin  
**I want** group chat messages included when reviewing or exporting simulation history  
**So that** the full session record is complete for debrief and reporting.

**Acceptance hints:**
- Group messages appear in history export / AI summary pipeline where applicable

---

## 5. Business Rules

| # | Rule |
|---|------|
| BR-1 | Exactly **one** group chat per simulation |
| BR-2 | Group chat is created on simulation **create** and **update** |
| BR-3 | Group members = facilitator + all terminal participants in that simulation |
| BR-4 | Admin role identities (CEO, CFO rows) are **not** separate group members — facilitator participates as facilitator |
| BR-5 | **No new login link** for group chat — use existing terminal token |
| BR-6 | Group chat is active while simulation is running; closed when simulation ends |
| BR-7 | Private 1:1 chat behaviour must **not** regress |
| BR-8 | Group chat supports text and media (images, documents, videos, links) like private chat |

---

## 6. Acceptance Criteria (Definition of Done — Business)

### Simulation setup

- [ ] Creating a simulation with N terminals and M admin roles results in private threads **plus one** group chat
- [ ] Updating a simulation recreates the group chat correctly

### During live session

- [ ] Facilitator sees **Group Chat** in chat list
- [ ] Each logged-in participant sees **Group Chat** in chat list
- [ ] Facilitator can send a group message; all participants receive it in real time
- [ ] Participant can send a group message; facilitator and other participants receive it
- [ ] Media messages work in group chat (same types as private chat)
- [ ] Private 1:1 chats continue to work without change

### Session end

- [ ] Group chat is no longer usable after simulation ends
- [ ] Group messages are included in simulation history / report where applicable

### Regression

- [ ] One-time token login per terminal still works
- [ ] Admin broadcast to private threads still works
- [ ] End simulation + AI summary report job still completes

---

## 7. Out of Scope (v1)

- Separate login link or token for group chat
- Group chat across multiple simulations
- Message edit or delete in group chat
- @mentions or threaded replies
- Moderation roles (mute/kick) in group chat
- Upgrading Laravel, PHP, Vue, or Node as part of this feature

---

## 8. Dependencies

| Dependency | Why |
|------------|-----|
| Simulation create/update flow | Group row created with simulation |
| `chat_users` / `messages` tables | Store group thread and messages |
| Pusher + Laravel Echo | Real-time delivery to all members |
| `PrivateChat.vue` | Chat list and message UI |
| Simulation history / report collector | Include group messages in exports |
| Existing token login | Participants access group via current session |

**Third-party:** Pusher (required for live chat). OpenAI/S3 only if group messages included in AI report.

---

## 9. Assumptions

| # | Assumption | Risk if wrong |
|---|------------|---------------|
| A-1 | Participants should **see each other's names** in group chat | UI/privacy requirements may differ |
| A-2 | Facilitator appears under admin account name in group (not role name) | May need role label |
| A-3 | One shared timeline for entire simulation (not per-role group rooms) | Scope could expand |
| A-4 | Group Chat label is **"Group Chat"** in English | Localization may be needed later |

---

## 10. Open Questions (for stakeholder)

| # | Question | Decision |
|---|----------|----------|
| Q-1 | Should participants see **each other's terminal names** in group chat? | Pending — assume **yes** |
| Q-2 | Should group chat appear in **AI summary report** as a separate section? | Pending |
| Q-3 | Should group chat be **pinned at top** of chat list for all users? | Pending — assume **yes** |
| Q-4 | Any **moderation** requirement (facilitator-only post)? | Pending — assume **no** for v1 |

---

## 11. Success Metrics (optional)

- Facilitators can broadcast to all participants in one message without using multi-select broadcast
- Reduced facilitator effort during open plenary segments of simulation
- No increase in support issues related to token login or private chat after release

---

## 12. Alignment with Technical Spec

After this requirement is approved:

1. Planning agent creates `docs/plans/group-chat.md`
2. Compare with technical spec in `.cursor/features/` (when created)
3. Resolve any gaps between business acceptance criteria and technical design
4. Stakeholder sign-off: **Approve and build**

| Business requirement | Technical spec section |
|---------------------|------------------------|
| One group per simulation | §3, §4, §5.1 |
| All members can chat | §3.2, §5.3–5.7 |
| No new token | §3.1 |
| Private chat unchanged | §9 regression |
| History/reports | §5.8, technical spec §8 |

---

## 13. Approval

| Role | Name | Date | Status |
|------|------|------|--------|
| Business owner | | | Pending |
| Technical lead | | | Pending |
| Facilitator / UAT | | | Pending |

**Approval options:** Approve and build | Request changes | Defer

---

## 14. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 0.1 | 2026-07-27 | AI draft | Initial business requirement from CURSOR.md + feature spec |
