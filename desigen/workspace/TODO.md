<instructions>
This file powers chat suggestion chips. Keep it focused and actionable.

# Be proactive
- Suggest ideas and things the user might want to add *soon*. 
- Important things the user might be overlooking (SEO, more features, bug fixes). 
- Look specifically for bugs and edge cases the user might be missing (e.g., what if no user has logged in).

# Rules
- Each task must be wrapped in a "<todo id="todo-id">" and "</todo>" tag pair.
- Inside each <todo> block:
  - First line: title (required)
  - Second line: description (optional)
- The id must be a short stable identifier for the task and must not change when you rewrite the title or description.
- Only maintain this file when there is meaningful unfinished work left after the current response.
- Add tasks only when there are concrete, project-specific next steps from current progress.
- Do NOT add filler tasks. Skip adding if no meaningful next step exists.
- Keep this list high-signal and concise, usually 1-3 strong tasks.
- If there are already 3 strong open tasks, usually do not add more.
- Remove or rewrite stale tasks when they are completed, obsolete, duplicated, or clearly lower-priority than current work.
- Preserve existing todo ids whenever you edit or reorder tasks.
- If the user dismisses or rejects a task, do not recreate it unless they ask for it again or it becomes truly blocking.
- Re-rank remaining tasks by current impact and urgency.
- Prefer specific wording tied to real project scope/files; avoid vague goals.
</instructions>

<!-- Add tasks here only when there are real next steps. -->

<todo id="rentals-url-sync">
Sync rentals filters to URL params
Persist type/location/price/amenities filters in URL query params so searches are shareable (/rentals?type=villa&location=Dilijan).
</todo>

<todo id="seo-meta-all-pages">
Add SEO meta tags (react-helmet-async) to Blog + Rentals pages
Set title, description, og:image per post/property and for listing pages.
</todo>

<todo id="rentals-map-view">
Build map view toggle for the Rentals listing page
Show property pins on a real (Leaflet or Google Maps) map synced with the list — hover to highlight, click to open property.
</todo>
