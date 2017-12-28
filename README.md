# Laravel auth helper

![Version](https://poser.pugx.org/ollyxar/laravel-auth/v/stable.svg)
![Downloads](https://poser.pugx.org/ollyxar/laravel-auth/d/total.svg)
![License](https://poser.pugx.org/ollyxar/laravel-auth/license.svg)

Get user's id from session identified by cookie

### Why auth helper?

Auth helper designed for external PHP application authentication. It helps developers to get `user_id` from their session without any auth-token or any authorize process.

### How it's work?

Suppose you have some PHP server application that need to know who is making request. It can be some artisan command requiring authorization or WebSocket server in another port that is listening connections.

In the browser user go to some address (obviously it must have the same domain). Thus browser sends current headers to your application. And then you can use `Cookie` from the headers to define client.

![License](https://ollyxar.com/upload/images/headers.jpg)

### Example

Here is a simple example how to determine `user_id`

```php
// if the sessions are in files by default
use Ollyxar\LaravelAuth\FileAuth;

// or if the sessions in the Redis
// use Ollyxar\LaravelAuth\RedisAuth;

// The same code for Redis except you have to call RedisAuth instead
if ($userId = FileAuth::getUserIdByHeaders($headers)) {
    // we got it!
}
```

You have to provide correct `$headers` array that must content Cookie item. Otherwise `$user_id` will return `false`

The headers should be like:

```php
$headers = [
    'Cookie' => 'someotherkey=someothervalue;laravel_session=somerandomstring'
];
```

Please notice that auth helper use the same native methods / functions to find session by cookie.