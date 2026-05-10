# xAI CMS — AI Content Factory

## Repo
https://github.com/xAIcms/xaicms

## Current State

22 commits / MIT / Docker / 双语 README / v1.1.1

### Router
- `src/Core/Router.php` — lightweight HTTP router, replaces if/else chain
- Supports: exact match, regex with params, group prefixes, nested groups, middleware, catch-all (/*)
- PHP 7.3 compatible
- `public/index.php` refactored: 1621→~470 lines

### I18n Translation System
- `src/Core/I18n.php` — translation engine, PHP array dictionary
- `lang/zh-CN.php`, `lang/en-US.php` — 131 keys each
- `__()` global function, `__f()` for sprintf-style
- Auto-loads based on `admin_language` setting
- Admin sidebar + frontend navbar/footer/all templates translated

### Language System
- 14 languages: en-US > ja-JP > ko-KR > de-DE > fr-FR > es-ES > ru-RU > pt-BR > ar-SA > th-TH > vi-VN > id-ID > zh-CN > zh-TW
- `language` — frontend site language, default en-US
- `admin_language` — admin panel language, default zh-CN
- AI generation language is per-api-config, independent

### SEO
- `hreflang` tags — x-default + page language
- `BreadcrumbList` Schema.org JSON-LD
- `/robots.txt` route — settings-driven with auto-default
- Open Graph + Twitter Card + Canonical + GEO tags
- Sitemap XML + RSS Feed
- Article-level SEO fields (seo_title/seo_description/seo_keywords)

### Settings-Driven Pages (admin editable)
- About page: hero title/desc, HTML content, contact email/phone/address
- Privacy Policy: title + HTML content
- Terms of Service: title + HTML content
- Homepage sections: about, services, CTA — all HTML
- Landing page: full HTML content, falls back to article list

### Core Systems
| System | Files |
|---|---|
| Hooks | src/Core/Hooks.php — 7 hooks + 1 filter |
| Plugin | src/Core/Plugin.php — scan, activate, deactivate |
| Template | src/Core/Template.php — scan, preview, switch |
| Updater | src/Core/Updater.php — GitHub Release update |
| Scheduler | src/Core/Scheduler.php — pseudo-cron |
| Router | src/Core/Router.php — HTTP router |
| I18n | src/Core/I18n.php — translation engine |

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
- AI content generation (multi-model, multi-language)
- User management + points system
- Multi-language (14) + Multi-region (17)
- SEO (Sitemap/RSS/SpiderLogs/hreflang/Breadcrumb/OG/Twitter)
- Task scheduler (auto-publish, AI generation)
- Docker Compose one-line deploy
- Online update (GitHub Release)

### Coming (V2)
- [ ] xai-schemes plugin extraction (on hold — too coupled with AI engine)
- [ ] Composer autoload + PSR-4 namespaces
- [ ] JSON REST API
- [ ] Plugin marketplace
- [ ] Template marketplace
- [ ] ProductHunt launch
