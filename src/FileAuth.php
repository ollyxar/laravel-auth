<?php namespace Ollyxar\LaravelAuth;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Encryption\Encrypter;
use Illuminate\Auth\SessionGuard;

/**
 * Class FileAuth
 * @package Ollyxar\LaravelAuth
 */
class FileAuth
{
    /**
     * Get userId by request headers.
     *
     * @param array $headers
     * @return bool|int
     */
    public static function getUserIdByHeaders(array $headers)
    {
        $cookieName = env(
            'SESSION_COOKIE',
            str_slug(env('APP_NAME', 'laravel'), '_') . '_session'
        );

        if (!isset($headers['Cookie'])) {
            return false;
        }

        preg_match("/(^|;)\s*$cookieName\s*=\s*([^;|^\n]+)/", $headers['Cookie'], $match);

        if (!isset($match[2]) || empty($match[2])) {
            return false;
        }

        $cookie = urldecode($match[2]);
        $key = base64_decode(explode(':', env('APP_KEY'))[1]);
        $serialized = true;

        if (method_exists(EncryptCookies::class, 'serialized')) {
            $serialized = EncryptCookies::serialized($cookieName);
        }

        $sessionName = (new Encrypter($key, config('app.cipher')))->decrypt($cookie, $serialized);

        if (!$sessionFile = @file_get_contents(config('session.files') . '/' . $sessionName)) {
            return false;
        } else {
            $object = @unserialize($sessionFile);
            $userId = @$object['login_web_' . sha1(SessionGuard::class)];
            return $userId;
        }
    }
}