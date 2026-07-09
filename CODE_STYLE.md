# COMP1841 Code Style

This document is the shared coding standard for the University of Greenwich Coursework Discussion Platform.

## 1. Project conventions

- Architecture: PHP MVC with PDO and MySQL.
- UI: Tailwind CSS v4.
- Interaction: vanilla JavaScript; GSAP is used only for motion.
- Indentation: 4 spaces. Do not use tabs.
- Line endings: use the repository default. Do not reformat unrelated files.
- Prefer the existing implementation pattern over introducing a second abstraction or style.
- Make the smallest change that fully solves the task.

## 2. Directory ownership

```text
app/Controllers/       Request handling and view data
app/Core/              MVC infrastructure
app/Models/            Database access and domain queries
app/Views/             Page templates
app/Views/partials/    Shared view fragments
resources/css/         Tailwind source files
public/assets/css/     Generated browser-ready CSS
public/assets/js/      Application scripts and browser-ready vendor files
```

Do not create new folders when an existing folder already owns the concern.

## 3. PHP

Follow a PSR-12-like layout:

- Classes use `PascalCase`.
- Methods and variables use `camelCase`.
- Constants use `UPPER_SNAKE_CASE`.
- Opening braces for classes and methods go on the next line.
- Opening braces for control statements stay on the same line.
- Use parameter types where they are already established, but do not add scalar return type declarations such as `: string`, `: ?string`, `: int`, or `: bool`.
- Use strict comparisons (`===`, `!==`) by default.
- Prefer early returns over deeply nested conditions.

```php
public function findById(int $id)
{
    if ($id <= 0) {
        return null;
    }

    return $this->moduleModel->findById($id);
}
```

Controllers coordinate the request, call models, and pass data to views. They must not contain SQL or large HTML fragments.

Models own database queries. Use PDO prepared statements for values originating outside the query.

```php
$statement = $this->db->prepare(
    'SELECT * FROM discussions WHERE id = ?'
);
$statement->execute([$id]);
```

## 4. Views and partials

Views render controller-provided data. Do not query the database or instantiate models inside a view.

Use alternative PHP syntax in HTML:

```php
<?php foreach ($modules as $module): ?>
    <a href="<?= $url('modules/' . rawurlencode($module['code'])) ?>">
        <?= htmlspecialchars($module['code'], ENT_QUOTES, 'UTF-8') ?>
    </a>
<?php endforeach; ?>
```

Escape dynamic text and attribute values with:

```php
htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
```

Use `BASE_URL` for browser URLs and `ROOT_PATH` for server-side includes:

```php
<img src="<?= BASE_URL ?>/assets/images/shared/logo.png" alt="">

<?php require ROOT_PATH . '/app/Views/partials/navbar.php'; ?>
```

Never hardcode `/COMP1841/public`, `../`, or machine-specific absolute paths.

For long HTML elements, keep related attributes together and wrap at a readable boundary. Indent child elements by 4 spaces.

```php
<button
    id="menu-btn"
    type="button"
    class="flex items-center gap-3"
    aria-controls="mega-menu"
    aria-expanded="false"
>
    Menu
</button>
```

## 5. Tailwind CSS

Use Tailwind utilities for layout, spacing, responsive behavior, color, and visual states.

Order classes approximately as follows:

1. Position and z-index
2. Display and layout
3. Sizing
4. Spacing
5. Border and background
6. Typography
7. Effects and transitions
8. State variants
9. Responsive variants

Keep a class list on one line when it remains readable. Wrap the HTML attribute rather than placing every utility on its own line.

```html
class="flex min-h-20 items-center gap-4 border-b bg-white px-5 transition-colors hover:bg-black hover:text-white lg:px-32"
```

- Use mobile-first responsive classes.
- Reuse the project breakpoints (`sm`, `md`, `lg`, `xl`).
- Arbitrary values are allowed when they represent an intentional design measurement.
- Avoid duplicating the same visual rule in custom CSS and Tailwind.
- Do not manually edit `public/assets/css/app.css`; it is generated.
- Add reusable custom utilities only to `resources/css/input.css`.

Build commands:

```powershell
npm run dev
npm run build
```

Run `npm run build` after changing Tailwind classes or `resources/css/input.css`.

## 6. JavaScript

- Use modern vanilla JavaScript supported by current browsers.
- Use `const` by default and `let` only for reassignment.
- Use single quotes for JavaScript strings.
- End statements with semicolons.
- Use trailing commas in multiline objects and argument lists.
- Prefer small named functions for repeated state changes.
- Return early when required elements are missing.
- Do not put application logic in minified vendor files.

```javascript
const menuButton = document.querySelector('#menu-btn');

if (!menuButton) {
    return;
}
```

Use stable IDs for unique components and `data-*` attributes for repeated items or state hooks:

```text
#menu-btn
#site-logo
#mega-menu
[data-menu-trigger]
[data-menu-panel]
```

Keep selectors and state names aligned between PHP and JavaScript. Do not rename them in only one file.

## 7. GSAP and vendor assets

GSAP owns animation values such as transforms, opacity during motion, timing, easing, and timelines. Tailwind owns the static appearance and responsive layout.

```javascript
gsap.to(logo, {
    x: logoTargetX,
    duration: 0.42,
    ease: 'power3.out',
});
```

- Do not use GSAP to define colors, spacing, borders, or permanent layout.
- Respect `prefers-reduced-motion`.
- Kill an active timeline before starting its replacement.
- Keep animation code in files such as `public/assets/js/navbar.js`.
- Never edit `public/assets/js/gsap.min.js` directly.
- Browser-ready vendor files in `public/assets/js` must remain loadable without exposing `node_modules` publicly.

## 8. Accessibility

- Interactive elements must be semantic buttons or links.
- Icon-only buttons require an accessible label.
- Toggle buttons must update `aria-expanded`.
- Dialog-like overlays must update `aria-hidden` and use `inert` while closed.
- Closed UI must not remain clickable or keyboard-focusable.
- Full-screen menus must close with Escape.
- Maintain visible keyboard focus styles.
- Images require meaningful `alt` text, or `alt=""` when decorative.

## 9. Navbar-specific rules

The navbar lives in `app/Views/partials/navbar.php`; its behavior lives in `public/assets/js/navbar.js`.

- Preserve `BASE_URL`, the current navigation links, and controller-provided module data.
- Keep the full-screen Harvard-inspired mega menu.
- Do not query the database from the navbar.
- Use Tailwind for open/closed visual states and GSAP for menu/logo movement.
- Keep `#menu-btn`, `#site-logo`, and `#mega-menu` stable.
- Search, account controls, and other header UI must not interfere with the full-screen menu state.

## 10. Comments and documentation

Comments explain intent, constraints, or non-obvious decisions. Do not narrate obvious syntax.

Good:

```javascript
// Align the animated logo with the responsive header edge.
```

Avoid:

```javascript
// Set x to zero.
```

## 11. Required checks

Run checks appropriate to the changed files:

```powershell
php -l path/to/file.php
node --check public/assets/js/navbar.js
npm run build
git diff --check
```

Before finishing:

- Test the affected page at mobile and desktop widths.
- Test keyboard navigation and Escape for overlays.
- Confirm existing routes and `BASE_URL` links still work.
- Confirm generated assets are up to date.
- Leave unrelated user changes untouched.
