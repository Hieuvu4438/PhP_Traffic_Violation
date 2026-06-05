<?php
namespace App\Core;

/**
 * Validate dữ liệu đầu vào
 */
class Validator
{
    private array $errors = [];

    /**
     * Validate biển số xe Việt Nam
     * Format: 30A-12345, 59F1-12345, 30A12345, 59F112345
     */
    public static function plateNumber(string $plate): bool
    {
        $plate = strtoupper(trim($plate));
        // Cho phép có hoặc không có dấu gạch ngang
        $pattern = '/^\d{2}[A-Z]\d?[-\s]?\d{4,5}$/';
        return (bool) preg_match($pattern, $plate);
    }

    public static function email(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function phone(string $phone): bool
    {
        // Số điện thoại VN: 10 số, bắt đầu bằng 0
        $phone = preg_replace('/\s+/', '', $phone);
        return (bool) preg_match('/^0\d{9}$/', $phone);
    }

    public static function required(mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }
        return $value !== null;
    }

    public static function minLength(string $value, int $min): bool
    {
        return mb_strlen(trim($value)) >= $min;
    }

    public static function maxLength(string $value, int $max): bool
    {
        return mb_strlen(trim($value)) <= $max;
    }

    public static function numeric(mixed $value): bool
    {
        return is_numeric($value);
    }

    /**
     * Validate một mảng dữ liệu theo rules
     * Rules format: ['field' => 'required|min:3|max:255']
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $params = explode(':', $rule);
                $ruleName = $params[0];
                $ruleParam = $params[1] ?? null;

                $method = 'validate' . ucfirst($ruleName);
                if (method_exists($this, $method)) {
                    $this->{$method}($field, $value, $ruleParam);
                }
            }
        }

        return empty($this->errors);
    }

    private function validateRequired(string $field, mixed $value, ?string $param): void
    {
        if (!self::required($value)) {
            $this->errors[$field][] = "Trường này không được để trống.";
        }
    }

    private function validateEmail(string $field, mixed $value, ?string $param): void
    {
        if ($value && !self::email($value)) {
            $this->errors[$field][] = "Email không đúng định dạng.";
        }
    }

    private function validatePhone(string $field, mixed $value, ?string $param): void
    {
        if ($value && !self::phone($value)) {
            $this->errors[$field][] = "Số điện thoại không đúng định dạng (10 số, bắt đầu bằng 0).";
        }
    }

    private function validatePlateNumber(string $field, mixed $value, ?string $param): void
    {
        if ($value && !self::plateNumber($value)) {
            $this->errors[$field][] = "Biển số xe không đúng định dạng (VD: 30A-12345).";
        }
    }

    private function validateMin(string $field, mixed $value, ?string $param): void
    {
        if ($value && $param && !self::minLength($value, (int) $param)) {
            $this->errors[$field][] = "Tối thiểu {$param} ký tự.";
        }
    }

    private function validateMax(string $field, mixed $value, ?string $param): void
    {
        if ($value && $param && !self::maxLength($value, (int) $param)) {
            $this->errors[$field][] = "Tối đa {$param} ký tự.";
        }
    }

    private function validateNumeric(string $field, mixed $value, ?string $param): void
    {
        if ($value !== null && $value !== '' && !self::numeric($value)) {
            $this->errors[$field][] = "Phải là số.";
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}
