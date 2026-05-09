# xAI CMS — AI Content Factory

## Repo
https://github.com/xAIcms/xaicms

## Done (2026-05-09)
- [x] Docker Compose 一键部署
- [x] 环境变量配置 (.env → config.php)
- [x] Git 初始化 + 首版推送
- [x] README EN 重写
- [x] 默认模型: deepseek-v4-flash / deepseek-v4-pro
- [x] 内置免费 AI 额度(10次)

## Next
- [ ] DEMO 视频 (30s)
- [ ] 插件 Hook 系统
- [ ] JSON REST API
- [ ] 模板市场
- [ ] 插件市场
- [ ] ProductHunt 发布
- [ ] xaicms.com 域名上线

## Architecture
- PHP 8.0+ / MySQL 8.0 / Docker
- MVC: src/Controllers src/Models src/Services
- Templates: templates/ (PHP直出)
- AI: OpenAI-compatible API
