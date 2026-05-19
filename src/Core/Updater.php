<?php
// src/Core/Updater.php
// Online update system — checks GitHub releases, downloads and applies updates

class Updater
{
    private string $repo = 'xAIcms/xaicms';
    private string $currentVersion;
    private string $cacheFile;

    public function __construct()
    {
        $this->currentVersion = $this->getCurrentVersion();
        $this->cacheFile = sys_get_temp_dir() . '/xaicms_update_check.json';
    }

    /**
     * Get current version from version file or settings
     */
    public function getCurrentVersion(): string
    {
        $versionFile = __DIR__ . '/../../VERSION';
        if (file_exists($versionFile)) {
            return trim(file_get_contents($versionFile));
        }
        // Fallback: read from settings
        try {
            return Settings::get('system_version', '1.0.0');
        } catch (\Exception $e) {
            return '1.0.0';
        }
    }

    /**
     * Check for updates (cached for 1 hour)
     */
    public function check(): array
    {
        // Return cached result if fresh
        if (file_exists($this->cacheFile)) {
            $cached = json_decode(file_get_contents($this->cacheFile), true);
            if ($cached && (time() - $cached['checked_at'] < 3600)) {
                return $cached;
            }
        }

        $result = [
            'current' => $this->currentVersion,
            'latest' => $this->currentVersion,
            'has_update' => false,
            'release_url' => '',
            'download_url' => '',
            'zip_url' => '',
            'changelog' => '',
            'published_at' => '',
            'checked_at' => time(),
        ];

        try {
            $release = $this->fetchLatestRelease();
            if ($release) {
                $latestVersion = ltrim($release['tag_name'] ?? '', 'vV');
                $result['latest'] = $latestVersion;
                $result['has_update'] = version_compare($latestVersion, $this->currentVersion, '>');
                $result['release_url'] = $release['html_url'] ?? '';
                $result['changelog'] = $release['body'] ?? '';
                $result['published_at'] = $release['published_at'] ?? '';

                // Find zip download URL
                if (!empty($release['assets'])) {
                    foreach ($release['assets'] as $asset) {
                        if (str_ends_with($asset['name'], '.zip')) {
                            $result['zip_url'] = $asset['browser_download_url'];
                            break;
                        }
                    }
                }
                // Fallback: GitHub source zip
                if (empty($result['zip_url']) && !empty($release['zipball_url'])) {
                    $result['zip_url'] = $release['zipball_url'];
                }
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        // Cache result
        file_put_contents($this->cacheFile, json_encode($result, JSON_PRETTY_PRINT));

        return $result;
    }

    /**
     * Download and apply update (returns true on success)
     */
    public function apply(): array
    {
        $check = $this->check();
        if (!$check['has_update']) {
            return ['success' => false, 'message' => 'No update available'];
        }
        if (empty($check['zip_url'])) {
            return ['success' => false, 'message' => 'No download URL found'];
        }

        $zipFile = sys_get_temp_dir() . '/xaicms_update.zip';
        $extractDir = sys_get_temp_dir() . '/xaicms_update_extract';

        try {
            // 1. Download
            $zipContent = $this->download($check['zip_url']);
            if (!$zipContent) {
                return ['success' => false, 'message' => 'Download failed'];
            }
            file_put_contents($zipFile, $zipContent);

            // 2. Extract
            if (is_dir($extractDir)) {
                $this->rmdir($extractDir);
            }
            $zip = new \ZipArchive();
            if ($zip->open($zipFile) !== true) {
                return ['success' => false, 'message' => 'Failed to open zip'];
            }
            $zip->extractTo($extractDir);
            $zip->close();

            // 3. Find the root folder (GitHub wraps in repo-name-branch/)
            $files = scandir($extractDir);
            $rootDir = $extractDir;
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;
                $subDir = "$extractDir/$f";
                if (is_dir($subDir) && file_exists("$subDir/public/index.php")) {
                    $rootDir = $subDir;
                    break;
                }
            }

            // 4. Backup current (keep config.php + uploads)
            $backupDir = sys_get_temp_dir() . '/xaicms_backup_' . date('YmdHis');
            $targetRoot = __DIR__ . '/../..';
            rename($targetRoot, $backupDir);
            rename($rootDir, $targetRoot);

            // 5. Restore config + uploads
            if (file_exists("$backupDir/config.php")) {
                copy("$backupDir/config.php", "$targetRoot/config.php");
            }
            if (is_dir("$backupDir/public/uploads")) {
                $this->copyDir("$backupDir/public/uploads", "$targetRoot/public/uploads");
            }

            // 6. Cleanup
            unlink($zipFile);
            if (is_dir($extractDir)) $this->rmdir($extractDir);

            // 7. Clear update cache
            if (file_exists($this->cacheFile)) unlink($this->cacheFile);

            return [
                'success' => true,
                'message' => "Updated from {$check['current']} to {$check['latest']}",
                'backup' => $backupDir,
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Update error: ' . $e->getMessage()];
        }
    }

    /**
     * Fetch latest release from GitHub API
     */
    private function fetchLatestRelease(): ?array
    {
        $url = "https://api.github.com/repos/{$this->repo}/releases/latest";
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: xAIcms-Updater\r\nAccept: application/vnd.github+json\r\n",
                'timeout' => 10,
            ]
        ]);

        $response = @file_get_contents($url, false, $ctx);
        if (!$response) return null;

        return json_decode($response, true);
    }

    /**
     * Download file via HTTP
     */
    private function download(string $url): ?string
    {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: xAIcms-Updater\r\n",
                'timeout' => 60,
                'follow_location' => 1,
            ]
        ]);

        // GitHub requires stream_context for redirects on some configs
        $content = @file_get_contents($url, false, $ctx);
        if ($content === false) {
            // Fallback: try cURL
            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_USERAGENT, 'xAIcms-Updater');
                $content = curl_exec($ch);
                curl_close($ch);
            }
        }

        return $content ?: null;
    }

    private function rmdir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = "$dir/$item";
            is_dir($path) ? $this->rmdir($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function copyDir(string $src, string $dst): void
    {
        if (!is_dir($src)) return;
        if (!is_dir($dst)) mkdir($dst, 0755, true);
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = "$src/$item";
            $d = "$dst/$item";
            is_dir($s) ? $this->copyDir($s, $d) : copy($s, $d);
        }
    }
}
