# Plan: Add Gallery Side Menu

**Request:** Add one more side menu item named **Gallery**  
**Tier:** Small feature  
**Defaults (questions skipped):** TYPE=feature, TDD=off, UNIT_TESTS=off

---

## Current state

Admin side menu: `resources/views/inc/dashboardnavmenu.blade.php`

| Menu item | Route |
|-----------|-------|
| New Simulation | `/simulationcreate` |
| Simulation List | `/simulationlist` |
| Media Upload (sub) | Images, Documents, Video Links |
| Simulation History | `/simulation_history` (super admin) |

Uploads stored in `upload_files` table, served from `public/uploads/`.

---

## Proposed solution

Add a new top-level side menu **Gallery** that opens a page showing all active uploaded **images** in a grid (read-only view). Matches existing admin layout and upload patterns.

---

## Tasks

### Task 1 — Route + controller

**Files:**
- `routes/web.php` — add inside admin middleware group:
  ```php
  Route::get('/gallery', 'UploadsController@gallery_index')->name('gallery');
  ```
- `app/Http/Controllers/UploadsController.php` — add method:
  ```php
  public function gallery_index()
  {
      $images = Upload::where([['type', 1], ['status', 0]])->orderBy('id', 'DESC')->get();
      $path = 'gallery';
      return view('simulation.gallery', compact('images', 'path'));
  }
  ```

### Task 2 — Gallery view

**File:** `resources/views/simulation/gallery.blade.php`

- Extend existing dashboard layout (`layouts/dashboardheader` pattern like `image_upload.blade.php`)
- Grid of image thumbnails from `$images`
- Use `asset('uploads/' . $image->path_name)` or existing path pattern from `image_upload.blade.php`

### Task 3 — Side menu item

**File:** `resources/views/inc/dashboardnavmenu.blade.php`

- Add new `<a href="/gallery">` block after Simulation List (or after Media Upload)
- Label: **Gallery**
- Active class: `$path === 'gallery'`
- Reuse an SVG icon (image-style, same as Images submenu or simple gallery icon)

---

## Acceptance criteria

- [ ] Admin sees **Gallery** in left side menu
- [ ] Clicking Gallery opens `/gallery` page
- [ ] Page shows uploaded images in a grid
- [ ] Active menu highlight works on Gallery page
- [ ] Existing menu items unchanged
- [ ] Admin middleware required (same as other upload pages)

---

## Out of scope

- Upload from Gallery page (view only)
- Videos/documents in gallery (images only for v1)
- Database changes

---

## Files touched (estimate: 4)

1. `routes/web.php`
2. `app/Http/Controllers/UploadsController.php`
3. `resources/views/simulation/gallery.blade.php` (new)
4. `resources/views/inc/dashboardnavmenu.blade.php`

---

## Test plan (manual)

1. Login as admin
2. Confirm **Gallery** appears in side menu
3. Click Gallery → page loads with images
4. Upload new image via Images menu → appears on Gallery after refresh
