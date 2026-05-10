# xAI CMS — AI Content Factory

## Repo
https://github.com/xAIcms/xaicms
Latest: [v1.1.0](https://github.com/xAIcms/xaicms/releases/tag/v1.1.0)

## Architecture
```
xAIcms/
├── src/Core/
│   ├── Hooks.php      ← Action/filter engine (7 hooks)
│   ├── Plugin.php      ← Plugin loader + activation
│   ├── Template.php    ← Template scanning + switching
│   └── Updater.php     ← GitHub Release update system
├── plugins/            ← Plugin directory
│   └── hello/          ← Example plugin (all hooks demo)
├── templates/          ← Frontend themes + admin templates
├── public/index.php    ← Entry point
├── docker-compose.yml
├── README.md / README_CN.md
└── PLUGIN_MIGRATION.md  ← 6-plugin extraction roadmap
```

## Done (v1.1.0)
- [x] Docker Compose 一键部署
- [x] Plugin Hook 系统 + 管理面板 + 示例插件
- [x] Template 系统 + 切换面板
- [x] GitHub Release 在线更新
- [x] MIT License + CONTRIBUTING.md
- [x] 中英双语 README
- [x] AI 批量生成 (DeepSeek V4 Flash/Pro)
- [x] AI 设置页 (Provider/Key/Model配置)
- [x] CMS (Article/Category/Tag/Media + UEditor富文本)
- [x] Multi-region + Multi-language
- [x] SEO (Sitemap/RSS/SpiderLogs)
- [x] User + Points + Recharge

## Hook 点 (7个)
- `article_saved` — 文章保存后
- `user_registered` — 用户注册后
- `before_footer` — 页脚前
- `before_output` — 页面输出前
- `admin_dashboard_widgets` — 后台仪表盘
- `admin_menu` — 后台侧栏菜单
- `article_title` (Filter) — 文章标题

## 插件迁移计划 (PLUGIN_MIGRATION.md)
1. xai-points — 积分充值
2. xai-schemes — AI 批量方案
3. xai-announcements — 公告
4. xai-updates — 系统更新日志
5. xai-spider-log — 爬虫日志
6. xai-sms — 短信验证

## Coming (v1.2.0+)
- [ ] JSON REST API
- [ ] Plugin marketplace
- [ ] Template marketplace
- [ ] CLI scaffolding
- [ ] ProductHunt launch
