# Plugin Migration Guide

This guide outlines how to extract existing core features into standalone plugins.
Each plugin lives in `/plugins/{slug}/` with a `plugin.php` entry point.

## Available Hooks

| Hook | Type | When |
|---|---|---|
| `article_saved` | Action | Article created or updated |
| `user_registered` | Action | New user registration |
| `before_footer` | Action | Before closing footer |
| `before_output` | Action | Before `</body>` |
| `admin_dashboard_widgets` | Action | Admin dashboard content area |
| `admin_menu` | Action | Admin sidebar (add custom items) |
| `article_title` | Filter | Modify article title before display |

## Migration Plan

### 1. xai-points (积分充值)
**Core files to move**: `Models/PointPackage.php`, `Models/RechargeOrder.php`, `Controllers/AdminPointPackageController.php`, `Controllers/AdminRechargeController.php`, `Controllers/UserRechargeController.php`
**Routes affected**: `/admin/point-packages`, `/admin/recharge-orders`, `/user/recharge`
**Tables**: `point_packages`, `recharge_orders`
**Hooks to add**: `points_charged`, `points_deducted`, `before_recharge`

### 2. xai-schemes (AI 批量方案)
**Core files to move**: `Models/AiScheme.php`, `Controllers/AdminAiSchemeController.php`, `Controllers/UserAiSchemeController.php`
**Routes affected**: `/admin/ai-schemes`, `/user/ai-schemes`
**Tables**: `ai_schemes`
**Hooks to add**: `scheme_created`, `scheme_completed`, `before_scheme_run`

### 3. xai-announcements (公告)
**Core files to move**: `Models/Announcement.php`, `Controllers/AdminAnnouncementController.php`
**Routes affected**: `/admin/announcements`
**Tables**: `announcements`

### 4. xai-updates (系统更新日志)
**Core files to move**: `Models/SystemUpdate.php`, `Controllers/AdminSystemUpdateController.php`
**Routes affected**: `/admin/system-updates`
**Tables**: `system_updates`

### 5. xai-spider-log (爬虫日志)
**Core files to move**: `Models/SpiderLog.php`
**Routes affected**: `/admin/spider-logs`
**Tables**: `spider_logs`

### 6. xai-sms (短信验证)
**Core files to move**: `Services/TencentSmsService.php`, `Models/VerificationCode.php`
**Routes affected**: `/api/send-sms`
**Tables**: `verification_codes`

## Migration Steps (per plugin)

1. Create `/plugins/{slug}/plugin.php`
2. Move model/controller files into `/plugins/{slug}/src/`
3. Use hooks to register routes and admin menus
4. Remove old files from core
5. Remove old routes from `public/index.php`
6. Test
