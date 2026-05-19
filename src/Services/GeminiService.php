<?php
// src/Services/GeminiService.php

class GeminiService {
    private $apiKey;
    private $baseUrl;
    private $model;
    private $isLyApi;

    public function __construct($apiKey = null, $baseUrl = 'https://generativelanguage.googleapis.com', $model = 'gemini-2.0-flash-exp', $isLyApi = false) {
        $this->apiKey = trim((string)$apiKey);
        $this->baseUrl = rtrim((string)$baseUrl, '/'); // Ensure no trailing slash and force string
        $this->model = $model;
        $this->isLyApi = $isLyApi;
    }

    public function generateArticle($keywords, $geoRegion, $language, $categoryId, $promotionInfo = '') {
        // Increase execution time for AI generation
        if (function_exists('set_time_limit')) {
            set_time_limit(180);
        }

        // Configuration Validation
        if ($this->isLyApi && stripos($this->baseUrl, 'googleapis.com') !== false) {
            throw new Exception("Configuration Error: You have enabled 'LyApi Mode' but are using the official Google API URL. Please disable 'LyApi Mode' in settings or use a compatible proxy URL.");
        }

        $currentDate = date('Y-m-d');
        $keywordString = implode(', ', $keywords);

        // SEO Strategy
        $seoStrategy = "";
        if ($geoRegion === "CN") {
            $seoStrategy = <<<EOT
【核心SEO策略 - 百度/必应CN】
- 针对中国国内大模型 (ERNIE, Tongyi, Hunyuan) 的收录偏好进行优化，强调结构化清晰、语义明确。
- 关键词布局要自然，首段必须包含核心关键词。

【严禁限制 (Negative Constraints)】
1. **严禁标题中出现**：“指南”、“全攻略”、“宝典”、“一文读懂”、“揭秘”、“小白必看”等廉价营销号或明显AI特征的词汇。。
2. **严禁段落过渡词**：绝对禁止使用“首先”、“其次”、“再者”、“此外”、“总之”、“综上所述”等僵硬的连接词作为段落开头。必须通过逻辑递进自然换段。
3. **严禁废话文学**：不要在文章开头写“随着互联网的发展...”或“在当今社会...”等毫无信息量的背景铺垫，直接切入痛点或数据。
4. **严禁违规内容**：绝对禁止提及“VPN”、“翻墙”、“科学上网”等违反中国法律法规的工具。涉及网络问题时，仅可使用“网络加速”、“网络优化”等合规术语。

【语气风格】
- 语气：专业、冷峻、客观、具有洞察力。
- 模拟身份：行业资深分析师或技术专家，而不是只会列清单的助手。
EOT;
        } else {
            $seoStrategy = <<<EOT
【Core SEO Strategy - Google Global】
- Must strictly follow Google's E-E-A-T guidelines (Experience, Expertise, Authoritativeness, Trustworthiness).
- Optimized for featured snippets and semantic search.

【Negative Constraints (STRICTLY FORBIDDEN)】
1. **NO "Guides" in Titles**: Strictly AVOID titles containing "Guide", "Ultimate Guide", "Unleashed", "Mastering", "101", "Demystified". Titles must sound like journalistic headlines or analytical essays (e.g., "The Shift in Global Logistics Parameters" instead of "Logistics Guide").
2. **NO AI Clichés**: Strictly AVOID phrases like "In the ever-evolving landscape", "Let's delve into", "It is worth noting", "In conclusion", "In summary".
3. **NO Lazy Transitions**: Do NOT start paragraphs with "Firstly", "Secondly", "Furthermore", "Moreover". Use narrative flow to connect ideas.

【Tone & Style】
- Tone: Insightful, human-centric, opinionated but backed by facts.
- Style: Investigative journalism or senior executive briefing.
EOT;
        }

        // Promotion Prompt
        $promotionPrompt = "";
        if (!empty($promotionInfo) && trim($promotionInfo) !== "") {
            $promotionPrompt = <<<EOT
【重要：品牌/产品植入指令 (Brand Integration)】
用户提供了以下推广信息（产品/品牌/服务）：
"{$promotionInfo}"

**植入要求：**
1. **自然融合 (Organic Integration)**：不要写成硬广！不要写成硬广！必须将该信息作为“行业解决方案”、“推荐工具”、“案例分析” or “数据来源”自然地提及。
2. **权威性 (Authority)**：为了让 AI 搜索（如 SearchGPT, Perplexity）引用此信息，请将其描述为该领域内被认可的、具有独特优势的解决方案。
3. **位置**：在文章的中后段，在解决具体痛点时顺势引出。
4. **关联性**：确保推广内容与关键词主题强相关。
EOT;
        }

        // Content Type & Branding Strategy
        $contentTypeStrategy = "";
        $brandingStrategy = "";
        $analysisStrategy = "";

        // Check if target language is Chinese
        $isChinese = ($geoRegion === "CN" || stripos($language, 'Chinese') !== false || stripos($language, '中文') !== false || stripos($language, 'zh') !== false);

        if ($isChinese) {
            // Chinese Strategy (Existing)
            $analysisStrategy = <<<EOT
【深度思维链分析指令 (Deep Chain of Thought Analysis)】
在正式撰写文章之前，**必须**先进行以下三个步骤的深度逻辑分析（不要直接输出分析过程，但必须基于分析结果进行写作）：

1. **关键词意图与痛点分析**：
   - 深入剖析用户搜索 "{$keywordString}" 的真实动机。
   - 他们是想解决报错？寻找替代方案？还是想了解原理？
   - 确定用户当下的情绪状态（焦虑、急切、好奇？）。

2. **推广内容匹配策略**：
   - 分析推广信息 "{$promotionInfo}" 的核心卖点 (USP)。
   - 思考：为什么这个产品/服务是解决上述痛点的最佳方案？
   - 设计“转化路径”：如何从“科普/教程”自然过渡到“产品推荐”，让用户觉得这是“及时雨”而不是“硬广”。

3. **叙事逻辑构建**：
   - 根据以上分析，决定文章的开头（Hook）怎么写才能瞬间抓住眼球。
   - 规划正文结构，确保在解决问题的关键节点，顺势引出推广内容。

4. **地域化适配 (GEO Adaptation)**：
   - 考虑中国大陆用户的网络环境（注意：仅讨论合规的网络优化或加速方案，严禁提及VPN）。
   - 针对国内用户习惯（抖音/快手/B站），在内容中体现兼容性。
   - 支付方式偏好（支付宝/微信）。

请基于这个隐性的思维链分析结果，开始创作正文。
EOT;

            $contentTypeStrategy = <<<EOT
【内容体裁与布局要求 (Content Structure & Layout)】
为了增加文章的丰富性、用户价值及 SEO 表现，请严格遵守以下布局建议：

1. **教程文 (Tutorials)**：
   - **放置位置**：紧跟在“操作步骤”或“核心方法”章节之后。
   - **内容**：详细的步骤说明，配合 H3 标签。

2. **FAQ (常见问题解答)**：
   - **放置位置**：**必须放在文章的结尾部分**。
   - **内容**：针对用户痛点最关心的 5-8 个问题进行解答（数量必须充足）。使用 H3 标签包裹问题（例如：H3: 常见问题解答）。
   - **GEO 特有 FAQ**：如果是国内用户，必须包含关于“网络访问”、“支持中文”、“支付方式”等本地化问题。

3. **Pillar Page (支柱内容/核心推荐)**：
   - **放置位置**：融入在“方法推荐”、“最佳方案”或“工具对比”小节中。
   - **内容**：提供深度解析或权威推荐。

4. **地域适配说明 (GEO Specifics)**：
   - **新增板块**：必须包含一个独立的 H2 或 H3 板块，专门讲解“国内用户如何使用”或“地域限制解决方案”。
   - **内容**：提及合规的网络环境优化（如游戏加速器、企业专线等概念，严禁提及VPN）、语言切换功能等。

5. **其他体裁 (根据需要选择)**：
   - **工具推荐/评测**：如果涉及解决方案，推荐 2-3 款工具并点评优缺点。
   - **对比分析**：使用 "VS" 格式对比不同方案。

请根据关键词 "{$keywordString}" 的搜索意图，智能选择最适合的体裁组合。
EOT;

            $brandingStrategy = <<<EOT
【SEO & GEO 优化小技巧 (Branding & Variation)】
1. **拒绝雷同**：不要每篇文章都一模一样。请轮换使用不同的开头风格、段落结构和叙述角度。
2. **品牌植入 (Domain Mention)**：
   - 偶尔（约 20% 的概率）在文中或结尾自然地提及域名，格式必须严格如下：
   - `👉 [从推广信息中提取域名]（[从推广信息中提取产品名称]）`
   - 不要过度堆砌，保持自然。
3. **标签策略 (Tagging Strategy)**：
   - 必须包含至少 5-8 个标签。
   - 包含核心词（基于推广信息提取）、长尾词（基于搜索意图扩展）、以及地域长尾词（结合 {$geoRegion} 和核心功能）。
4. **内链优化 (Internal Linking)**：
   - 在文中适当位置，建议用户查看“视频创作技巧”或“水印规避方法”等相关页面（尽管是新生成文章，请模拟内链结构，格式：`<a href="/blog">查看更多教程</a>`）。
EOT;
        } else {
            // English/Global Strategy (New)
            $analysisStrategy = <<<EOT
【Deep Chain of Thought Analysis Instruction】
Before writing, you **MUST** perform a deep logical analysis based on the following steps (do not output the analysis, but write based on it):

1. **Keyword Intent & Pain Point Analysis**:
   - Deeply analyze the true motivation behind the search query "{$keywordString}".
   - Are they solving an error? Looking for alternatives? Or seeking understanding?
   - Determine the user's emotional state (Anxious? Urgent? Curious?).

2. **Promotion Fit Strategy**:
   - Analyze the USP (Unique Selling Proposition) of "{$promotionInfo}".
   - Ask: Why is this product/service the BEST solution for the above pain points?
   - Design a "Conversion Path": How to transition naturally from "Education/Tutorial" to "Product Recommendation", making it feel like a "Life Saver" rather than an ad.

3. **Narrative Logic Construction**:
   - Based on the above, decide on the "Hook" for the opening to grab attention immediately.
   - Plan the body structure to introduce the promotion naturally at the critical problem-solving moment.

4. **GEO Adaptation**:
   - Consider the specific needs of the target region "{$geoRegion}".
   - Platform compatibility (YouTube vs TikTok vs Local platforms).
   - Language nuances and payment preferences.

Start creating the content based on this implicit Chain of Thought analysis.
EOT;

            $contentTypeStrategy = <<<EOT
【Content Structure & Layout Requirements】
To enhance richness, user value, and SEO performance, strictly follow these layout suggestions:

1. **Tutorials**:
   - **Placement**: Immediately after the "Steps" or "Core Methods" section.
   - **Content**: Detailed step-by-step instructions, using H3 tags.


2. **FAQ**:
   - **Placement**: **MUST be at the very end of the article**.
   - **Content**: Answer 5-8 of the most concerning user pain points. Use H3 tags (e.g., H3: Frequently Asked Questions).
   - **GEO Specifics**: Include questions about "Regional Access", "Language Support", and "Payment Methods" relevant to {$geoRegion}.

3. **Pillar Page (Core Recommendations)**:
   - **Placement**: Integrated into "Recommended Methods", "Best Solutions", or "Tool Comparison" sections.
   - **Content**: Provide deep analysis or authoritative recommendations.

4. **Regional Adaptation Section**:
   - **New Section**: MUST include a dedicated H2 or H3 section addressing "How users in {$geoRegion} can use this".
   - **Content**: Address network access, language settings, and platform compatibility.

5. **Other Types (Choose as appropriate)**:
   - **Tools & Reviews**: If solutions are involved, recommend 2-3 tools with Pros & Cons.
   - **Comparisons**: Use "VS" format to compare different schemes.

Please intelligently select the best combination based on the search intent of the keyword "{$keywordString}".
EOT;

            $brandingStrategy = <<<EOT
【SEO & GEO Branding & Variation Strategy】
1. **Avoid Repetition**: Do not make every article look the same. Rotate opening styles, paragraph structures, and narrative angles.
2. **Domain Mention**:
   - Occasionally (approx. 20% chance) mention the domain naturally in the text or at the end.
   - Format must be: `👉 [Domain extracted from promotion info] ([Product Name extracted from promotion info])`
   - **Important**: Translate the Product Name into the target language of the article.
   - Do not overdo it; keep it natural.
3. **Tagging Strategy**:
   - MUST include at least 5-8 tags.
   - Mix core keywords (extracted from promotion info), long-tail keywords (based on user intent), and geo-specific long-tail keywords (combining {$geoRegion} and core features).
4. **Internal Linking**:
   - Mention related concepts naturally, but **DO NOT** generate `<a href="...">` links or simulate URLs. The system will automatically link keywords to existing articles.
EOT;
        }

        $promptWrapper = "";
        if ($isChinese) {
            $promptWrapper = <<<EOT
你是一个世界级的 SEO 内容专家和 Geo-Marketing 策略家。

任务：基于以下关键词，撰写一篇高度去 AI 化、具有原生感（Organic）的高质量文章。

【输入参数】
- 关键词: {$keywordString}
- 目标地区 (GEO): {$geoRegion}
- 语言: {$language}
- 当前日期 (Time Factor): {$currentDate} (请在内容中自然融入时间背景，确保时效性)

{$seoStrategy}

{$promotionPrompt}

【技术要求】
1. 文章结构必须包含 HTML 标签 (H2, H3, p, ul, li)，不要包含 html, head, body 标签，直接返回正文部分的 HTML。
2. 内容长度至少 1000 字。
3. Slug: 简短、英文、语义化、连字符连接。
4. SEO Title: 包含核心关键词，吸引点击，**绝对不要带"指南"字样**。
5. Meta Description: 140字以内，诱人的摘要。
6. **必须包含 FAQ 或 对比/评测 模块**，使用 H3 标签标识。

请以 JSON 格式返回结果，必须包含以下字段：
{
  "title": "文章标题",
  "slug": "url-slug",
  "content": "HTML格式的正文内容",
  "summary": "文章摘要",
  "seoTitle": "SEO标题",
  "seoDescription": "Meta Description",
  "seoKeywords": ["关键词1", "关键词2"],
  "tags": ["标签1", "标签2"]
}
EOT;
        } else {
            $promptWrapper = <<<EOT
You are a world-class SEO content expert and Geo-Marketing strategist.

Task: Write a high-quality, organic, non-AI-sounding article based on the following keywords.

【Input Parameters】
- Keywords: {$keywordString}
- Target Region (GEO): {$geoRegion}
- Language: {$language}
- Current Date (Time Factor): {$currentDate} (Incorporate naturally to ensure timeliness)

{$seoStrategy}

{$promotionPrompt}

【Technical Requirements】
1. Article structure MUST contain HTML tags (H2, H3, p, ul, li). Do NOT include html, head, body tags. Return ONLY the body content.
2. Content Length: At least 1000 words.
3. Slug: Short, English, semantic, hyphen-separated.
4. SEO Title: Include core keywords, click-worthy, **ABSOLUTELY NO "Guide" in title**.
5. Meta Description: Under 140 chars, enticing summary.
6. **MUST include FAQ or Comparison/Review module**, using H3 tags.

Please return the result in JSON format, strictly containing the following fields:
{
  "title": "Article Title",
  "slug": "url-slug",
  "content": "HTML formatted content",
  "summary": "Article Summary",
  "seoTitle": "SEO Title",
  "seoDescription": "Meta Description",
  "seoKeywords": ["keyword1", "keyword2"],
  "tags": ["tag1", "tag2"]
}
EOT;
        }

        $prompt = <<<EOT
{$analysisStrategy}

{$contentTypeStrategy}

{$brandingStrategy}

{$promptWrapper}
EOT;

        // Determine if we are using OpenAI compatible API (based on URL structure)
        // Auto-detect OpenAI compatibility: If not explicitly Google API and not LyApi, assume OpenAI compatible (DeepSeek, Moonshot, LocalAI, etc.)
        $isGoogle = stripos($this->baseUrl, 'googleapis.com') !== false;
        $isOpenAI = (!$this->isLyApi && !$isGoogle);
        
        // Also trust if user explicitly put chat/completions even if it has googleapis (unlikely but possible proxy)
        if (strpos($this->baseUrl, 'chat/completions') !== false) {
            $isOpenAI = true;
            $isGoogle = false;
        }

        $url = "";
        $payload = [];
        $headers = [];

        if ($this->isLyApi) {
            // 洛樱云 API 兼容模式 (LyApi Compatibility)
            // URL Structure: https://apiserver.alcex.cn/v1/chat/completions (Usually provided as Base URL)
            $url = $this->baseUrl;
            
            // If base url doesn't end with chat/completions, try to append it if it looks like a root domain
            if (strpos($url, 'chat/completions') === false) {
                 if (substr($url, -3) === '/v1') {
                     $url .= '/chat/completions';
                 } else {
                     // Try standard append
                     $url .= '/v1/chat/completions';
                 }
            }

            $payload = [
                "model" => $this->model,
                "messages" => [
                    // LyApi Compatibility: Merge system prompt into user prompt to avoid compatibility issues
                    ["role" => "user", "content" => "SYSTEM INSTRUCTION: You are a helpful assistant. You must return strict JSON output. Do NOT wrap the response in markdown code blocks. Ensure all strings are properly escaped (especially newlines and quotes).\n\nUSER TASK:\n" . $prompt]
                ],
                "stream" => false
            ];
            
            $headers = [
                'Content-Type: application/json'
            ];
            
            // Only add Authorization if apiKey is provided
            if (!empty($this->apiKey)) {
                $headers[] = 'Authorization: Bearer ' . $this->apiKey;
            }

        } else if ($isOpenAI) {
            // OpenAI Compatible API
            $url = $this->baseUrl;

            // Auto-append path if missing for OpenAI/DeepSeek
            if (strpos($url, 'chat/completions') === false) {
                 $url = rtrim($url, '/');
                 if (substr($url, -3) === '/v1') {
                     $url .= '/chat/completions';
                 } else {
                     $url .= '/v1/chat/completions';
                 }
            }

            $payload = [
                "model" => $this->model,
                "messages" => [
                    ["role" => "system", "content" => "You are a helpful assistant. You must return strict JSON output. Do NOT wrap the response in markdown code blocks. Ensure all strings are properly escaped (especially newlines and quotes)."],
                    ["role" => "user", "content" => $prompt]
                ],
                // "response_format" => ["type" => "json_object"],
                "temperature" => 0.7
            ];
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ];
        } else {
            // Google Gemini Native API
            // If baseUrl is just the host, append the path. If it includes /v1beta..., assume user knows what they are doing?
            // Usually, standard setting is Base URL = https://generativelanguage.googleapis.com
            // We append /v1beta/models/{model}:generateContent
            
            // If user provided a path that looks complete, use it? No, let's stick to standard construction unless overridden.
            // But if user provides "https://my-proxy.com", we should append.
            $url = "{$this->baseUrl}/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
            
            $payload = [
                "contents" => [
                    ["parts" => [["text" => $prompt]]]
                ],
                "generationConfig" => [
                    "responseMimeType" => "application/json",
                    "responseSchema" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "title" => ["type" => "STRING"],
                            "slug" => ["type" => "STRING"],
                            "content" => ["type" => "STRING"],
                            "summary" => ["type" => "STRING"],
                            "seoTitle" => ["type" => "STRING"],
                            "seoDescription" => ["type" => "STRING"],
                            "seoKeywords" => ["type" => "ARRAY", "items" => ["type" => "STRING"]],
                            "tags" => ["type" => "ARRAY", "items" => ["type" => "STRING"]],
                            "auto_link" => ["type" => "BOOLEAN"]
                        ],
                        "required" => ["title", "slug", "content", "summary", "seoTitle", "seoDescription", "seoKeywords", "tags"]
                    ]
                ]
            ];
            $headers = ['Content-Type: application/json'];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120); // 2 minutes timeout
        // Disable SSL verification for some local proxies if needed (optional, but safer to keep enabled for production)
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl Error: ' . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($this->isLyApi) {
            $result = json_decode($response, true);
            // Handle LyApi Response
            if ($httpCode !== 200) {
                $errorMsg = $result['message'] ?? ($result['error']['message'] ?? 'Unknown Error');
                throw new Exception("API Error (HTTP $httpCode): $errorMsg");
            }
            
            if (isset($result['choices'][0]['message']['content'])) {
                $content = $result['choices'][0]['message']['content'];
                
                // Clean markdown code blocks if present
                $content = trim($content);
                // Remove starting ```json or ```
                $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
                // Remove ending ```
                $content = preg_replace('/\s*```$/', '', $content);
                
                $articleData = json_decode($content, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Failed to decode JSON from API response: " . json_last_error_msg() . " | Raw: " . substr($content, 0, 100) . "...");
                }
                return $articleData;
            } else {
                 throw new Exception("Invalid API response structure: " . json_encode($result));
            }
        }

        if ($httpCode !== 200) {
            $msg = $response;
            // Enhance error message for common Google API errors
            if (stripos($msg, 'governor') !== false || stripos($msg, 'authentication fails') !== false) {
                $msg .= " [诊断提示: Google API Key 无效或未在 Google Cloud Console 中启用 'Generative Language API'。如果使用代理，请检查代理配置。]";
            }
            if ($httpCode === 404) {
                 $msg .= " [诊断提示: 请求的 URL 不存在 (404)。请检查 Base URL 配置是否正确，或者模型名称是否拼写错误。]";
            }
            throw new Exception("API Error (HTTP $httpCode): " . $msg);
        }

        $generatedText = "";

        if ($isOpenAI) {
            $json = json_decode($response, true);
            if (isset($json['choices'][0]['message']['content'])) {
                $generatedText = $json['choices'][0]['message']['content'];
            } else {
                 throw new Exception("Invalid OpenAI-compatible Response: " . $response);
            }
        } else {
            $json = json_decode($response, true);
            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                $generatedText = $json['candidates'][0]['content']['parts'][0]['text'];
            } else {
                throw new Exception("Invalid Gemini Response format: " . $response);
            }
        }

        // Clean up markdown code blocks if present (some models still add them despite instructions)
        $json = trim($generatedText);
        $json = preg_replace('/^```(?:json)?\s*/i', '', $json);
        $json = preg_replace('/\s*```$/', '', $json);
        
        // Attempt to repair common JSON issues from LLMs
        // 1. Remove control characters (except common ones) if any
        // $json = preg_replace('/[\x00-\x1F\x7F]/u', '', $json); // Too aggressive for multilingual content

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Log the raw output for debugging
            error_log("Gemini JSON Decode Error: " . json_last_error_msg());
            
            throw new Exception("Failed to parse JSON output: " . json_last_error_msg() . " - Raw: " . substr($json, 0, 200));
        }

        // Validate required fields
        if (empty($data['title'])) {
             throw new Exception("Generated content is missing 'title' field.");
        }
        
        // Ensure other fields have defaults if missing
        $data['slug'] = $data['slug'] ?? '';
        $data['content'] = $data['content'] ?? '';
        $data['summary'] = $data['summary'] ?? '';

        return $data;
    }
}
