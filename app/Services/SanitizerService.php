<?php

// app/Services/SanitizerService.php

namespace App\Services;

class SanitizerService
{
    public function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        return $this->sanitizeString($data);
    }

    protected function sanitizeString($data)
    {
        // Remove HTML tags and encode special characters
        $cleaned = htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');

        // Remove JavaScript event handlers
        return preg_replace('/\s*on\w+="[^"]*"/i', '', $cleaned);
    }
}
