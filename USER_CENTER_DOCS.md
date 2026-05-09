# 用户中心模块文档

本文档详细说明了 xAI CMS 用户中心模块的 API 接口、前端组件结构及数据库设计。

## 1. 功能概述

用户中心模块提供以下核心功能：
- **用户认证**：注册、登录、注销、会话管理。
- **个人中心**：仪表盘展示（注册时间、最近活动）。
- **资料管理**：修改昵称、头像（预留）。
- **安全设置**：修改密码。
- **活动日志**：记录用户登录、修改资料等操作。

## 2. 路由与 API 接口

所有路由均在 `public/index.php` 中定义，由 `src/Controllers/UserController.php` 处理。

### 认证接口

| 方法 | 路径 | 描述 | 参数/备注 |
|------|------|------|-----------|
| GET | `/login` | 登录页面 | 若已登录则跳转至 `/user/center` |
| POST | `/login` | 提交登录 | `email`, `password` |
| GET | `/register` | 注册页面 | 若已登录则跳转至 `/user/center` |
| POST | `/register` | 提交注册 | `name`, `email`, `password`, `confirm_password` |
| GET | `/logout` | 注销 | 销毁 Session 并跳转至 `/login` |

### 用户中心接口 (需登录)

所有 `/user/*` 路由均经过 `requireLogin()` 守卫验证。

| 方法 | 路径 | 描述 | 参数/备注 |
|------|------|------|-----------|
| GET | `/user/center` | 用户中心首页 | 展示用户信息和最近活动日志 |
| GET | `/user/profile` | 个人资料页 | 表单预填当前用户信息 |
| POST | `/user/profile` | 更新资料 | `name` |
| GET | `/user/security` | 安全设置页 | 修改密码表单 |
| POST | `/user/security` | 修改密码 | `current_password`, `new_password`, `confirm_password` |

## 3. 前端组件结构

前端模板位于 `templates/` 目录下，采用 Tailwind CSS 构建，适配移动端。

### 认证模板 (`templates/auth/`)
- **login.php**: 登录表单，包含 CSRF 保护和错误提示。
- **register.php**: 注册表单，包含密码一致性校验。

### 用户中心模板 (`templates/user/`)
- **layout.php**: 核心布局文件。
  - 包含响应式侧边栏 (Sidebar)。
  - 处理顶部导航栏和内容区域的结构。
  - 提供 `active_tab` 变量用于高亮当前菜单。
- **center.php**: 仪表盘视图。
  - 展示用户统计卡片。
  - 渲染活动日志表格 (`user_logs`)。
- **profile.php**: 资料修改表单。
- **security.php**: 密码修改表单。

### 公共组件 (`templates/partials/`)
- **navbar.php**: 
  - 集成了用户下拉菜单（头像/昵称）。
  - 未登录状态显示 登录/注册 按钮。
  - 移动端菜单同步集成了用户链接。

## 4. 数据库设计

### Users 表 (`users`)
在原有基础上增加了以下字段：
- `avatar`: VARCHAR(255) - 用户头像 URL
- `created_at`: DATETIME - 注册时间
- `updated_at`: DATETIME - 更新时间

### User Logs 表 (`user_logs`)
用于记录用户敏感操作。

```sql
CREATE TABLE `user_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `action` VARCHAR(50) NOT NULL, -- e.g., 'login', 'register', 'update_profile'
  `details` TEXT,                -- 操作详情
  `ip_address` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_created` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 5. 安全特性

- **密码存储**: 使用 `password_hash()` (Bcrypt) 进行哈希存储。
- **CSRF 防护**: 所有 POST 请求均验证 `csrf_token`。
- **会话劫持防护**: `session_start` 配置了 `cookie_httponly` 和 `cookie_samesite`。
- **XSS 防护**: 输出内容使用 `htmlspecialchars()` 转义。
- **路由守卫**: 未授权访问 `/user/*` 会被强制重定向至登录页。

## 6. 部署说明

请确保执行数据库迁移脚本以更新 Schema：

```bash
php upgrade_db.php
```
