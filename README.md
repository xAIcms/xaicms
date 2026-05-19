<p align="center">
  <h1>xAI CMS</h1>
  <p><strong>AI Content Factory</strong> — Batch generate multilingual SEO content with AI. One prompt, global content.</p>
  <p><a href="https://xaicms.com">🌐 xaicms.com</a></p>
</p>

<p align="center">
  <a href="README_CN.md">简体中文</a> · 
  <a href="#quick-start">Quick Start</a> ·
  <a href="#features">Features</a> ·
  <a href="#monetization">Monetization</a> ·
  <a href="#roadmap">Roadmap</a>
</p>

---

## What is xAI CMS?

**An open-source AI content factory.** Tell it what you need — *"Generate 20 SEO articles about solar panels in English, Japanese, and German"* — and it dispatches AI models to write, optimize, and publish. Multi-language, multi-region, SEO-ready.

Think WordPress + Jasper + DeepL, all in one self-hosted package.

> **Live demo**: [xaicms.com](https://xaicms.com) | **Admin**: admin@xaicms.com / admin123

---

## Quick Start

```bash
git clone https://github.com/xAIcms/xaicms.git
cd xaicms
docker-compose up -d
# Open http://localhost:8080
```

MySQL and PHP included in Docker. Zero config needed.

Set `AI_API_KEY` in `docker-compose.yml` to enable AI features. Supports DeepSeek, OpenAI, Claude, and any OpenAI-compatible API.

---

## Features

### CMS
- Article CRUD with rich text editor (UEditor)
- Categories, tags, media library
- Draft/publish workflow

### AI Content Generation
- Batch generate N articles from a single prompt
- 14 languages + 17 regions
- Multi-model support (DeepSeek/OpenAI/Claude)
- AI scheme management (topic → articles)

### SEO Toolkit
- Auto sitemap.xml + RSS feed
- hreflang tags for multilingual sites
- Schema.org BreadcrumbList JSON-LD
- Spider/bot visit analytics
- Per-article SEO meta fields

### Upgrade & Monetization
- Three-tier plans: Free / Pro / Enterprise
- Feature gating per plan level
- WeChat Pay V3 integration
- Developer revenue sharing (default 70%)

### Plugin Marketplace
- WordPress-style Hook system (7 actions + 1 filter)
- 10 built-in plugins (SEO analyzer, auto-translate, social share, newsletter, points, SMS, etc.)
- Activate/deactivate from admin panel

### Template System
- Scan, preview, switch templates
- template.json metadata format
- Built-in default + blog themes

### Developer Center
- Developer registration + profile
- Revenue dashboard + withdrawal to WeChat
- Full developer documentation

### User & Admin
- User registration/login (email + phone)
- Points system + SMS verification
- Admin dashboard with version update notifications
- Online update from GitHub Releases

---

## Tech Stack

- **Backend**: PHP 8.0+ (hand-written MVC, zero framework)
- **Database**: MySQL 8.0
- **AI**: OpenAI-compatible API
- **Deploy**: Docker, Nginx/Apache, BT Panel
- **Frontend**: Bootstrap 5, Tailwind CSS, UEditor

**16MB core code, zero framework dependencies.**

---

## Monetization (for SaaS operators)

xAI CMS is MIT-licensed and free forever for self-hosting. For SaaS operators:

1. **Subscription plans** — Free / Pro ($29/mo) / Enterprise ($99/mo)
2. **API proxy markup** — Charge a margin on AI API calls
3. **Plugin/Template marketplace** — Revenue share with developers
4. **SEO agency** — Use xAI CMS to deliver content services

---

## Roadmap

- [x] Article CRUD + categories + tags
- [x] Multi-region + multi-language (14 languages)
- [x] AI batch content generation
- [x] Points system + SMS verification
- [x] Docker deployment
- [x] WordPress-style Hook plugin system
- [x] Template management + marketplace
- [x] Online update (GitHub Release)
- [x] Upgrade plans (Free/Pro/Enterprise)
- [x] WeChat Pay integration
- [x] Developer revenue sharing
- [x] Developer documentation
- [ ] JSON REST API
- [ ] Plugin marketplace (public)
- [ ] Template marketplace (public)
- [ ] CLI scaffolding (`php xai make:plugin`)

---

## License

MIT — free for personal and commercial use.

---

<p align="center">
  <sub>Built with ❤️ by <a href="https://github.com/xAIcms">xAIcms</a>. ⭐ Star us on GitHub!</sub>
</p>
