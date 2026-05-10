# Contributing to xAI CMS

Thanks for considering contributing! Here's how to get started.

## Ways to Contribute

- **Report bugs** — Open an [Issue](https://github.com/xAIcms/xaicms/issues)
- **Suggest features** — Open a [Discussion](https://github.com/xAIcms/xaicms/discussions)
- **Write plugins** — Create plugins in `/plugins/your-plugin/plugin.php`
- **Build templates** — Create templates in `/templates/your-template/`
- **Submit PRs** — Fix bugs, improve docs, add features

## Development Setup

```bash
git clone https://github.com/xAIcms/xaicms.git
cd xaicms
docker-compose up -d
# Visit http://localhost:8080
```

## Plugin Development

Every plugin is a folder under `/plugins/` with a `plugin.php`:

```php
<?php
/**
 * Plugin Name: My Plugin
 * Description: Does something cool
 * Version: 1.0.0
 * Author: Your Name
 */

add_action('before_footer', function() {
    echo '<p>Hello from My Plugin!</p>';
});

add_filter('article_title', function($title) {
    return '⭐ ' . $title;
});
```

Available hooks:
- `article_saved` — After article created/updated
- `before_footer` — Before page footer
- `admin_dashboard_widgets` — Admin dashboard widgets
- `before_output` — Before page output (coming soon)

## Template Development

Templates live in `/templates/your-template/`. Create a `template.json`:

```json
{
    "name": "My Theme",
    "description": "A clean blog theme",
    "version": "1.0.0",
    "author": "Your Name"
}
```

Add `screenshot.png` for the admin preview.

## Pull Request Process

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes
4. Test: `docker-compose up -d` and verify
5. Commit with clear message
6. Push and open a PR against `main`

## Style Guide

- **PHP**: Follow PSR-12 where practical
- **HTML**: Bootstrap 5 classes, semantic markup
- **SQL**: Use PDO prepared statements
- **Plugins**: No breaking changes to existing hooks

## Questions?

Open a [Discussion](https://github.com/xAIcms/xaicms/discussions) — we're happy to help.
