# NEXERA Assets Guidance

This folder contains lightweight placeholder assets used for the NEXERA UI:

- `bg-tech-loop.gif` – background animation for the hero section.
- `hero-glass.gif` – overlay accent for landing page cards.
- `spinner-neon.gif` – micro-interaction spinner used in “Developing” modals.

These GIFs are intentionally minimal to keep the repository small. For production or staging, replace them with higher fidelity visuals:

1. Export your assets to `.webp` or `.gif` formats for compatibility with browsers supported by XAMPP.
2. Maintain the same filenames or update the corresponding references in:
   - `css/style.css`
   - `views/components/development-modal.php`
   - `index.php`
3. For background visuals, prefer loopable clips with subtle motion to preserve readability on the dark glossy theme.

> Tip: Store large media files outside version control (e.g., in a CDN or static storage bucket) and update the theme to reference the hosted URLs for faster deployment pipelines.


