<?php

namespace App\Packages\Crypto;

use RuntimeException;

class File
{

    /**
     * Шифрование файла
     *
     * openssl v1.1.1 command:
     * openssl aes-256-cbc -e -salt -pbkdf2 -iter 10000 -in 'путь до входного файла' -out 'путь до выходного файла'
     *
     * @param string $filepath - путь до локального файла
     * @param string $password - пароль, с помощью которого файл будет зашифрован
     *
     * @throws RuntimeException
     */
    public static function encrypt(string $filepath, string $password): void
    {
        $content = file_get_contents($filepath);
        $salt = openssl_random_pseudo_bytes(8);

        $derivatedKey = openssl_pbkdf2(
            $password,
            $salt,
            // key_length is 48 bytes because
            // the key itself is 32 bytes (256 bits, because aes 256)
            // and the IV is 16 bytes (returned by openssl_cipher_iv_length)
            // so 32+16 -> 48
            key_length: 48,
            // 10000 is a of 2021 the amount recommended by the NIST
            // see https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-63b.pdf section 5.1.1.2
            iterations: 10000,
            digest_algo: 'sha256',
        );

        // the key itself is 32 bytes (i.e 256 bits, because aes *256*)
        $key = mb_substr($derivatedKey, 0, 32, '8bit');
        $iv = mb_substr($derivatedKey, 32, openssl_cipher_iv_length('aes-256-cbc'), '8bit');
        // 16 is the 8 bytes of `Salted__`  and 8 bytes of salt itself
        $cypherText = mb_substr($content, 0, encoding: '8bit');

        $bin_prefix = 'Salted__'; // для совместимости с терминальным вызовом openssl

        $encrypted_content = openssl_encrypt($cypherText, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($encrypted_content === false) {
            throw new RuntimeException('Encrypt error: ' . openssl_error_string());
        }

        $encrypted_content = $bin_prefix . $salt . $encrypted_content;
        if (file_put_contents($filepath, $encrypted_content) === false) {
            throw new RuntimeException("Error while saving encrypted file: '$filepath'");
        }
    }

    /**
     * Расшифрока файлов и каталогов
     *
     * openssl v1.1.1 command:
     * openssl aes-256-cbc -d -salt -pbkdf2 -iter 10000 -in 'путь до входного файла' -out 'путь до выходного файла'
     *
     * @param string $filepath - путь до локального файла
     * @param string $password - пароль, с помощью которого файл будет расшифрован
     *
     * @return string
     */
    public static function decrypt(string $filepath, string $password): string
    {
        $file = fopen($filepath, 'r');
        if(!$file){
            throw new RuntimeException('Не удалось открыть файл: ' . $filepath);
        }

        $keyBytes = stream_get_contents($file, length: 16);
        // 16 is the 8 bytes of `Salted__`  and 8 bytes of salt itself
        $cypherText = stream_get_contents($file, offset: 16);

        $salt = mb_substr($keyBytes, 8, 8, '8bit');

        $derivatedKey = openssl_pbkdf2(
            $password,
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

        $decrypted_content = openssl_decrypt($cypherText, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted_content === false){
            throw new RuntimeException('Decrypt error: ' . openssl_error_string());
        }

        return $decrypted_content;
    }

}
