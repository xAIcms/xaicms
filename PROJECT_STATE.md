# xAI CMS — AI Content Factory

## Repo
https://github.com/xAIcms/xaicms
Latest: [v1.1.0](https://github.com/xAIcms/xaicms/releases/tag/v1.1.0)

## Architecture
```
xAIcms/
├── src/Core/
│   ├── Hooks.php      ← Action/filter engine
│   ├── Plugin.php      ← Plugin loader + activation
│   ├── Template.php    ← Template scanning + switching
│   └── Updater.php     ← GitHub Release update system
├── plugins/            ← Plugin directory
│   └── hello/          ← Example plugin
├── templates/          ← Frontend themes
├── public/index.php    ← Entry point
├── docker-compose.yml
└── VERSION
```

## Done (v1.1.0)
- [x] Docker Compose 一键部署
- [x] Plugin Hook 系统 + 管理面板 + 示例插件
- [x] Template 系统 + 切换面板
- [x] GitHub Release 在线更新
- [x] MIT License + CONTRIBUTING.md
- [x] AI 批量生成 (DeepSeek V4 Flash/Pro)
- [x] CMS (Article/Category/Tag/Media)
- [x] Multi-region + Multi-language
- [x] SEO (Sitemap/RSS/SpiderLogs)
- [x] User + Points + Recharge

## Coming (v1.2.0+)
- [ ] JSON REST API
- [ ] Plugin marketplace
- [ ] Template marketplace
- [ ] CLI scaffolding
- [ ] ProductHunt launch
