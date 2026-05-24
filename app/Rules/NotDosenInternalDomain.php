<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Cegah penggunaan domain email yang direservasi untuk akun dosen internal
 * (pengajar/penguji/kaprodi telkomuniversity).
 */
class NotDosenInternalDomain implements ValidationRule
{
    public const RESERVED_DOMAINS = [
        'pengajar.telkomuniversity.ac.id',
        'penguji.telkomuniversity.ac.id',
        'kaprodi.telkomuniversity.ac.id',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) return;

        $email = strtolower($value);
        foreach (self::RESERVED_DOMAINS as $domain) {
            if (str_ends_with($email, '@' . $domain)) {
                $fail('Domain email tersebut direservasi untuk akun dosen internal dan tidak dapat digunakan.');
                return;
            }
        }
    }
}
