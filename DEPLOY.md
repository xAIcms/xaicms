# xAI CMS 部署指南

本指南将帮助您将 xAI CMS (PHP 版) 部署到生产服务器。

## 1. 服务器环境要求

在开始之前，请确保您的服务器满足以下要求：

*   **操作系统**: Linux (推荐 Ubuntu 20.04/22.04, CentOS 7/8) 或 Windows Server
*   **Web 服务器**: Nginx (推荐) 或 Apache
*   **PHP 版本**: PHP 8.0 或更高版本
*   **数据库**: MySQL 8.0 或更高版本
*   **PHP 扩展**:
    *   `pdo_mysql` (必须)
    *   `mbstring` (必须)
    *   `curl` (必须)
    *   `json` (必须)
    *   `xml` (用于 Sitemap)
    *   `gd` 或 `imagick` (用于图片处理)

## 2. 文件上传与目录结构

将 `php_backend` 文件夹内的所有内容上传到您的网站根目录（例如 `/www/wwwroot/your-site.com`）。

推荐的最终目录结构：
```text
/www/wwwroot/your-site.com/
├── api/             # API 接口目录
├── public/          # Web 根目录 (Nginx/Apache 指向这里)
│   ├── assets/      # 静态资源 (CSS/JS/Images)
│   ├── install/     # 安装程序 (安装后需删除)
│   │   ├── index.php
│   │   └── install.php
│   ├── index.php    # 前台入口文件
│   └── .htaccess    # Apache 伪静态规则
├── src/             # 核心 PHP 代码
│   ├── Config/
│   ├── Controllers/
│   └── Models/
├── sql/             # 数据库脚本
├── templates/       # 前端模板文件
├── config.php       # 配置文件 (安装后生成)
└── DEPLOY.md        # 本文档
```

**注意**: 为了安全起见，建议将 Web 服务器的根目录 (`root` 或 `DocumentRoot`) 指向 `public` 目录，而不是项目根目录。这样可以防止敏感文件（如 `.env`, `src/`）被直接访问。

## 3. Web 服务器配置

### 方案 A: 宝塔面板 (BT Panel) - 推荐

如果您使用的是宝塔面板，请按以下步骤操作，无需手动编辑配置文件：

1.  **上传代码**: 将文件上传到网站根目录。
2.  **设置运行目录**:
    *   点击【网站设置】 -> 【网站目录】
    *   将 **运行目录** 选择为 `/public`，然后保存。
3.  **设置伪静态**:
    *   点击【网站设置】 -> 【伪静态】
    *   在输入框中填入以下代码并保存：
    ```nginx
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    ```

### 方案 B: Nginx (手动配置)

如果您手动管理 Nginx 配置文件，请参考以下配置（注意修改路径）：

```nginx
server {
    listen 80;
    server_name your-domain.com;
    
    # 修改为您的实际路径
    root /www/wwwroot/your-site.com/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # 处理 PHP 执行
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # 根据实际 PHP 版本调整
    }

    # 禁止访问隐藏文件
    location ~ /\.ht {
        deny all;
    }
}
```

### 方案 B: Apache

如果您使用 Apache，请确保已启用 `mod_rewrite` 模块。
项目 `public` 目录下已经为您准备好了 `.htaccess` 文件，通常无需额外配置。
只需确保 VirtualHost 配置中 `AllowOverride All` 已开启：

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /www/wwwroot/your-site.com/public
    <Directory /www/wwwroot/your-site.com/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 4. 目录权限设置

确保 Web 服务器用户 (通常是 `www-data` 或 `nginx`) 对项目目录有读取权限，并对根目录有写入权限（用于生成配置文件）。

```bash
# 示例命令 (Linux)
chown -R www-data:www-data /www/wwwroot/your-site.com
chmod -R 755 /www/wwwroot/your-site.com
```

## 5. 执行安装向导

1.  打开浏览器，访问安装程序入口：
    `http://your-domain.com/../install/index.php`
    *(注：如果您将 root 指向了 public，安装路径可能是 `http://your-domain.com/../install/index.php`，或者您需要临时将 root 指向项目根目录进行安装，安装后再改回 public)*

    **更简单的做法**：
    直接访问 `http://your-domain.com/install/index.php` (前提是您没有将 root 指向 public，或者您临时配置了 install 目录的访问权限)。

    *如果您的 root 指向了 `public`，您可以通过 `http://your-domain.com/index.php` 访问前台，但安装程序位于上一级目录。建议在安装阶段，暂时将 root 设置为项目根目录，安装完成后再修改为 `public`。*

2.  按照页面提示：
    *   检查环境监测是否全部通过。
    *   输入 MySQL 数据库信息。
    *   设置管理员账号。
    *   点击“开始安装”。

## 6. 安装后收尾

1.  **安全清理**: 安装成功后，请务必删除 `install` 目录！
    ```bash
    rm -rf install/
    ```
2.  **配置保护**: 将生成的 `config.php` 设置为只读。
    ```bash
    chmod 444 config.php
    ```
3.  **配置调整**: 如果您之前为了安装修改了 Nginx root 目录，现在请将其改回 `/public` 并重启 Nginx。

## 7. 常见问题

*   **页面 404**: 检查 Nginx 的 `try_files` 配置或 Apache 的 `.htaccess` 是否生效。
*   **数据库连接错误**: 检查 `config.php` 中的数据库信息是否正确，以及服务器防火墙是否允许连接 3306 端口。
*   **权限错误**: 确保 PHP 进程有权限写入 `config.php`。
