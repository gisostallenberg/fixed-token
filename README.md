# FixedToken
Generate and verify tokens based on a secret and some public data

> ## ⚠️ Do not use this package
>
> It is insecure and unnecessary. Two flaws, both fatal for a security token:
>
> 1. **Length-extension attack.** `generate()` returns `sha1($secret . $data)`.
>    SHA-1 is a Merkle–Damgård hash, so anyone holding a single valid
>    `(data, token)` pair can forge a valid token for extended data **without
>    knowing the secret**. The construction that prevents this is HMAC.
> 2. **Timing attack.** `verify()` compares with `===`, which stops at the first
>    differing byte and leaks the match length, allowing a byte-by-byte forgery.
>    MAC comparison must be constant-time (`hash_equals()`).
>
> Both are solved by PHP's standard library — no dependency needed. `hash_hmac`
> and `hash_equals` are built in (`hash_equals` since PHP 5.6):
>
> ```php
> $payload = http_build_query($data); // ksort($data) first for a stable order
>
> // generate
> $token = hash_hmac('sha256', $payload, $secret);
>
> // verify (constant-time)
> hash_equals($token, $givenToken); // bool
> ```
>
> If you need expiry, claims or revocation rather than a bare tag, use a
> maintained, reviewed library such as
> [paragonie/paseto](https://github.com/paragonie/paseto) or
> [firebase/php-jwt](https://github.com/firebase/php-jwt).

## Installation
```bash
composer require gisostallenberg/fixed-token
```

## Usage example
```php
<?php

use GisoStallenberg\FixedToken\FixedToken;

$secret = 'oxQu/H2FZLOK2elkfle8bg./eg';
$data = array(
    'username' => 'giso',
);

$token = FixedToken::create($secret)
    ->addData($data)
    ->generate();

FixedToken::create($secret)
    ->addData($data)
    ->verify($token); // should return true
```