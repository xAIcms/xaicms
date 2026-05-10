<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xAI CMS 安装向导</title>
    <link href="/assets/css/install.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>xAI CMS 安装向导</h1>
            <p>PHP + MySQL 高性能内容管理系统</p>
        </div>
        
        <div class="content">
            <!-- Step 1: 环境检测 -->
            <div id="step1" class="step active">
                <h2 style="margin-top:0">环境检测</h2>
                <div id="env-checks">
                    <p>正在检测服务器环境...</p>
                </div>
                <div style="margin-top: 24px;">
                    <button id="btn-step1" class="btn btn-primary" onclick="nextStep(2)" disabled>下一步：配置数据库</button>
                </div>
            </div>

            <!-- Step 2: 数据库配置 -->
            <div id="step2" class="step">
                <h2 style="margin-top:0">数据库配置</h2>
                <form id="db-form">
                    <div class="form-group">
                        <label>数据库主机 (Host)</label>
                        <input type="text" name="db_host" class="form-control" value="127.0.0.1" required>
                    </div>
                    <div class="form-group">
                        <label>端口 (Port)</label>
                        <input type="number" name="db_port" class="form-control" value="3306" required>
                    </div>
                    <div class="form-group">
                        <label>数据库名 (Database)</label>
                        <input type="text" name="db_name" class="form-control" value="geopulse_cms" required>
                    </div>
                    <div class="form-group">
                        <label>用户名 (User)</label>
                        <input type="text" name="db_user" class="form-control" value="root" required>
                    </div>
                    <div class="form-group">
                        <label>密码 (Password)</label>
                        <input type="password" name="db_pass" class="form-control" placeholder="数据库密码">
                    </div>
                </form>
                <div style="margin-top: 24px;">
                    <button id="btn-test-db" class="btn btn-primary" onclick="testDatabase()">测试连接并继续</button>
                </div>
            </div>

            <!-- Step 3: 管理员设置 -->
            <div id="step3" class="step">
                <h2 style="margin-top:0">管理员设置</h2>
                <form id="admin-form">
                    <div class="form-group">
                        <label>Site Language / 站点语言</label>
                        <select name="site_language" class="form-control" style="width:100%;padding:10px;">
                            <option value="zh-CN">简体中文 (zh-CN)</option>
                            <option value="en-US" selected>English (en-US)</option>
                            <option value="zh-TW">繁体中文 (zh-TW)</option>
                            <option value="ja-JP">日本語 (ja-JP)</option>
                            <option value="ko-KR">한국어 (ko-KR)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Admin Panel Language / 后台管理语言</label>
                        <select name="admin_language" class="form-control" style="width:100%;padding:10px;">
                            <option value="zh-CN" selected>简体中文 (zh-CN)</option>
                            <option value="en-US">English (en-US)</option>
                            <option value="zh-TW">繁体中文 (zh-TW)</option>
                            <option value="ja-JP">日本語 (ja-JP)</option>
                            <option value="ko-KR">한국어 (ko-KR)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>管理员邮箱</label>
                        <input type="email" name="admin_email" class="form-control" value="admin@example.com" required>
                    </div>
                    <div class="form-group">
                        <label>管理员密码</label>
                        <input type="password" name="admin_pass" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>确认密码</label>
                        <input type="password" name="admin_pass_confirm" class="form-control" required>
                    </div>
                </form>
                <div style="margin-top: 24px;">
                    <button id="btn-install" class="btn btn-primary" onclick="startInstall()">开始安装</button>
                </div>
            </div>

            <!-- Step 4: 安装进度 -->
            <div id="step4" class="step">
                <h2 style="margin-top:0">正在安装...</h2>
                <div id="install-log" class="progress-log"></div>
                <div id="install-result" style="display:none; text-align: center;">
                    <h3 style="color: var(--success)">安装成功！</h3>
                    <p>配置文件已生成，数据库已初始化。</p>
                    <p style="color: var(--error); font-size: 13px;">请务必删除 install 目录以确保安全！</p>
                    <a href="/" class="btn btn-primary" style="margin-top: 16px; text-decoration: none;">进入首页</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // API 路径
        const API_URL = 'install.php';

        // 初始化
        window.addEventListener('load', () => {
            checkEnv();
        });

        function showStep(step) {
            document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
        }

        function nextStep(step) {
            showStep(step);
        }

        async function checkEnv() {
            const container = document.getElementById('env-checks');
            const btn = document.getElementById('btn-step1');
            
            try {
                const res = await fetch(API_URL + '?action=check_env');
                const data = await res.json();
                
                let html = '';
                let allOk = true;

                // PHP Version
                html += renderCheckItem('PHP Version >= 8.0', data.php_version.current, data.php_version.ok);
                if (!data.php_version.ok) allOk = false;

                // Extensions
                for (const [ext, ok] of Object.entries(data.extensions)) {
                    html += renderCheckItem(`Extension: ${ext}`, ok ? '已安装' : '未安装', ok);
                    if (!ok) allOk = false;
                }

                // Write Permission
                html += renderCheckItem('Config Write Permission', data.write_permission ? '可写' : '不可写', data.write_permission);
                if (!data.write_permission) allOk = false;

                container.innerHTML = html;
                btn.disabled = !allOk;

            } catch (e) {
                container.innerHTML = `<div class="check-item" style="color:red">无法连接到服务器，请确保已启动 PHP 服务。<br>${e.message}</div>`;
            }
        }

        function renderCheckItem(label, value, ok) {
            return `
                <div class="check-item">
                    <span>${label}</span>
                    <span class="status-badge ${ok ? 'status-ok' : 'status-err'}">
                        ${value}
                    </span>
                </div>
            `;
        }

        async function testDatabase() {
            const btn = document.getElementById('btn-test-db');
            const form = new FormData(document.getElementById('db-form'));
            
            btn.disabled = true;
            btn.textContent = '正在连接...';

            try {
                const res = await fetch(API_URL + '?action=test_db', {
                    method: 'POST',
                    body: form
                });
                const data = await res.json();

                if (data.ok) {
                    nextStep(3);
                } else {
                    alert('数据库连接失败: ' + data.error);
                }
            } catch (e) {
                alert('请求失败: ' + e.message);
            } finally {
                btn.disabled = false;
                btn.textContent = '测试连接并继续';
            }
        }

        async function startInstall() {
            const dbForm = new FormData(document.getElementById('db-form'));
            const adminForm = new FormData(document.getElementById('admin-form'));
            
            if (adminForm.get('admin_pass') !== adminForm.get('admin_pass_confirm')) {
                alert('两次输入的密码不一致');
                return;
            }

            // Merge forms
            for (let [key, value] of adminForm.entries()) {
                dbForm.append(key, value);
            }

            nextStep(4);
            const log = document.getElementById('install-log');
            
            function addLog(msg, type = 'info') {
                const div = document.createElement('div');
                div.className = 'progress-line';
                if (type === 'success') div.classList.add('progress-success');
                if (type === 'error') div.classList.add('progress-error');
                div.textContent = `> ${msg}`;
                log.appendChild(div);
                log.scrollTop = log.scrollHeight;
            }

            addLog('开始安装...');

            try {
                const res = await fetch(API_URL + '?action=install', {
                    method: 'POST',
                    body: dbForm
                });
                const data = await res.json();

                if (data.logs) {
                    data.logs.forEach(l => addLog(l));
                }

                if (data.ok) {
                    addLog('安装完成！', 'success');
                    document.getElementById('install-result').style.display = 'block';
                } else {
                    addLog('安装失败: ' + data.error, 'error');
                }
            } catch (e) {
                addLog('发生致命错误: ' + e.message, 'error');
            }
        }
    </script>
</body>
</html>
