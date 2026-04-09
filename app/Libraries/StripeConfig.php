<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\SiteSettingModel;

/**
 * Resuelve claves Stripe: primero .env (si no es placeholder), luego site_settings.
 */
final class StripeConfig
{
    public static function publicKey(): string
    {
        $env = trim((string) env('STRIPE_PUBLIC_KEY', ''));
        if ($env !== '' && ! self::isPlaceholderPublic($env)) {
            return $env;
        }

        $db = model(SiteSettingModel::class)->get('stripe_public_key');

        return $db !== null ? trim($db) : '';
    }

    public static function secretKey(): string
    {
        $env = trim((string) env('STRIPE_SECRET_KEY', ''));
        if ($env !== '' && ! self::isPlaceholderSecret($env)) {
            return $env;
        }

        $db = model(SiteSettingModel::class)->get('stripe_secret_key');

        return $db !== null ? trim($db) : '';
    }

    public static function isPlaceholderPublic(string $key): bool
    {
        return $key === ''
            || str_starts_with($key, 'pk_test_xxx')
            || str_starts_with($key, 'pk_live_xxx');
    }

    public static function isPlaceholderSecret(string $key): bool
    {
        return $key === ''
            || str_starts_with($key, 'sk_test_xxx')
            || str_starts_with($key, 'sk_test_yyy')
            || str_starts_with($key, 'sk_live_xxx');
    }

    public static function paymentsReady(): bool
    {
        $pk = self::publicKey();
        $sk = self::secretKey();

        return $pk !== '' && ! self::isPlaceholderPublic($pk)
            && $sk !== '' && ! self::isPlaceholderSecret($sk);
    }
}
