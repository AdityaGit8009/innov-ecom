<?php

class JWT
{
    private static function signingKey(): string
    {
        $k = getenv('JWT_SECRET');
        if ($k !== false && $k !== '') {
            return $k;
        }
        return 'dev-only-change-JWT_SECRET-on-production';
    }

    public static function encode($payload)
    {
        $key = self::signingKey();
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $body   = base64_encode(json_encode($payload));
        $sign   = base64_encode(hash_hmac('sha256', "$header.$body", $key, true));
        return "$header.$body.$sign";
    }

    public static function decode($token)
    {
        $key = self::signingKey();
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            return false;
        }
        [$h, $b, $s] = $parts;
        $check = base64_encode(hash_hmac('sha256', "$h.$b", $key, true));
        if (!hash_equals($check, $s)) {
            return false;
        }
        return json_decode(base64_decode($b), true);
    }
}
