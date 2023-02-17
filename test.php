<?php

$content = file_get_contents(__DIR__ . '/composer.json');
$salt = openssl_random_pseudo_bytes(8);

$derivatedKey = openssl_pbkdf2(
    '9fb77d8882d0ab935a60f04c7d31a266df5398897d795a9e3f6daf43bcbf5998',
    $salt,

    // key_length is 48 bytes because
    // the key itself is 32 bytes (256 bits, because aes 256)
    // and the IV is 16 bytes (returned by openssl_cipher_iv_length)
    // so 32+16 -> 48
    key_length: 48,

    // 10000 is a of 2021 the amount recommended by the NIST
    // see https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-63b.pdf section 5.1.1.2
    // quote:
    // For PBKDF2, the cost factor is an iteration count: the more times the PBKDF2 function is
    // iterated, the longer it takes to compute the password hash. Therefore, the iteration count
    // SHOULD be as large as verification server performance will allow, typically at least 10,000
    // iterations.
    iterations: 10000,

    digest_algo: 'sha256',
);

// the key itself is 32 bytes (i.e 256 bits, because aes *256*)
$key = mb_substr($derivatedKey, 0, 32, '8bit');
$iv = mb_substr($derivatedKey, 32, openssl_cipher_iv_length('aes-256-cbc'), '8bit');
// 16 is the 8 bytes of `Salted__`  and 8 bytes of salt itself
$cypherText = mb_substr($content, 0, encoding: '8bit');

file_put_contents(__DIR__ . '/crypt', 'Salted__' . $salt . openssl_encrypt($cypherText, 'aes-256-cbc', $key, true, $iv));