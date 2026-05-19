# xAI CMS — AI Content Factory

## Repo
https://github.com/xAIcms/xaicms

## Current State

v1.1.0 / MIT / Docker / 10 plugins / 12-Agent audited

### v1.1.0 (2026-05-19) — Major Update

**New Features:**
- Plan system: Free / Pro / Enterprise tiers with feature gating
- Plugin marketplace: 5 built-in + 4 new plugins (SEO analyzer, auto-translate, social share, newsletter)
- Template marketplace with activate/switch support
- WeChat Pay V3 integration (Native QR code payment)
- Developer center: registration, revenue sharing (default 70%), withdrawal
- Developer documentation
- Upgrade page with plan comparison
- Version update notifications in admin dashboard
- FAQ page, search functionality (/search)
- Article sidebar bio (database-driven, admin-editable)
- Product landing page redesign

**Security Fixes (12-Agent audit):**
- SQL injection fix (Scheduler.php)
- Session fixation fix (session_regenerate_id on login)
- CSRF protection added to tag creation/editing
- XSS fixes (landing.php, category delete)
- Removed backdoor files (promote_admin.php)
- Removed diagnostic tool (check.php)
- Removed installer directory after installation
- Plugin controllers now require admin authentication
- Gemini API key no longer logged
- Database error messages sanitized

**Bug Fixes:**
- Tag detail page route (/tag/{slug})
- About/Privacy/Terms/FAQ routes
- Article page blue→indigo color consistency
- Footer FAQ link, removed pricing link
- 404 page template created

### Core Systems
| System | Files |
|---|---|
| Hooks | src/Core/Hooks.php — 7 hooks + 1 filter |
| Plugin | src/Core/Plugin.php — scan, activate, deactivate |
| Template | src/Core/Template.php — scan, preview, switch |
| Plan | src/Core/Plan.php — feature gate, tier management |
| Router | src/Core/Router.php — HTTP router |
| I18n | src/Core/I18n.php — translation engine |
| Scheduler | src/Core/Scheduler.php — pseudo-cron |
| Updater | src/Core/Updater.php — GitHub Release update |

### Plugins (10 built-in)
| Plugin | Status |
|---|---|
| hello | Demo plugin |
| xai-announcements | Platform notices |
| xai-updates | System changelog |
| xai-spider-log | Bot crawler analytics |
| xai-sms | SMS verification |
| xai-points | Credit packages |
| xai-seo-analyzer | SEO scoring |
| xai-auto-translate | Auto translation |
| xai-social-share | Social sharing |
| xai-newsletter | Email subscription |

### New Models
- Order.php — payment order tracking
- Developer.php — developer profile, revenue, withdrawal
- Plan.php (Core) — feature gate and tier system

### New Services
- WechatPayService.php — WeChat Pay V3 API

### Database
- New tables: orders, developers, developer_transactions
- New settings: plan, plan_features, pricing_*, wx_*, icpBeian

### Live Demo
https://xaicms.com/
Admin: admin@xaicms.com / admin123

### Coming (V2)
- [ ] JSON REST API (public)
- [ ] Plugin marketplace (public listing)
- [ ] Template marketplace (public listing)
- [ ] CLI scaffolding (`php xai make:plugin`)
- [ ] Composer autoload + PSR-4 namespaces
