<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录 - xAI CMS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-login">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4 text-white">
                    <i class="bi bi-globe2 display-3 mb-3 d-block"></i>
                    <h2 class="fw-bold">xAI CMS</h2>
                    <p class="text-white-50">高效 · 智能 · 全球化</p>
                </div>
                
                <div class="card login-card overflow-hidden">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-dark">管理员登录</h4>
                            <p class="text-muted small">请输入您的凭证以继续</p>
                        </div>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-10 text-danger" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/admin/login" class="needs-validation" novalidate>
                            <?php echo Csrf::input(); ?>
                            <div class="mb-4">
                                <label for="email" class="form-label text-secondary small fw-bold text-uppercase">邮箱账号</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" id="email" name="email" class="form-control" required autofocus placeholder="name@example.com">
                                </div>
                            </div>
                            <div class="mb-5">
                                <label for="password" class="form-label text-secondary small fw-bold text-uppercase">密码</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" id="password" name="password" class="form-control" required placeholder="请输入密码">
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> 登 录
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-light bg-opacity-50 text-center py-3 border-top border-light">
                        <small class="text-muted">© <?php echo date('Y'); ?> xAI CMS. All rights reserved.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
