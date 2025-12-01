# Uploads Directory

Secure document uploads (study materials, marksheets, etc.) are stored here. The repository keeps this folder empty except for this note so version control stays clean.

Production tips:
- Apply server rules (e.g., Apache `.htaccess`) to prevent direct file listing.
- Store files outside the webroot for maximum safety and adjust `file_path` accordingly.
- Validate and sanitise every upload via `controllers/StaffController` extensions before saving.


