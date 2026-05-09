-- xAI CMS Database Schema
-- MySQL 5.6+ Compatible

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for articles
-- ----------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL COMMENT '保留原前端生成的UUID，便于迁移',
  `category_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(191) NOT NULL COMMENT 'URL友好标识，utf8mb4下索引最大长度限制',
  `summary` VARCHAR(500) NOT NULL DEFAULT '',
  `content` MEDIUMTEXT NOT NULL COMMENT '正文内容',
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `geo_region` CHAR(2) NOT NULL DEFAULT 'CN' COMMENT 'ISO国家代码',
  `language` CHAR(5) NOT NULL DEFAULT 'zh-CN' COMMENT '语言代码',
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '0:草稿, 1:已发布, 2:归档, 3:垃圾',
  `views` INT UNSIGNED NOT NULL DEFAULT 0,
  `seo_title` VARCHAR(255) DEFAULT NULL,
  `seo_description` VARCHAR(255) DEFAULT NULL,
  `seo_keywords` VARCHAR(255) DEFAULT NULL,
  `published_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_category_status` (`category_id`, `status`, `published_at`),
  KEY `idx_region_lang` (`geo_region`, `language`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_status_published` (`status`, `published_at`),
  KEY `idx_status_views` (`status`, `views`),
  KEY `idx_uuid` (`uuid`),
  FULLTEXT KEY `idx_ft_search` (`title`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `parent_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT '',
  `sort_order` INT NOT NULL DEFAULT 0,
  `article_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '冗余字段，缓存文章数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_parent_sort` (`parent_id`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for tags
-- ----------------------------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  `article_count` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_article_count` (`article_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for article_tags
-- ----------------------------
DROP TABLE IF EXISTS `article_tags`;
CREATE TABLE `article_tags` (
  `article_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`article_id`, `tag_id`),
  KEY `idx_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `key` VARCHAR(64) NOT NULL,
  `value` TEXT,
  `group` VARCHAR(32) NOT NULL DEFAULT 'system',
  `autoload` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否自动加载到缓存',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Default Settings
-- ----------------------------
INSERT INTO `settings` (`key`, `value`, `group`) VALUES
('siteName', 'xAI CMS', 'system'),
('siteUrl', 'https://www.xAI-demo.com', 'system'),
('adminEmail', 'admin@xAI.com', 'system'),
('icpBeian', '京ICP备12345678号', 'compliance'),
('gonganBeian', '', 'compliance'),
('enableSitemap', '1', 'seo'),
('robotsTxt', 'User-agent: *\nAllow: /', 'seo'),
('globalSeoTitle', 'xAI - 全球化内容管理引擎', 'seo'),
('globalSeoDescription', '专注于 GEO 与 SEO 融合的高端内容管理系统。', 'seo'),
('globalSeoKeywords', 'SEO 优化,人工智能,跨境电商,品牌出海', 'seo'),
('savedKeywords', 'SEO 优化\n人工智能\n跨境电商\n品牌出海\nGoogle 算法\n百度收录\n独立站运营', 'seo'),
('savedPromotionInfo', '', 'seo'),
('language', 'zh-CN', 'system'),
('timezone', 'Asia/Shanghai', 'system'),
('footerCopyright', '© 2025 xAI Inc. All rights reserved.', 'system'),
('articlesPerPage', '10', 'system'),
('enableComments', '0', 'system'),
('adminPath', '/admin', 'security'),
('maxLoginAttempts', '5', 'security'),
('siteLogo', '', 'system'),
('siteFavicon', '', 'system'),
('defaultCoverImage', 'https://picsum.photos/800/400', 'system'),
('socialFacebook', 'https://facebook.com', 'social'),
('showSocialFacebook', '1', 'social'),
('socialTwitter', 'https://twitter.com', 'social'),
('showSocialTwitter', '1', 'social'),
('socialLinkedIn', 'https://linkedin.com', 'social'),
('showSocialLinkedIn', '1', 'social'),
('socialInstagram', '', 'social'),
('showSocialInstagram', '0', 'social'),
('socialQQ', '', 'social'),
('showSocialQQ', '0', 'social'),
('socialWeChat', '', 'social'),
('showSocialWeChat', '0', 'social'),
('socialWeibo', '', 'social'),
('showSocialWeibo', '0', 'social'),
('socialBilibili', '', 'social'),
('showSocialBilibili', '0', 'social'),
('socialToutiao', '', 'social'),
('showSocialToutiao', '0', 'social'),
('socialKuaishou', '', 'social'),
('showSocialKuaishou', '0', 'social'),
('socialDouyin', '', 'social'),
('showSocialDouyin', '0', 'social'),
('socialTikTok', '', 'social'),
('showSocialTikTok', '0', 'social'),
('socialYouTube', '', 'social'),
('showSocialYouTube', '0', 'social'),
('aboutPageMission', '赋能每一家出海企业，通过 智能化的 SEO 策略 和 本地化的内容生成，在全球互联网的任何角落建立品牌影响力。', 'content'),
('contactEmail', 'contact@xAI.com', 'content'),
('contactAddress', '100 Robinson Road, Singapore', 'content'),
('smsDriver', 'tencent', 'sms'),
('smsSecretId', '', 'sms'),
('smsSecretKey', '', 'sms'),
('smsSdkAppId', '', 'sms'),
('smsSignName', '', 'sms'),
('smsTemplateRegister', '', 'sms'),
('smsTemplateForgot', '', 'sms'),
('smsTemplateBind', '', 'sms');

-- ----------------------------
-- Table structure for spider_logs
-- ----------------------------
DROP TABLE IF EXISTS `spider_logs`;
CREATE TABLE `spider_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bot_name` VARCHAR(64) NOT NULL COMMENT 'Googlebot, GPTBot, etc.',
  `bot_type` VARCHAR(32) NOT NULL COMMENT 'SearchEngine, AIModel',
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` VARCHAR(500) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `status_code` SMALLINT NOT NULL DEFAULT 200,
  `visited_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_visited_at` (`visited_at`),
  KEY `idx_bot_name` (`bot_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for api_configs
-- ----------------------------
DROP TABLE IF EXISTS `api_configs`;
CREATE TABLE `api_configs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `api_key` VARCHAR(64) NOT NULL,
  `geo_region` CHAR(2) NOT NULL,
  `language` CHAR(5) NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `keywords` TEXT COMMENT 'JSON存储',
  `keyword_count` INT NOT NULL DEFAULT 1,
  `promotion_info` TEXT,
  `status` TINYINT NOT NULL DEFAULT 1,
  `article_status` INT NOT NULL DEFAULT 1,
  `ai_model_id` INT DEFAULT 0,
  `insert_image_count` INT NOT NULL DEFAULT 0,
  `insert_image_position` VARCHAR(20) DEFAULT 'random',
  `image_source_type` VARCHAR(20) DEFAULT 'random',
  `custom_image_urls` TEXT,
  `media_category_id` INT DEFAULT 0,
  `auto_link` TINYINT(1) DEFAULT 0,
  `user_id` INT DEFAULT 0,
  `scheme_id` INT DEFAULT 0,
  `daily_limit` INT DEFAULT 0,
  `call_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_api_key` (`api_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for ai_models
-- ----------------------------
DROP TABLE IF EXISTS `ai_models`;
CREATE TABLE `ai_models` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `provider` VARCHAR(50) NOT NULL,
  `model_name` VARCHAR(100) NOT NULL,
  `base_url` VARCHAR(255) NOT NULL,
  `api_key` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_ly_api` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `role` VARCHAR(20) NOT NULL DEFAULT 'user',
  `last_login_at` DATETIME DEFAULT NULL,
  `login_ip` VARCHAR(45) DEFAULT NULL,
  `points` INT DEFAULT 0 COMMENT '用户积分',
  `frozen_points` INT DEFAULT 0 COMMENT '冻结积分',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  UNIQUE KEY `uk_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for user_logs
-- ----------------------------
DROP TABLE IF EXISTS `user_logs`;
CREATE TABLE `user_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `details` TEXT,
  `ip_address` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_created` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for ai_schemes
-- ----------------------------
DROP TABLE IF EXISTS `ai_schemes`;
CREATE TABLE `ai_schemes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `config` TEXT,
  `target_count` INT NOT NULL DEFAULT 0,
  `daily_limit` INT NOT NULL DEFAULT 0,
  `cost_per_post` INT NOT NULL DEFAULT 0,
  `frozen_points` INT NOT NULL DEFAULT 0,
  `generated_count` INT NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `admin_notes` TEXT,
  `scheme_key` VARCHAR(64) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_scheme_key` (`scheme_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Insert default admin user (password: admin)
-- Note: In production, use bcrypt hash. This is a placeholder.
-- ----------------------------
-- INSERT INTO `users` (`email`, `password_hash`, `name`, `role`) VALUES ('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- ----------------------------
-- Table structure for media_categories
-- ----------------------------
DROP TABLE IF EXISTS `media_categories`;
CREATE TABLE `media_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Insert default media category
-- ----------------------------
INSERT INTO `media_categories` (`name`, `slug`) VALUES ('默认分类', 'default');

-- ----------------------------
-- Table structure for media_files
-- ----------------------------
DROP TABLE IF EXISTS `media_files`;
CREATE TABLE `media_files` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `size` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for announcements
-- ----------------------------
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'announcement' COMMENT 'activity, announcement, feature, important',
  `content` TEXT,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1:published, 0:draft',
  `published_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status_published` (`status`, `published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Insert default announcements
-- ----------------------------
INSERT INTO `announcements` (`title`, `type`, `content`, `status`, `published_at`) VALUES
('欢迎使用 xAI CMS', 'important', '感谢您选择 xAI CMS。这是一个基于 PHP 的现代化内容管理系统。', 1, NOW()),
('新功能上线通知', 'feature', '我们刚刚上线了用户仪表盘的新功能，现在您可以查看平台公告和系统更新了。', 1, NOW()),
('系统维护通知', 'announcement', '系统将于本周日凌晨进行例行维护，预计耗时 1 小时。', 1, DATE_SUB(NOW(), INTERVAL 2 DAY));

-- ----------------------------
-- Table structure for system_updates
-- ----------------------------
DROP TABLE IF EXISTS `system_updates`;
CREATE TABLE `system_updates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(50) NOT NULL,
  `content` TEXT NOT NULL,
  `release_date` DATE NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_release_date` (`release_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Insert default system_updates
-- ----------------------------
INSERT INTO `system_updates` (`version`, `content`, `release_date`) VALUES
('v1.1.0', '1. 新增平台公告功能\n2. 新增系统更新日志功能\n3. 优化用户仪表盘布局', CURDATE()),
('v1.0.5', '1. 修复了一些已知 bug\n2. 优化了后台管理界面的样式', DATE_SUB(CURDATE(), INTERVAL 10 DAY)),
('v1.0.0', 'xAI CMS 初始版本发布', DATE_SUB(CURDATE(), INTERVAL 30 DAY));



-- ----------------------------
-- Table structure for verification_codes
-- ----------------------------
DROP TABLE IF EXISTS `verification_codes`;
CREATE TABLE `verification_codes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone` VARCHAR(20) NOT NULL,
  `code` VARCHAR(10) NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `is_used` TINYINT(1) NOT NULL DEFAULT 0,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_phone_type` (`phone`, `type`, `is_used`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for point_packages
-- ----------------------------
DROP TABLE IF EXISTS `point_packages`;
CREATE TABLE `point_packages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `points` INT UNSIGNED NOT NULL,
    `bonus_percent` DECIMAL(5, 2) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for recharge_orders
-- ----------------------------
DROP TABLE IF EXISTS `recharge_orders`;
CREATE TABLE `recharge_orders` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `package_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `points` INT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `admin_remark` TEXT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_status` (`user_id`, `status`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Drop watermark-related tables if they exist
DROP TABLE IF EXISTS `watermark_logs`;
DROP TABLE IF EXISTS `watermark_stats`;
