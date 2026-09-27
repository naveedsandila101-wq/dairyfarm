# Dairyfarm WordPress Theme

Custom, dependency-free theme (no ACF, no page builder, no build step). Requires WordPress 6.3+ and PHP 7.4+.

## Install

1. Copy `wp-content/themes/dairyfarm` into your site's `wp-content/themes/`.
2. Activate **Appearance → Themes → Dairyfarm**.
3. Click **Set up demo home page** in the admin notice. This creates the Home, About Us, Contact Us and News pages, menus, 6 sample products and 3 reviews. It never overwrites existing content.
4. Replace the dummy images: edit a page, open a section in its **Page Sections** box, and choose images.

## Section page templates (meta boxes)

Create a page, choose one of these templates and save. The block editor is replaced by a **Page Sections** box. It has one panel per section, and each panel has a show/hide switch, all the section's fields, repeaters for lists, and a background and anchor setting. Unsaved fields use the template's defaults, so the page looks complete straight away.

| Template | Sections |
| --- | --- |
| **Home Page** | Hero · Why Choose Us · About · Products · Process · Stats · Gallery · Reviews · FAQ · Call to Action |
| **About Us** | Page Banner · Our Story · Stats · Our Values · Process · Meet the Family (team) · Reviews · Call to Action |
| **Contact Us** | Page Banner · Contact cards, form and map · FAQ · Call to Action (hidden by default) |

Templates and their default copy are defined in `dairyfarm_section_templates()` in `inc/page-sections.php` (filterable with `dairyfarm_section_templates`).

The same sections are also blocks: on any other page, insert them from the "Dairy Farm Sections" block category, or use the "Complete Home Page" pattern. Both use the same `block.json` field schema and `render.php`, so a section looks the same either way.

Any image left empty shows a bundled dummy illustration from `assets/images/dummy/`. This covers the hero, banners, about, gallery, team, products without a featured image, and post cards.

## Contact form

The Contact section has a built-in form (name, email, phone, subject, message). Messages are emailed with `wp_mail()` to the email in **Customize → Dairyfarm Theme Options → Business & Contact Details**, or the site admin email. It is protected by a nonce, a honeypot field and a limit of 5 messages per visitor every 10 minutes. Install an SMTP plugin for reliable delivery; local XAMPP installs usually cannot send mail. To use a form plugin instead, paste its shortcode into the section's **Form shortcode** field. The map uses a Google Maps embed of the Customizer address (no API key needed) or the section's **Map location**.

## Where content is edited

| What | Where |
| --- | --- |
| Home, About and Contact page sections (copy, images, buttons, background, show/hide) | **Pages → (page)** → **Page Sections** meta box |
| Products (image, price, unit, badge, category, order link) | **Products** |
| Customer reviews (name, photo, rating, headline, role) | **Reviews** |
| Top bar, header button, phone, email, address, hours, social links, footer text | **Appearance → Customize → Dairyfarm Theme Options** |
| Logo | **Customize → Site Identity** |
| Header and footer menus | **Appearance → Menus** (4 locations) |

## Sections (blocks)

Hero (split or full-bleed cover) · Page Banner (inner pages) · Why Choose Us · About Us · Our Products (with category filter) · Farm-to-Table Process · Stats Band · Farm Gallery (mosaic plus lightbox) · Customer Reviews · FAQ (adds FAQ schema) · Meet the Family / Team · Contact Details & Form (with map) · CTA / Newsletter (takes any form shortcode).

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
