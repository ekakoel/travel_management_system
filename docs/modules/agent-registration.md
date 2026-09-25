# Agent Registration

Status: active

## Public Flow

- Canonical routes are `GET /become-partner`, `POST /become-partner`, and `GET /become-partner/pending`.
- Legacy `/agent/register` and `/agent-register` remain available for compatibility.
- The public request uses `submission_token`; a repeated processed token redirects to the pending page without creating another application.
- An active application is one with status `pending`, `approved`, or `verified`. A rejected applicant may submit again.

## Data and Documents

- `agents` remains the application entity and stores company, contact, consent, and workflow data.
- Each new application creates an inactive `users` record linked by `agents.user_id`.
- Partner documents are stored on the `private` disk and recorded in `agent_documents` with original filename metadata.
- Only the existing agent-review admin positions may download partner documents through the guarded download route. Public storage URLs are forbidden for these files.

## Review and Messaging

- A successful submission creates a `pending` agent application in one database transaction.
- Developer, administrator, and author users receive the existing database notification pattern; the applicant receipt is sent to `agents.contact_email`.
- Verifying an application changes the Agent status to `verified` and activates its linked User. Rejecting it keeps the linked User inactive.
