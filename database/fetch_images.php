<?php
/**
 * Fetch images for news thumbnails and traffic signs.
 * Usage: php database/fetch_images.php
 */

$pdo = new PDO('mysql:host=localhost;dbname=traffic_violation_db;charset=utf8mb4', 'root', 'hieu1205', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$uploadDir = __DIR__ . '/../public/assets/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$signsDir = $uploadDir . 'signs/';
if (!is_dir($signsDir)) {
    mkdir($signsDir, 0755, true);
}

// ============================================================
// 1. Traffic Signs: generate SVG images programmatically
// ============================================================
echo "=== TRAFFIC SIGN IMAGES ===\n";

$signs = $pdo->query('SELECT s.id, s.sign_code, s.name, g.sign_prefix, g.name as group_name FROM traffic_signs s JOIN traffic_sign_groups g ON s.group_id = g.id ORDER BY s.id')->fetchAll();

foreach ($signs as $sign) {
    $filename = str_replace('.', '', $sign['sign_code']) . '.svg';
    $filepath = $signsDir . $filename;

    $svg = generateSignSvg($sign['sign_code'], $sign['name'], $sign['sign_prefix'], $sign['group_name']);
    file_put_contents($filepath, $svg);

    // Update DB
    $stmt = $pdo->prepare('UPDATE traffic_signs SET image = :img WHERE id = :id');
    $stmt->execute(['img' => 'assets/uploads/signs/' . $filename, 'id' => $sign['id']]);

    echo "  [OK] {$sign['sign_code']} -> assets/uploads/signs/{$filename}\n";
}

$signCount = count($signs);
echo "Done: {$signCount} sign images generated.\n\n";

// ============================================================
// 2. News thumbnails: download from picsum.photos
// ============================================================
echo "=== NEWS THUMBNAILS ===\n";

$newsArticles = $pdo->query('SELECT id, title FROM news ORDER BY id')->fetchAll();

// Topic keywords mapped by news content theme
$topics = [
    1 => 'traffic-camera',
    2 => 'search-guide',
    3 => 'law-regulation',
    4 => 'traffic-violation',
    5 => 'police',
    6 => 'road-sign',
    7 => 'license-plate',
    8 => 'highway-construction',
];

foreach ($newsArticles as $news) {
    $id = $news['id'];
    $seed = ($topics[$id] ?? 'traffic') . '-' . $id;
    $url = "https://picsum.photos/seed/{$seed}/640/360";

    $filename = 'news_' . $id . '.jpg';
    $filepath = $uploadDir . $filename;

    $imgData = @file_get_contents($url);
    if ($imgData !== false && strlen($imgData) > 1000) {
        file_put_contents($filepath, $imgData);

        $stmt = $pdo->prepare('UPDATE news SET thumbnail = :thumb WHERE id = :id');
        $stmt->execute(['thumb' => 'assets/uploads/' . $filename, 'id' => $id]);
        echo "  [OK] News #{$id}: {$news['title']} -> assets/uploads/{$filename}\n";
    } else {
        echo "  [FAIL] News #{$id}: Could not download {$url}\n";
    }
}

echo "\n=== ALL DONE ===\n";
echo "Signs: {$signCount} SVG files in public/assets/uploads/signs/\n";
echo "News: " . count($newsArticles) . " JPG files in public/assets/uploads/\n";

// ============================================================
// Helper: Generate SVG for traffic sign
// ============================================================
function generateSignSvg(string $code, string $name, string $prefix, string $groupName): string
{
    $colors = match ($prefix) {
        'P' => ['bg' => '#FFFFFF', 'border' => '#E53935', 'text' => '#333333', 'accent' => '#E53935'],
        'W' => ['bg' => '#FFEB3B', 'border' => '#E53935', 'text' => '#333333', 'accent' => '#000000'],
        'R' => ['bg' => '#1E88E5', 'border' => '#1E88E5', 'text' => '#FFFFFF', 'accent' => '#FFFFFF'],
        'S' => ['bg' => '#1E88E5', 'border' => '#1E88E5', 'text' => '#FFFFFF', 'accent' => '#FFFFFF'],
        default => ['bg' => '#FFFFFF', 'border' => '#757575', 'text' => '#333333', 'accent' => '#757575'],
    };

    $shape = '';
    $symbol = getSymbolSvg($code, $colors['accent']);

    if ($prefix === 'W') {
        // Triangle for warning signs
        $shape = '<polygon points="100,10 190,170 10,170" fill="' . $colors['bg'] . '" stroke="' . $colors['border'] . '" stroke-width="6"/>';
    } elseif ($prefix === 'P') {
        // Circle for prohibition signs
        $shape = '<circle cx="100" cy="100" r="90" fill="' . $colors['bg'] . '" stroke="' . $colors['border'] . '" stroke-width="8"/>';
    } elseif ($prefix === 'R') {
        // Circle for mandatory signs
        $shape = '<circle cx="100" cy="100" r="90" fill="' . $colors['bg'] . '" stroke="' . $colors['border'] . '" stroke-width="3"/>';
    } else {
        // Rectangle for information signs
        $shape = '<rect x="15" y="15" width="170" height="170" rx="8" fill="' . $colors['bg'] . '" stroke="' . $colors['border'] . '" stroke-width="3"/>';
    }

    $codeY = ($prefix === 'W') ? 195 : 190;
    $codeColor = ($prefix === 'W') ? '#333333' : $colors['text'];

    return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">
    {$shape}
    {$symbol}
    <text x="100" y="{$codeY}" text-anchor="middle" font-family="Arial, sans-serif" font-size="11" font-weight="bold" fill="{$codeColor}">{$code}</text>
</svg>
SVG;
}

function getSymbolSvg(string $code, string $color): string
{
    $num = (int) substr($code, strpos($code, '.') + 1);

    return match ($code) {
        'P.101' => '<line x1="10" y1="10" x2="190" y2="190" stroke="' . $color . '" stroke-width="8"/>',
        'P.102' => '<text x="100" y="105" text-anchor="middle" font-size="60" fill="' . $color . '">↔</text>',
        'P.103' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">🚗</text>',
        'P.104' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">🏍</text>',
        'P.105' => '<text x="100" y="110" text-anchor="middle" font-size="45" fill="' . $color . '">🚗🏍</text>',
        'P.106' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">🚛</text>',
        'P.107' => '<text x="100" y="110" text-anchor="middle" font-size="45" fill="' . $color . '">🚌🚛</text>',
        'P.115' => '<text x="100" y="110" text-anchor="middle" font-size="40" fill="' . $color . '">⚖</text>',
        'P.123' => '<text x="100" y="110" text-anchor="middle" font-size="65" fill="' . $color . '">↰</text>',
        'P.124' => '<text x="100" y="110" text-anchor="middle" font-size="65" fill="' . $color . '">↱</text>',
        'P.125' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">↶</text>',
        'P.127' => '<text x="100" y="110" text-anchor="middle" font-size="45" fill="' . $color . '">⏱</text>',
        'P.130' => '<text x="100" y="105" text-anchor="middle" font-size="65" fill="' . $color . '">🅇</text>',
        'P.131' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">🅿</text>',
        'W.201' => '<text x="100" y="105" text-anchor="middle" font-size="55" fill="' . $color . '">↝</text>',
        'W.202' => '<text x="100" y="105" text-anchor="middle" font-size="55" fill="' . $color . '">↝↝</text>',
        'W.205' => '<text x="100" y="105" text-anchor="middle" font-size="50" fill="' . $color . '">+</text>',
        'W.208' => '<text x="100" y="105" text-anchor="middle" font-size="55" fill="' . $color . '">⛕</text>',
        'W.210' => '<text x="100" y="105" text-anchor="middle" font-size="55" fill="' . $color . '">🚂</text>',
        'W.211' => '<text x="100" y="105" text-anchor="middle" font-size="50" fill="' . $color . '">🚂</text>',
        'W.215' => '<text x="100" y="105" text-anchor="middle" font-size="55" fill="' . $color . '">≋</text>',
        'W.221' => '<text x="100" y="105" text-anchor="middle" font-size="50" fill="' . $color . '">⫼</text>',
        'W.224' => '<text x="100" y="105" text-anchor="middle" font-size="55" fill="' . $color . '">🚶</text>',
        'W.225' => '<text x="100" y="105" text-anchor="middle" font-size="55" fill="' . $color . '">👤</text>',
        'W.234' => '<text x="100" y="105" text-anchor="middle" font-size="50" fill="' . $color . '">🚦</text>',
        'R.301' => '<text x="100" y="115" text-anchor="middle" font-size="65" fill="' . $color . '">↑</text>',
        'R.302' => '<text x="100" y="115" text-anchor="middle" font-size="65" fill="' . $color . '">→</text>',
        'R.303' => '<text x="100" y="115" text-anchor="middle" font-size="65" fill="' . $color . '">←</text>',
        'R.306' => '<text x="100" y="115" text-anchor="middle" font-size="45" fill="' . $color . '">⏱</text>',
        'R.403' => '<text x="100" y="115" text-anchor="middle" font-size="55" fill="' . $color . '">🛒</text>',
        'R.412' => '<text x="100" y="115" text-anchor="middle" font-size="55" fill="' . $color . '">🚶</text>',
        'S.401' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">🛣</text>',
        'S.403' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">🅿</text>',
        'S.406' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">⛽</text>',
        'S.407' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">🔧</text>',
        'S.414' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">📞</text>',
        'S.418' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">🏥</text>',
        'S.422' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">🏨</text>',
        'S.501' => '<text x="100" y="110" text-anchor="middle" font-size="45" fill="' . $color . '">📏</text>',
        'S.503' => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">↕</text>',
        default => '<text x="100" y="110" text-anchor="middle" font-size="55" fill="' . $color . '">⚠</text>',
    };
}
