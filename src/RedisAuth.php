<?php namespace Ollyxar\LaravelAuth;

use Illuminate\Encryption\Encrypter;
use Illuminate\Auth\SessionGuard;
use Predis\Client as Redis;

/**
 * Class
 * @package Ollyxar\LaravelAuth
 */
class RedisAuth
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

        $sessionName = (new Encrypter($key, config('app.cipher')))->decrypt($cookie);

        $redis = new Redis([
            'scheme' => 'tcp',
            'host'   => env('REDIS_HOST', '127.0.0.1'),
            'port'   => env('REDIS_PORT', 6379)
        ]);

        if (!$sessionFile = @$redis->get(config('cache.prefix', 'laravel') . ':' . $sessionName)) {
            return false;
        } else {
            $object = @unserialize($sessionFile);
            $userId = @$object['login_web_' . sha1(SessionGuard::class)];
            return $userId;
        }
    }
}