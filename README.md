# FixedToken
Generate and verify tokens based on a secret and some public data

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