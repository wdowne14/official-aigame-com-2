<?php
/**
 * Site metadata container with description generation capabilities.
 */

class SiteMeta {
    private array $meta;
    private string $separator;

    public function __construct(array $meta = [], string $separator = ' | ') {
        $this->meta = $meta;
        $this->separator = $separator;
    }

    /**
     * Set or update a single meta field.
     */
    public function set(string $key, $value): void {
        $this->meta[$key] = $value;
    }

    /**
     * Get a meta field value, or default if missing.
     */
    public function get(string $key, $default = null) {
        return $this->meta[$key] ?? $default;
    }

    /**
     * Return all meta fields as associative array.
     */
    public function all(): array {
        return $this->meta;
    }

    /**
     * Generate a short description text (≤ maxLength characters).
     * Combines title, description, keywords and fallback site name.
     */
    public function generateShortDescription(int $maxLength = 160): string {
        $parts = [];

        $title = $this->get('title');
        if ($title !== null) {
            $parts[] = $title;
        }

        $desc = $this->get('description');
        if ($desc !== null) {
            $parts[] = $desc;
        }

        $keywords = $this->get('keywords');
        if ($keywords !== null) {
            if (is_array($keywords)) {
                $parts[] = implode(', ', $keywords);
            } else {
                $parts[] = (string) $keywords;
            }
        }

        $siteName = $this->get('site_name');
        if ($siteName === null && empty($parts)) {
            $siteName = '爱游戏体育';
        }
        if ($siteName !== null) {
            $parts[] = $siteName;
        }

        $raw = implode($this->separator, $parts);

        // Trim to maxLength, preferably at word boundary.
        if (mb_strlen($raw) > $maxLength) {
            $trimmed = mb_substr($raw, 0, $maxLength - 3);
            $lastSpace = mb_strrpos($trimmed, ' ');
            if ($lastSpace !== false && $lastSpace > 0) {
                $trimmed = mb_substr($trimmed, 0, $lastSpace);
            }
            $raw = $trimmed . '...';
        }

        return $raw;
    }

    /**
     * Return description HTML-safe (escaped).
     */
    public function htmlDescription(int $maxLength = 160): string {
        $desc = $this->generateShortDescription($maxLength);
        return htmlspecialchars($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Static factory: create from well-known defaults.
     */
    public static function createDefault(): self {
        return new self([
            'site_name'        => '爱游戏体育',
            'title'            => '爱游戏体育 - 官方平台',
            'description'      => '爱游戏体育提供最新体育赛事资讯、比分直播与精彩分析。',
            'keywords'         => ['爱游戏体育', '体育赛事', '比分直播', '体育资讯'],
            'url'              => 'https://official-aigame.com',
            'language'         => 'zh-CN',
            'charset'          => 'UTF-8',
            'author'           => '爱游戏体育团队',
        ]);
    }
}

// ---------------------------------------------------------------------------
// Example usage (could be omitted in library context)
// ---------------------------------------------------------------------------
if (!isset($argv) || (isset($argv[0]) && basename($argv[0]) === basename(__FILE__))) {
    $meta = SiteMeta::createDefault();
    echo "Generated description:\n";
    echo $meta->generateShortDescription(120) . "\n";
    echo "\nHTML safe version:\n";
    echo $meta->htmlDescription(120) . "\n";

    // Demonstrate custom meta
    $custom = new SiteMeta([
        'title'       => '爱游戏体育·最新动态',
        'description' => '覆盖足球、篮球、网球等主流运动。',
        'keywords'    => '爱游戏体育,足球,篮球',
        'url'         => 'https://official-aigame.com/news',
    ]);
    echo "\nCustom site description:\n";
    echo $custom->htmlDescription(100) . "\n";
}