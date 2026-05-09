<p align="center">
  <h1>xAI CMS</h1>
  <p><strong>AI Content Factory</strong> — Batch generate, manage, and distribute global content with AI.</p>
</p>

<p align="center">
  <a href="#quick-start">Quick Start</a> ·
  <a href="#features">Features</a> ·
  <a href="#docker">Docker</a> ·
  <a href="#ai-models">AI Models</a> ·
  <a href="#roadmap">Roadmap</a>
</p>

---

## What is xAI CMS?

Not just another CMS. **xAI CMS is an AI-native content factory.**

Tell it what you need — *"Generate 20 SEO articles about sustainable fashion in English and Japanese"* — and it dispatches AI models to write, optimize, and publish them. Multi-language, multi-region, SEO-ready.

Think WordPress + Jasper + DeepL, but built-in.

> **Status**: Early stage. Actively developed. Stars & contributions welcome.

---

## Quick Start

```bash
# Clone
git clone https://github.com/xAIcms/xAIcms.git
cd xAIcms

# Docker (recommended)
docker-compose up -d

# Open http://localhost:8080
```

**That's it.** MySQL and PHP are handled by Docker. No manual setup needed.

> **Demo video coming soon.**

---

## Features

- **AI Batch Generation** — One prompt → N articles. Specify language, region, keywords.
- **Multi-Region / Multi-Language** — Built-in support for 17 regions and 15 languages.
- **SEO Automation** — Auto sitemap, meta tags, spider logs, full-text search index.
- **AI Model Management** — Connect any OpenAI-compatible API. Manage multiple models in admin panel.
- **Point System** — Credit-based AI usage with recharge packages.
- **Plugin Architecture** *(coming)* — WordPress-style hooks for extensibility.
- **Template Marketplace** *(coming)* — One-click theme install.

---

## Docker

```yaml
# docker-compose.yml
services:
  app:
    build: .
    ports: ["8080:80"]
    environment:
      AI_API_KEY: "sk-your-key"       # Set your API key
      AI_MODEL: "deepseek-v4-flash"   # Lite model
      AI_PRO_MODEL: "deepseek-v4-pro" # Pro model
  db:
    image: mysql:8.0
```

Set `AI_API_KEY` to your own key, or leave empty and configure in admin panel after install.

**Default models**: DeepSeek V4 Flash (fast) + DeepSeek V4 Pro (powerful).

---

## AI Models

| Tier | Model | Use |
|---|---|---|
| **Flash** | deepseek-v4-flash | Daily content, fast generation |
| **Pro** | deepseek-v4-pro | Deep analysis, complex writing |

Supports any OpenAI-compatible API. Switch to GPT-4o, Claude, Gemini, or your own models.

**Free quota**: New installs get 10 free AI generations. After that, bring your own API key or use our token relay service.

---

## Tech Stack

- **Backend**: PHP 8.0+ (no framework — lightweight MVC)
- **Database**: MySQL 8.0+
- **AI**: OpenAI-compatible API (DeepSeek, OpenAI, Anthropic, etc.)
- **Deploy**: Docker, Nginx/Apache, 宝塔面板

---

## Roadmap

- [x] Article CRUD + Categories + Tags
- [x] Multi-region + multi-language
- [x] AI batch generation (schemes)
- [x] Point system + recharge
- [x] Docker deployment
- [ ] Plugin system (WordPress hooks)
- [ ] JSON REST API
- [ ] Template marketplace
- [ ] Plugin marketplace
- [ ] CLI scaffolding (`php xai make:plugin`)

---

## Why Open Source?

We believe content creation is being fundamentally changed by AI. The tools should be open, composable, and free to self-host. xAI CMS is MIT-licensed and always will be.

We make money from our optional [token relay service](https://xaicms.com) — if you don't want to manage your own API keys, use ours. Otherwise, it's free forever.

---

## Contributing

Pull requests welcome. See [CONTRIBUTING.md](CONTRIBUTING.md) (coming soon).

1. Fork
2. Create feature branch
3. Commit
4. Push
5. Open PR

---

## License

MIT — free for personal and commercial use.

---

<p align="center">
  <sub>Built with ❤️ by the xAIcms team. Stars make our day ⭐</sub>
</p>
