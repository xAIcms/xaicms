# xAI CMS — AI Content Factory

## Repo
https://github.com/xAIcms/xaicms

## Architecture
```
xAIcms/
├── src/
│   ├── Core/
│   │   ├── Hooks.php      ← WordPress-style action/filter engine  
│   │   └── Plugin.php      ← Plugin loader + activation
│   ├── Controllers/
│   ├── Models/
│   └── Services/
├── plugins/                ← Plugin directory
│   └── hello/plugin.php    ← Example plugin
├── templates/
│   ├── admin/plugins_list.php  ← Plugin management UI
│   └── ...
├── public/index.php        ← Entry point (loads hooks + plugins)
├── docker-compose.yml
└── Dockerfile
```

## Done (2026-05-09)
- [x] Docker Compose 一键部署
- [x] .env → config.php 环境配置
- [x] Git 初始化 + GitHub 推送
- [x] README EN 重写
- [x] **Plugin Hook 系统** (add_action/add_filter/do_action/apply_filters)
- [x] **插件自动发现 + 启用/禁用管理面板**
- [x] **Hello World 示例插件**
- [x] **Hook 点**: article_saved, before_footer, admin_dashboard_widgets
- [x] 默认模型: deepseek-v4-flash / deepseek-v4-pro

## Coming
- [ ] JSON REST API
- [ ] 模板市场
- [ ] 插件市场
- [ ] CLI 脚手架
- [ ] ProductHunt 发布
