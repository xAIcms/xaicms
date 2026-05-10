# xAI CMS — AI Content Factory

## Repo
https://github.com/xAIcms/xaicms

## Current State

15 commits / MIT / Docker / 双语 README

### Router (v1.1.1)
- `src/Core/Router.php` — lightweight HTTP router, replaces 1600-line if/else chain
- Supports: exact match, regex with params, group prefixes, nested groups, middleware, catch-all (/*)
- PHP 7.3 compatible (polyfills for str_starts_with, str_ends_with, str_contains)
- `public/index.php` refactored: 1621→~470 lines, same behavior, all routes organized by section

### I18n Translation System (v1.1.1)
- `src/Core/I18n.php` — translation engine, loads PHP array packs
- `lang/zh-CN.php`, `lang/en-US.php` — dictionary files (55+ keys)
- `__()` global function — `__('Dashboard')` returns translated string
- `__f()` for sprintf-style — `__f('Created %d articles', 5)`
- Auto-loads based on `admin_language` setting
- `layout.php` sidebar fully translated (all menu items, quick links)
- 14 languages sorted by cross-border market demand: en-US > ja-JP > ko-KR > de-DE > fr-FR > es-ES > ru-RU > pt-BR > ar-SA > th-TH > vi-VN > id-ID > zh-CN > zh-TW
- `language` — frontend/public site language, default en-US
- `admin_language` — admin panel language, default zh-CN, 14 options same as frontend
- AI generation language is per-api-config, independent of site language
- Both stored in: config.php + settings table
- Settable at: install wizard Step 3 + admin settings Basic tab
- backend layout.php html lang reads admin_language

### Core Systems
| System | Files |
|---|---|
| Hooks | src/Core/Hooks.php — 7 hooks + 1 filter |
| Plugin | src/Core/Plugin.php — scan, activate, deactivate |
| Template | src/Core/Template.php — scan, preview, switch |
| Updater | src/Core/Updater.php — GitHub Release update |
| Scheduler | src/Core/Scheduler.php — pseudo-cron, BT Panel-style |
| Router | src/Core/Router.php — lightweight HTTP router |

### Plugins (6 built-in)
| Plugin | What it does |
|---|---|
| hello | Demo plugin, all hooks example |
| xai-announcements | Platform notices management |
| xai-updates | System changelog |
| xai-spider-log | Bot crawler analytics |
| xai-sms | SMS verification (Tencent Cloud) |
| xai-points | Credit packages + recharge orders |

### Features
- CMS (Article/Category/Tag/Media/UEditor)
- AI settings (Provider/Key/Model)
- User management
- Multi-region (17) + Multi-language (15)
- SEO (Sitemap/RSS/SpiderLogs)
- Task scheduler (auto-publish, AI generation)
- Docker Compose one-line deploy

### Coming (V2)
- [ ] xai-schemes plugin extraction (on hold — too coupled with AI engine)
- [ ] JSON REST API
- [ ] Plugin marketplace
- [ ] Template marketplace
- [ ] ProductHunt launch
- [ ] SEO: hreflang, Breadcrumb schema, robots.txt route

### Frequency Types
| Mode | Example |
|---|---|
| Every N min | Every 30min |
| Every N hrs | Every 6h |
| Daily at time | 08:00 daily |
| Weekly on days | Mon/Wed/Fri 09:00 |
| Custom seconds | Any interval |
