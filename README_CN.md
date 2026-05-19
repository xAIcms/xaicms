<p align="center">
  <h1>xAI CMS</h1>
  <p><strong>AI 内容工厂</strong> — 用 AI 批量生成、管理、分发全球化内容</p>
</p>

<p align="center">
  <a href="#快速开始">快速开始</a> ·
  <a href="#功能">功能</a> ·
  <a href="#docker-部署">Docker 部署</a> ·
  <a href="#插件开发">插件开发</a> ·
  <a href="#路线图">路线图</a>
</p>

---

## 这是什么

不是又一个 CMS。**xAI CMS 是一个 AI 原生的内容工厂。**

告诉它你要什么 —— *"帮我生成 20 篇关于可持续时尚的英文和日文 SEO 文章"* —— 它自动调度 AI 模型完成写作、优化、发布。多语言、多区域、SEO 一条龙。

相当于 WordPress + Jasper + DeepL，但内置在同一个系统里。

> **当前状态**：早期开发阶段，欢迎 Star 和贡献。

---

## 快速开始

```bash
git clone https://github.com/xAIcms/xaicms.git
cd xaicms
docker-compose up -d
# 打开 http://localhost:8080
```

搞定。MySQL 和 PHP 环境全都装在 Docker 里，不需要手动配置。

---

## 功能

### CMS 基础
- **文章管理** — 富文本编辑器（UEditor），手动编写或 AI 批量生成
- **分类和标签** — 树形分类 + 标签管理
- **多媒体库** — 图片上传和管理
- **草稿/发布** — 标准内容工作流

### AI 能力
- **批量内容生成** — 一个指令 → N 篇多语言 SEO 文章
- **多区域多语言** — 内置 17 个区域、15 种语言
- **模型管理** — 接入任何 OpenAI 兼容 API，支持多模型切换
- **DeepSeek 默认** — 开箱支持 deepseek-v4-flash（快速）和 deepseek-v4-pro（强力）

### SEO 工具
- **自动 Sitemap** — 自动生成并更新
- **爬虫日志** — 记录 Googlebot、GPTBot 等访问
- **全文搜索** — MySQL 全文索引
- **RSS 输出** — 标准 RSS 2.0
- **SEO Meta** — 每篇文章独立 SEO 标题/描述/关键词

### 扩展系统
- **插件系统** — WordPress 风格的 Hook（add_action / add_filter）
- **模板系统** — 扫描、预览、一键切换，支持 template.json 元数据
- **在线更新** — 一键检查 GitHub Release，自动下载安装

### 运营管理
- **用户系统** — 注册、登录、角色管理
- **积分充值** — 积分包 + 充值订单
- **公告系统** — 平台公告和功能更新
- **数据统计** — 文章数、浏览量、API 调用量

---

## Docker 部署

```yaml
# docker-compose.yml
services:
  app:
    build: .
    ports: ["8080:80"]
    environment:
      AI_API_KEY: "sk-your-key"        # 你的 API Key
      AI_MODEL: "deepseek-v4-flash"    # 快速模型
      AI_PRO_MODEL: "deepseek-v4-pro"  # 强力模型
  db:
    image: mysql:8.0
```

设置 `AI_API_KEY` 后即可使用 AI 功能。支持任何 OpenAI 兼容的 API（DeepSeek、OpenAI、Claude、火山引擎等）。

也支持宝塔面板、1Panel、手动 Nginx 部署，详见 [DEPLOY.md](DEPLOY.md)。

---

## AI 模型

| 级别 | 模型 | 用途 |
|---|---|---|
| **Flash** | deepseek-v4-flash | 日常内容，快速生成 |
| **Pro** | deepseek-v4-pro | 深度分析，复杂写作 |

支持任何 OpenAI 兼容 API。可以随时切换 GPT-4o、Claude、Gemini 或自建模型。

---

## 技术栈

- **后端**：PHP 8.0+（手写轻量 MVC，无框架）
- **数据库**：MySQL 8.0+
- **AI**：OpenAI 兼容 API
- **部署**：Docker、Nginx/Apache、宝塔面板
- **前端**：Bootstrap 5、UEditor 富文本

**16MB 核心代码，零依赖框架。**

---

## 插件开发

每个插件就是 `plugins/` 下的一个文件夹，里面一个 `plugin.php`：

```php
<?php
/**
 * Plugin Name: 我的插件
 * Description: 在页面底部加一句话
 * Version: 1.0.0
 * Author: 你的名字
 */

add_action('before_footer', function() {
    echo '<p>Powered by xAI CMS</p>';
});
```

现有 Hook 点：
- `article_saved` — 文章保存后
- `before_footer` — 页面底部
- `admin_dashboard_widgets` — 后台仪表盘小组件
- `before_output` — 页面输出前（开发中）

---

## 为什么开源

AI 正在彻底改变内容创作的方式。我们认为这些工具应该是开放的、可组合的、可以自由自托管的。xAI CMS 基于 MIT 协议，永久免费。

---

## 路线图

- [x] 文章 CRUD + 分类 + 标签
- [x] 多区域 + 多语言
- [x] AI 批量内容生成
- [x] 积分系统 + 充值
- [x] Docker 部署
- [x] 插件系统（WordPress 风格 Hook）
- [x] 模板管理
- [x] 在线更新
- [ ] JSON REST API
- [ ] 模板市场
- [ ] 插件市场
- [ ] CLI 脚手架（`php xai make:plugin`）

---

## 参与贡献

欢迎 PR。详见 [CONTRIBUTING.md](CONTRIBUTING.md)。

1. Fork
2. 建分支
3. 提交
4. Push
5. 开 PR

---

## 协议

MIT — 个人和商业用途均免费。

---

<p align="center">
  <sub>Made with ❤️ by <a href="https://github.com/xAIcms">xAIcms</a>. ⭐ Star us on GitHub!</sub>
</p>
