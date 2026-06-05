<?php
namespace App\Core;

/**
 * Common utility functions
 */
class Helper
{
    /**
     * Generate slug from Vietnamese string
     */
    public static function slug(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = self::removeAccents($str);
        $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
        $str = preg_replace('/[\s-]+/', '-', $str);
        return trim($str, '-');
    }

    /**
     * Remove Vietnamese diacritics
     */
    public static function removeAccents(string $str): string
    {
        $accents = [
            'a' => ['á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ'],
            'd' => ['đ'],
            'e' => ['é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ'],
            'i' => ['í','ì','ỉ','ĩ','ị'],
            'o' => ['ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ'],
            'u' => ['ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự'],
            'y' => ['ý','ỳ','ỷ','ỹ','ỵ'],
        ];

        foreach ($accents as $char => $accented) {
            $str = str_replace($accented, $char, $str);
        }
        return $str;
    }

    /**
     * Format number as VND currency string
     */
    public static function formatCurrency(float|int $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' VND';
    }

    /**
     * Format date in VN format
     */
    public static function formatDate(string $datetime, string $format = 'd/m/Y'): string
    {
        $date = new \DateTime($datetime);
        return $date->format($format);
    }

    /**
     * Format date and time in VN format
     */
    public static function formatDateTime(string $datetime): string
    {
        $date = new \DateTime($datetime);
        return $date->format('d/m/Y H:i');
    }

    /**
     * Excerpt text
     */
    public static function excerpt(string $text, int $length = 150): string
    {
        $text = strip_tags($text);
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . '...';
    }

    /**
     * Upload file
     */
    public static function upload(array $file, string $subDir = ''): string|false
    {
        $config = require __DIR__ . '/../../config/config.php';
        $uploadDir = $config['upload_path'] . ($subDir ? $subDir . '/' : '');

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > $config['upload_max_size']) {
            return false;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $config['upload_allowed_types'])) {
            return false;
        }

        $newName = uniqid() . '_' . time() . '.' . $ext;
        $dest = $uploadDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return 'assets/uploads/' . ($subDir ? $subDir . '/' : '') . $newName;
        }

        return false;
    }

    /**
     * Truncate text safely
     */
    public static function truncate(string $text, int $length = 100): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . '...';
    }

    /**
     * Safely get array value
     */
    public static function arrGet(array $array, string $key, mixed $default = null): mixed
    {
        return $array[$key] ?? $default;
    }
}
