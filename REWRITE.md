# xAI CMS 伪静态规则设置

为了确保 xAI CMS 的路由功能正常工作（如文章页、分类页的 URL 美化），您必须配置 Web 服务器的伪静态（Rewrite）规则。

## 1. Nginx 配置 (推荐)

如果您使用的是 Nginx 或 **宝塔面板 (BT Panel)**，请使用以下规则。

### 宝塔面板设置方法：
1. 登录宝塔面板，点击“网站”。
2. 点击您的网站对应的“设置”。
3. 在左侧菜单选择“伪静态”。
4. 将下方代码复制并粘贴到输入框中，点击“保存”。

### Nginx 规则代码：

```nginx
location / {
    if (!-e $request_filename){
        rewrite  ^(.*)$  /index.php?s=$1  last;   break;
    }
}
```

或者使用更标准的 `try_files` 写法 (推荐):

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

**注意**: 请确保您的网站运行目录（Root Directory）已设置为 `/public`。

---

## 2. Apache 配置

如果您使用的是 Apache 服务器，项目 `public` 目录下已经包含了一个 `.htaccess` 文件。

通常情况下，只要您的 Apache 开启了 `mod_rewrite` 模块，且 `AllowOverride` 设置为 `All`，则无需额外配置。

### .htaccess 内容参考：

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # 处理 Authorization 头
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # 移除 URL 末尾的斜杠
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # 前端控制器路由
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## 3. IIS 配置 (Windows Server)

如果您使用的是 IIS，请在网站根目录下创建 `web.config` 文件：

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Imported Rule 1" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```
