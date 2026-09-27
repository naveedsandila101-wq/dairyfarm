# Dairyfarm WordPress Theme

Custom, dependency-free theme (no ACF, no page builder, no build step). Requires WordPress 6.3+ and PHP 7.4+.

## Install

1. Copy `wp-content/themes/dairyfarm` into your site's `wp-content/themes/`.
2. Activate **Appearance → Themes → Dairyfarm**.
3. Click **Set up demo home page** in the admin notice. This creates the Home and News pages, menus, 6 sample products and 3 reviews. It never overwrites existing content.
4. Replace the dummy images: edit the Home page, open a section in **Home Page Sections**, and choose images.

## Home page: two ways to build it

- **Home Page template (meta boxes).** Create a page and choose the **Home Page (sections in meta boxes)** template, then save. The block editor is replaced by a **Home Page Sections** box. It has one panel per section, and each panel has a show/hide switch, all the section's fields, repeaters for lists, and a background and anchor setting. Unsaved fields use the section defaults, so the page looks complete straight away. The demo importer uses this template.
- **Blocks.** On any other page, insert sections from the "Dairy Farm Sections" block category, or use the "Complete Home Page" pattern.

Both use the same `block.json` field schema and `render.php`, so a section looks the same either way.

Any image left empty shows a bundled dummy illustration from `assets/images/dummy/`. This covers the hero, about, gallery, products without a featured image, and post cards.

## Where content is edited

| What | Where |
| --- | --- |
| Home page sections (copy, images, buttons, background, show/hide) | **Pages → Home** → **Home Page Sections** meta box (Home Page template) |
| Products (image, price, unit, badge, category, order link) | **Products** |
| Customer reviews (name, photo, rating, headline, role) | **Reviews** |
| Top bar, header button, phone, email, address, hours, social links, footer text | **Appearance → Customize → Dairyfarm Theme Options** |
| Logo | **Customize → Site Identity** |
| Header and footer menus | **Appearance → Menus** (4 locations) |

## Sections (blocks)

Hero (split or full-bleed cover) · Why Choose Us · About Us · Our Products (with category filter) · Farm-to-Table Process · Stats Band · Farm Gallery (mosaic plus lightbox) · Customer Reviews · FAQ (adds FAQ schema) · CTA / Newsletter (takes any form shortcode).

Every section has **Section Settings**: a background tone (white, cream, mint, dark, brand) and an anchor ID for menu links such as `#products`.

## Architecture

- `blocks/<name>/block.json`: attributes and defaults, plus the sidebar field schema (`dairyfarm.fields`).
- `blocks/<name>/render.php`: the server-side markup. The editor preview uses the same file.
- `assets/js/blocks-editor.js`: one generic editor script that builds the sidebar from the schema.
  Field types: text, url, textarea, number, toggle, select, icon, image, term, repeater.
- To add a section, create a folder with `block.json` and `render.php`. It registers automatically.
- `inc/`: setup, Customizer, CPTs and meta, JSON-LD schema, demo importer, helpers, SVG icons.

## Notes

- Google Fonts is loaded by default. To self-host for GDPR, use `add_filter( 'dairyfarm_font_url', '__return_empty_string' );`.
- To disable the theme's JSON-LD (for example, if an SEO plugin already outputs it), use `add_filter( 'dairyfarm_schema_enabled', '__return_false' );`.
- The Products and Reviews post types live in the theme. Move `inc/post-types.php` and `inc/meta-boxes.php` into a site plugin before going live, so content survives a theme change.
