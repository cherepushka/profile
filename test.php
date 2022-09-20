<?php

$content = file_get_contents('./test.zip');

$encryption_key = openssl_random_pseudo_bytes(1);
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

var_dump($encryption_key);

file_put_contents(
    'test-encrypted.zip',
    openssl_encrypt($content, 'aes-256-cbc', $encryption_key, OPENSSL_RAW_DATA, $iv)
);
