# xAI CMS — AI Content Factory

## Repo
https://github.com/xAIcms/xaicms

## Current State

15 commits / MIT / Docker / 双语 README

### Language System (v1.1.1)
- `language` — frontend/public site language (for SEO targeting)
- `admin_language` — admin panel language (for operator convenience)
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
- [ ] xai-schemes plugin extraction
- [ ] JSON REST API
- [ ] Plugin marketplace
- [ ] Template marketplace
- [ ] ProductHunt launch

### Frequency Types
| Mode | Example |
|---|---|
| Every N min | Every 30min |
| Every N hrs | Every 6h |
| Daily at time | 08:00 daily |
| Weekly on days | Mon/Wed/Fri 09:00 |
| Custom seconds | Any interval |
