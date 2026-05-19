<?php
$title = '系统设置';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800 fw-bold">系统设置</h1>
        <p class="text-muted mb-0">
            <i class="bi bi-gear me-1"></i> 管理网站全局配置、SEO、社交媒体及外观
        </p>
    </div>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-4 text-success"></i>
            <div>
                <strong>操作成功</strong>
                <div class="small">设置已成功保存并生效。</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-4 text-danger"></i>
            <div>
                <strong>操作失败</strong>
                <div class="small"><?php echo $error; ?></div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/settings" id="settingsForm">
    <?php echo Csrf::input(); ?>
    <div class="row g-4">
        <!-- 左侧导航 -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2">
                    <div class="nav flex-column nav-pills" id="settingsTabs" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start py-3 px-3 d-flex align-items-center mb-1" id="basic-tab" data-bs-toggle="pill" data-bs-target="#basic" type="button" role="tab">
                            <i class="bi bi-sliders me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">基础设置</div>
                                <div class="small opacity-50">站点名称、URL、页脚</div>
                            </div>
                        </button>
                        <button class="nav-link text-start py-3 px-3 d-flex align-items-center mb-1" id="payment-tab" data-bs-toggle="pill" data-bs-target="#payment" type="button" role="tab">
                            <i class="bi bi-credit-card-2-front me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">支付设置</div>
                                <div class="small opacity-50">收款码、银行账户配置</div>
                            </div>
                        </button>
                        <button class="nav-link text-start py-3 px-3 d-flex align-items-center mb-1" id="sms-tab" data-bs-toggle="pill" data-bs-target="#sms" type="button" role="tab">
                            <i class="bi bi-chat-dots me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">短信配置</div>
                                <div class="small opacity-50">腾讯云短信服务</div>
                            </div>
                        </button>
                        <button class="nav-link text-start py-3 px-3 d-flex align-items-center mb-1" id="seo-tab" data-bs-toggle="pill" data-bs-target="#seo" type="button" role="tab">
                            <i class="bi bi-search me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">SEO 设置</div>
                                <div class="small opacity-50">Meta信息、Sitemap、Robots</div>
                            </div>
                        </button>
                        <button class="nav-link text-start py-3 px-3 d-flex align-items-center mb-1" id="social-tab" data-bs-toggle="pill" data-bs-target="#social" type="button" role="tab">
                            <i class="bi bi-share me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">社交媒体</div>
                                <div class="small opacity-50">各平台账号链接配置</div>
                            </div>
                        </button>
                        <button class="nav-link text-start py-3 px-3 d-flex align-items-center mb-1" id="compliance-tab" data-bs-toggle="pill" data-bs-target="#compliance" type="button" role="tab">
                            <i class="bi bi-shield-check me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">合规与备案</div>
                                <div class="small opacity-50">ICP备案、公安备案</div>
                            </div>
                        </button>
                        <button class="nav-link text-start py-3 px-3 d-flex align-items-center mb-1" id="ai-tab" data-bs-toggle="pill" data-bs-target="#ai" type="button" role="tab">
                            <i class="bi bi-cpu me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">AI 设置</div>
                                <div class="small opacity-50">模型参数、API配置</div>
                            </div>
                        </button>
                        <button class="nav-link text-start py-3 px-3 d-flex align-items-center" id="custom-tab" data-bs-toggle="pill" data-bs-target="#custom" type="button" role="tab">
                            <i class="bi bi-palette me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">外观与自定义</div>
                                <div class="small opacity-50">Favicon、CSS、JS注入</div>
                            </div>
                        </button>
                        <button class="nav-link text-start py-3 px-3 d-flex align-items-center" id="pages-tab" data-bs-toggle="pill" data-bs-target="#pages" type="button" role="tab">
                            <i class="bi bi-file-earmark-text me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">静态页面</div>
                                <div class="small opacity-50">关于我们、隐私政策、服务条款</div>
                            </div>
                        </button>
                        <button class="nav-link text-start py-3 px-3 d-flex align-items-center" id="homepage-tab" data-bs-toggle="pill" data-bs-target="#homepage" type="button" role="tab">
                            <i class="bi bi-house-heart me-2 fs-5"></i>
                            <div>
                                <div class="fw-medium">首页布局</div>
                                <div class="small opacity-50">首页内容区、CTA、落地页</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 右侧内容 -->
        <div class="col-md-9">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="tab-content" id="settingsTabsContent">
                        <!-- 基础设置 -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">基础设置</h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">站点名称</label>
                                    <input type="text" name="settings[siteName]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['siteName'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">站点 URL</label>
                                    <input type="url" name="settings[siteUrl]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['siteUrl'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">管理员邮箱</label>
                                    <input type="email" name="settings[adminEmail]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['adminEmail'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">每页文章数</label>
                                    <input type="number" name="settings[articlesPerPage]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['articlesPerPage'] ?? '10'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Site Language</label>
                                    <select name="settings[language]" class="form-select bg-light border-0">
                                        <option value="en-US" <?php echo ($settings['language'] ?? '') === 'en-US' ? 'selected' : ''; ?>>English (en-US)</option>
                                        <option value="ja-JP" <?php echo ($settings['language'] ?? '') === 'ja-JP' ? 'selected' : ''; ?>>日本語 (ja-JP)</option>
                                        <option value="ko-KR" <?php echo ($settings['language'] ?? '') === 'ko-KR' ? 'selected' : ''; ?>>한국어 (ko-KR)</option>
                                        <option value="de-DE" <?php echo ($settings['language'] ?? '') === 'de-DE' ? 'selected' : ''; ?>>Deutsch (de-DE)</option>
                                        <option value="fr-FR" <?php echo ($settings['language'] ?? '') === 'fr-FR' ? 'selected' : ''; ?>>Français (fr-FR)</option>
                                        <option value="es-ES" <?php echo ($settings['language'] ?? '') === 'es-ES' ? 'selected' : ''; ?>>Español (es-ES)</option>
                                        <option value="ru-RU" <?php echo ($settings['language'] ?? '') === 'ru-RU' ? 'selected' : ''; ?>>Русский (ru-RU)</option>
                                        <option value="pt-BR" <?php echo ($settings['language'] ?? '') === 'pt-BR' ? 'selected' : ''; ?>>Português (pt-BR)</option>
                                        <option value="ar-SA" <?php echo ($settings['language'] ?? '') === 'ar-SA' ? 'selected' : ''; ?>>العربية (ar-SA)</option>
                                        <option value="th-TH" <?php echo ($settings['language'] ?? '') === 'th-TH' ? 'selected' : ''; ?>>ไทย (th-TH)</option>
                                        <option value="vi-VN" <?php echo ($settings['language'] ?? '') === 'vi-VN' ? 'selected' : ''; ?>>Tiếng Việt (vi-VN)</option>
                                        <option value="id-ID" <?php echo ($settings['language'] ?? '') === 'id-ID' ? 'selected' : ''; ?>>Bahasa Indonesia (id-ID)</option>
                                        <option value="zh-CN" <?php echo ($settings['language'] ?? '') === 'zh-CN' ? 'selected' : ''; ?>>简体中文 (zh-CN)</option>
                                        <option value="zh-TW" <?php echo ($settings['language'] ?? '') === 'zh-TW' ? 'selected' : ''; ?>>繁體中文 (zh-TW)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Admin Language</label>
                                    <select name="settings[admin_language]" class="form-select bg-light border-0">
                                        <option value="zh-CN" <?php echo ($settings['admin_language'] ?? '') === 'zh-CN' ? 'selected' : ''; ?>>简体中文 (zh-CN)</option>
                                        <option value="en-US" <?php echo ($settings['admin_language'] ?? '') === 'en-US' ? 'selected' : ''; ?>>English (en-US)</option>
                                        <option value="ja-JP" <?php echo ($settings['admin_language'] ?? '') === 'ja-JP' ? 'selected' : ''; ?>>日本語 (ja-JP)</option>
                                        <option value="ko-KR" <?php echo ($settings['admin_language'] ?? '') === 'ko-KR' ? 'selected' : ''; ?>>한국어 (ko-KR)</option>
                                        <option value="de-DE" <?php echo ($settings['admin_language'] ?? '') === 'de-DE' ? 'selected' : ''; ?>>Deutsch (de-DE)</option>
                                        <option value="fr-FR" <?php echo ($settings['admin_language'] ?? '') === 'fr-FR' ? 'selected' : ''; ?>>Français (fr-FR)</option>
                                        <option value="es-ES" <?php echo ($settings['admin_language'] ?? '') === 'es-ES' ? 'selected' : ''; ?>>Español (es-ES)</option>
                                        <option value="ru-RU" <?php echo ($settings['admin_language'] ?? '') === 'ru-RU' ? 'selected' : ''; ?>>Русский (ru-RU)</option>
                                        <option value="pt-BR" <?php echo ($settings['admin_language'] ?? '') === 'pt-BR' ? 'selected' : ''; ?>>Português (pt-BR)</option>
                                        <option value="ar-SA" <?php echo ($settings['admin_language'] ?? '') === 'ar-SA' ? 'selected' : ''; ?>>العربية (ar-SA)</option>
                                        <option value="th-TH" <?php echo ($settings['admin_language'] ?? '') === 'th-TH' ? 'selected' : ''; ?>>ไทย (th-TH)</option>
                                        <option value="vi-VN" <?php echo ($settings['admin_language'] ?? '') === 'vi-VN' ? 'selected' : ''; ?>>Tiếng Việt (vi-VN)</option>
                                        <option value="id-ID" <?php echo ($settings['admin_language'] ?? '') === 'id-ID' ? 'selected' : ''; ?>>Bahasa Indonesia (id-ID)</option>
                                        <option value="zh-TW" <?php echo ($settings['admin_language'] ?? '') === 'zh-TW' ? 'selected' : ''; ?>>繁體中文 (zh-TW)</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">页脚版权信息</label>
                                    <input type="text" name="settings[footerCopyright]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['footerCopyright'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-light rounded-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="settings[showLandingAsHomepage]" value="0">
                                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="showLandingAsHomepage" name="settings[showLandingAsHomepage]" value="1" <?php echo !empty($settings['showLandingAsHomepage']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-medium cursor-pointer" for="showLandingAsHomepage">将落地页显示为首页</label>
                                    <div class="form-text text-muted mt-1">开启后，访问网站根目录将显示落地页；关闭后将显示新闻首页。</div>
                                </div>
                            </div>

                            <!-- 保存按钮 -->
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    <i class="bi bi-save me-1"></i> 保存设置
                                </button>
                            </div>
                        </div>

                        <!-- 支付设置 -->
                        <div class="tab-pane fade" id="payment" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">支付设置</h5>
                            
                            <div class="row g-4">
                                <!-- 银行转账信息 -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-bank me-2"></i>银行对公汇款信息</h6>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium">开户名</label>
                                            <input type="text" name="settings[bank_account_name]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['bank_account_name'] ?? ''); ?>" placeholder="例如：天为众享科技（天津）有限公司">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium">银行账号</label>
                                            <input type="text" name="settings[bank_account_number]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['bank_account_number'] ?? ''); ?>" placeholder="例如：12050162660100000255">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium">开户行</label>
                                            <input type="text" name="settings[bank_name]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['bank_name'] ?? ''); ?>" placeholder="例如：中国建设银行股份有限公司天津空港物流加工区支行">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12"><hr class="text-muted"></div>

                                <!-- 微信支付 -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-wechat me-2"></i>微信支付</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">收款二维码 URL</label>
                                        <input type="text" name="settings[wechat_pay_image]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['wechat_pay_image'] ?? ''); ?>" placeholder="请输入图片链接">
                                        <div class="form-text">请在“媒体库”上传图片后复制链接填入</div>
                                    </div>
                                </div>

                                <!-- 支付宝支付 -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-alipay me-2"></i>支付宝支付</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">收款二维码 URL</label>
                                        <input type="text" name="settings[alipay_image]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['alipay_image'] ?? ''); ?>" placeholder="请输入图片链接">
                                        <div class="form-text">请在“媒体库”上传图片后复制链接填入</div>
                                    </div>
                                </div>

                                <div class="col-12"><hr class="text-muted"></div>

                                <!-- 联系方式 -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill me-2"></i>联系与确认</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">业务经理微信二维码 URL</label>
                                            <input type="text" name="settings[contact_manager_qr]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['contact_manager_qr'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">业务经理电话</label>
                                            <input type="text" name="settings[contact_manager_phone]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['contact_manager_phone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 短信配置 -->
                        <div class="tab-pane fade" id="sms" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">短信配置 (腾讯云)</h5>
                            
                            <input type="hidden" name="settings[smsDriver]" value="tencent">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-medium">SDKAppID (应用 ID)</label>
                                    <input type="text" name="settings[smsSdkAppId]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['smsSdkAppId'] ?? ''); ?>" placeholder="例如：1400xxxxxx">
                                    <div class="form-text">在短信控制台应用列表查看</div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-medium">AppKey (应用密钥)</label>
                                    <input type="password" name="settings[smsSecretKey]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['smsSecretKey'] ?? ''); ?>">
                                    <div class="form-text">旧版接口专用：请填写短信应用的 AppKey</div>
                                </div>
                                
                                <input type="hidden" name="settings[smsSecretId]" value="">
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-medium">【注册】模板ID</label>
                                    <input type="text" name="settings[smsTemplateRegister]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['smsTemplateRegister'] ?? ''); ?>" placeholder="注册时使用的短信验证码模板ID">
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-medium">【找回密码】模板ID</label>
                                    <input type="text" name="settings[smsTemplateForgot]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['smsTemplateForgot'] ?? ''); ?>" placeholder="找回密码时使用的短信验证码模板ID">
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-medium">【绑定手机】模板ID</label>
                                    <input type="text" name="settings[smsTemplateBind]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['smsTemplateBind'] ?? ''); ?>" placeholder="绑定手机号时使用的短信验证码模板ID">
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-medium">签名名称</label>
                                    <input type="text" name="settings[smsSignName]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['smsSignName'] ?? ''); ?>" placeholder="短信的前缀【xxx】">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-medium">发送频率限制 (秒)</label>
                                    <input type="number" class="form-control bg-light border-0" value="60" disabled>
                                    <div class="form-text">系统固定为 60 秒</div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO 设置 -->
                        <div class="tab-pane fade" id="seo" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">SEO 设置</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium">全局 SEO 标题</label>
                                <input type="text" name="settings[globalSeoTitle]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['globalSeoTitle'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">全局 SEO 描述</label>
                                <textarea name="settings[globalSeoDescription]" class="form-control bg-light border-0" rows="3"><?php echo htmlspecialchars($settings['globalSeoDescription'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">全局 SEO 关键词</label>
                                <input type="text" name="settings[globalSeoKeywords]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['globalSeoKeywords'] ?? ''); ?>">
                                <div class="form-text">多个关键词请用逗号分隔</div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-light rounded-3 mb-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="settings[enableSitemap]" value="0">
                                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="enableSitemap" name="settings[enableSitemap]" value="1" <?php echo !empty($settings['enableSitemap']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-medium cursor-pointer" for="enableSitemap">开启 Sitemap</label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium">Robots.txt 内容</label>
                                <textarea name="settings[robotsTxt]" class="form-control bg-light border-0 font-monospace" rows="5"><?php echo htmlspecialchars($settings['robotsTxt'] ?? ''); ?></textarea>
                            </div>

                            <h5 class="mt-5 mb-3 fw-bold text-muted text-uppercase fs-7 ls-1">GEO 地理位置设置</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">地区代码 (geo.region)</label>
                                    <input type="text" name="settings[geoRegion]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['geoRegion'] ?? ''); ?>" placeholder="例如: CN-11">
                                    <div class="form-text">ISO 3166-2 代码</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">地名 (geo.placename)</label>
                                    <input type="text" name="settings[geoPlacename]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['geoPlacename'] ?? ''); ?>" placeholder="例如: Beijing">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">经纬度 (geo.position)</label>
                                    <input type="text" name="settings[geoPosition]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['geoPosition'] ?? ''); ?>" placeholder="例如: 39.9042;116.4074">
                                    <div class="form-text">格式: 纬度;经度</div>
                                </div>
                            </div>
                        </div>

                        <!-- 社交媒体 -->
                        <div class="tab-pane fade" id="social" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">社交媒体配置</h5>
                            
                            <div class="row g-4">
                                <?php
                                $socials = [
                                    ['key' => 'QQ', 'label' => 'QQ', 'icon' => 'tencent-qq'],
                                    ['key' => 'WeChat', 'label' => '微信', 'icon' => 'wechat'],
                                    ['key' => 'Weibo', 'label' => '微博', 'icon' => 'sina-weibo'],
                                    ['key' => 'Bilibili', 'label' => 'Bilibili', 'icon' => 'play-btn'],
                                    ['key' => 'Toutiao', 'label' => '今日头条', 'icon' => 'newspaper'],
                                    ['key' => 'Kuaishou', 'label' => '快手', 'icon' => 'camera-video'],
                                    ['key' => 'Douyin', 'label' => '抖音', 'icon' => 'tiktok'],
                                    ['key' => 'TikTok', 'label' => 'TikTok', 'icon' => 'tiktok'],
                                    ['key' => 'YouTube', 'label' => 'YouTube', 'icon' => 'youtube'],
                                    ['key' => 'Facebook', 'label' => 'Facebook', 'icon' => 'facebook'],
                                    ['key' => 'Twitter', 'label' => 'Twitter', 'icon' => 'twitter'],
                                    ['key' => 'X', 'label' => 'X.com', 'icon' => 'twitter-x'],
                                    ['key' => 'LinkedIn', 'label' => 'LinkedIn', 'icon' => 'linkedin'],
                                ];
                                
                                foreach ($socials as $s): 
                                    $showKey = 'showSocial' . $s['key'];
                                    $linkKey = 'social' . $s['key'];
                                ?>
                                <div class="col-md-6">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="settings[<?php echo $showKey; ?>]" value="0">
                                                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" name="settings[<?php echo $showKey; ?>]" value="1" <?php echo !empty($settings[$showKey]) ? 'checked' : ''; ?>>
                                                </div>
                                                <div class="fw-bold ms-2">
                                                    <i class="bi bi-<?php echo $s['icon'] ?? 'link-45deg'; ?> me-1"></i>
                                                    <?php echo $s['label']; ?>
                                                </div>
                                            </div>
                                            <input type="text" name="settings[<?php echo $linkKey; ?>]" class="form-control form-control-sm border-0 bg-white" placeholder="输入链接或账号" value="<?php echo htmlspecialchars($settings[$linkKey] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- 合规备案 -->
                        <div class="tab-pane fade" id="compliance" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">合规与备案</h5>
                            
                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                <div>
                                    <strong>提示</strong>
                                    <div>在中国大陆地区运营网站，请务必填写 ICP 备案号和公安网备案号，以符合相关法律法规要求。</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium">ICP 备案号</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-globe"></i></span>
                                    <input type="text" name="settings[icpBeian]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['icpBeian'] ?? ''); ?>" placeholder="例如：京ICP备00000000号">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">公安网备案号</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-shield-check"></i></span>
                                    <input type="text" name="settings[gonganBeian]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['gonganBeian'] ?? ''); ?>" placeholder="例如：京公网安备00000000000000号">
                                </div>
                            </div>
                        </div>

                        <!-- AI 设置 -->
                        <div class="tab-pane fade" id="ai" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">AI 设置</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium">API Base URL (自定义接口地址)</label>
                                <input type="text" name="settings[geminiBaseUrl]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['geminiBaseUrl'] ?? 'https://generativelanguage.googleapis.com'); ?>" placeholder="https://generativelanguage.googleapis.com">
                                <div class="form-text">如果是第三方中转接口，请输入完整的 Base URL，例如 `https://api.openai-proxy.com`。默认为 Google 官方地址。</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">API Key</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-key"></i></span>
                                    <input type="password" name="settings[geminiApiKey]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['geminiApiKey'] ?? ''); ?>" placeholder="sk-...">
                                </div>
                                <div class="form-text">用于后端调用 Google Gemini API 生成文章。请确保该 Key 有权限访问 `gemini-2.0-flash` 或相关模型。</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Model Name (模型名称)</label>
                                <input type="text" name="settings[geminiModel]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['geminiModel'] ?? 'gemini-2.0-flash-exp'); ?>" placeholder="gemini-2.0-flash-exp">
                                <div class="form-text">指定调用的模型名称，如 `gemini-1.5-pro`, `gemini-2.0-flash-exp` 等。</div>
                            </div>
                        </div>

                        <!-- 外观与自定义 -->
                        <div class="tab-pane fade" id="custom" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">外观与自定义</h5>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium">网站图标 (Favicon URL)</label>
                                <div class="input-group">
                                     <span class="input-group-text bg-light border-0"><i class="bi bi-image"></i></span>
                                     <input type="text" name="settings[siteFavicon]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['siteFavicon'] ?? '/favicon.svg'); ?>" placeholder="/favicon.svg">
                                </div>
                                <div class="form-text">您可以输入图标的 URL 地址。如果您想使用自定义图标，请先在媒体库上传图片，然后复制链接填入此处。</div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium">自定义 CSS</label>
                                <div class="position-relative">
                                    <textarea name="settings[customCss]" class="form-control bg-light border-0 font-monospace" rows="10" placeholder="/* 在此处输入 CSS 代码 */"><?php echo htmlspecialchars($settings['customCss'] ?? ''); ?></textarea>
                                    <div class="position-absolute top-0 end-0 m-2 badge bg-secondary opacity-50">CSS</div>
                                </div>
                                <div class="form-text">这些 CSS 将被注入到页面 <code>&lt;head&gt;</code> 区域。</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium">自定义 JavaScript</label>
                                <div class="position-relative">
                                    <textarea name="settings[customJs]" class="form-control bg-light border-0 font-monospace" rows="10" placeholder="// 在此处输入 JavaScript 代码"><?php echo htmlspecialchars($settings['customJs'] ?? ''); ?></textarea>
                                    <div class="position-absolute top-0 end-0 m-2 badge bg-warning opacity-50 text-dark">JS</div>
                                </div>
                                <div class="form-text">这些 JS 将被注入到页面底部 <code>&lt;/body&gt;</code> 之前。</div>
                            </div>
                        </div>

                        <!-- 静态页面 -->
                        <div class="tab-pane fade" id="pages" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">静态页面内容</h5>
                            <p class="text-muted mb-4">编辑关于我们、隐私政策、服务条款等页面的内容。留空则显示默认文本。</p>

                            <h6 class="fw-bold text-indigo-600 mb-3"><i class="bi bi-info-circle me-2"></i>关于我们</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">页面标题</label>
                                    <input type="text" name="settings[about_hero_title]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['about_hero_title'] ?? ''); ?>" placeholder="关于我们">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">副标题</label>
                                    <input type="text" name="settings[about_hero_desc]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['about_hero_desc'] ?? ''); ?>" placeholder="简短描述">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">页面内容 (HTML)</label>
                                    <textarea name="settings[about_content]" class="form-control bg-light border-0" rows="10"><?php echo htmlspecialchars($settings['about_content'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">联系邮箱</label>
                                    <input type="email" name="settings[about_contact_email]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['about_contact_email'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">联系电话</label>
                                    <input type="text" name="settings[about_contact_phone]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['about_contact_phone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">公司地址</label>
                                    <input type="text" name="settings[about_contact_address]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['about_contact_address'] ?? ''); ?>">
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold text-indigo-600 mb-3"><i class="bi bi-shield-check me-2"></i>隐私政策</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">页面标题</label>
                                    <input type="text" name="settings[privacy_title]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['privacy_title'] ?? ''); ?>" placeholder="隐私政策">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">页面内容 (HTML)</label>
                                    <textarea name="settings[privacy_content]" class="form-control bg-light border-0" rows="10"><?php echo htmlspecialchars($settings['privacy_content'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold text-indigo-600 mb-3"><i class="bi bi-file-text me-2"></i>服务条款</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">页面标题</label>
                                    <input type="text" name="settings[terms_title]" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($settings['terms_title'] ?? ''); ?>" placeholder="服务条款">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">页面内容 (HTML)</label>
                                    <textarea name="settings[terms_content]" class="form-control bg-light border-0" rows="10"><?php echo htmlspecialchars($settings['terms_content'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 首页布局 -->
                        <div class="tab-pane fade" id="homepage" role="tabpanel">
                            <h5 class="card-title fw-bold mb-4 pb-2 border-bottom">首页内容配置</h5>
                            <p class="text-muted mb-4">设置首页展示的定制内容区域。留空则不显示。</p>

                            <h6 class="fw-bold text-indigo-600 mb-3"><i class="bi bi-blockquote-left me-2"></i>关于我们区</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-medium">内容 (HTML)</label>
                                    <textarea name="settings[homepage_about_html]" class="form-control bg-light border-0 font-monospace" rows="6"><?php echo htmlspecialchars($settings['homepage_about_html'] ?? ''); ?></textarea>
                                    <div class="form-text">首页文章列表上方的品牌介绍区。支持 HTML。</div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-indigo-600 mb-3"><i class="bi bi-grid-3x3-gap me-2"></i>服务展示区</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-medium">内容 (HTML)</label>
                                    <textarea name="settings[homepage_services_html]" class="form-control bg-light border-0 font-monospace" rows="6"><?php echo htmlspecialchars($settings['homepage_services_html'] ?? ''); ?></textarea>
                                    <div class="form-text">首页功能/服务展示区。支持 HTML。</div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-indigo-600 mb-3"><i class="bi bi-megaphone me-2"></i>底部 CTA 区</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-medium">内容 (HTML)</label>
                                    <textarea name="settings[homepage_cta_html]" class="form-control bg-light border-0 font-monospace" rows="4"><?php echo htmlspecialchars($settings['homepage_cta_html'] ?? ''); ?></textarea>
                                    <div class="form-text">首页底部的 Call-to-Action 区域（深色背景）。仅在首页第1页显示。</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold text-indigo-600 mb-3"><i class="bi bi-layout-text-window me-2"></i>落地页</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-medium">落地页内容 (HTML)</label>
                                    <textarea name="settings[landing_content]" class="form-control bg-light border-0 font-monospace" rows="10"><?php echo htmlspecialchars($settings['landing_content'] ?? ''); ?></textarea>
                                    <div class="form-text">当首页类型设为"落地页"时显示。留空则自动显示最新文章列表。支持完整 HTML。</div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold text-indigo-600 mb-3"><i class="bi bi-person-lines-fill me-2"></i>文章侧栏</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">作者/开发者名称</label>
                                <input type="text" name="settings[article_sidebar_name]" value="<?php echo htmlspecialchars($settings['article_sidebar_name'] ?? ''); ?>" class="form-control bg-light border-0">
                                <div class="form-text">显示在文章侧栏"关于"小部件中的标题。</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">角色/副标题</label>
                                <input type="text" name="settings[article_sidebar_role]" value="<?php echo htmlspecialchars($settings['article_sidebar_role'] ?? ''); ?>" class="form-control bg-light border-0">
                                <div class="form-text">例如：Intelligent Editor、AI 内容引擎。</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">开发者/作者简介</label>
                                <textarea name="settings[article_sidebar_bio]" class="form-control bg-light border-0" rows="3"><?php echo htmlspecialchars($settings['article_sidebar_bio'] ?? ''); ?></textarea>
                                <div class="form-text">显示在文章详情页侧栏的"关于"小部件中。支持换行。</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3 text-end">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> 保存所有设置
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>