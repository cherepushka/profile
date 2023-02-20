import {Logger} from '../../bootstrap';

/**
 * https://github.com/meixler/web-browser-based-file-encryption-decryption
 * 
 * @param {Blob} base64Blob 
 * @param {string} password 
 * @param {string} filename 
 */
export const decryptAndDownload = async (base64Blob, password, filename) => {

    let cipherbytes = new Uint8Array(await base64Blob.arrayBuffer());

    const pbkdf2iterations = 10000;
    const passphrasebytes = new TextEncoder("utf-8").encode(password);
    const pbkdf2salt = cipherbytes.slice(8,16);

    var passphrasekey = await window.crypto.subtle.importKey(
        'raw', 
        passphrasebytes, 
        {name: 'PBKDF2'}, 
        false, 
        ['deriveBits']
    ).catch((err) => {
        Logger.error(err);
    });

    Logger.debug('passphrasekey imported');

    var pbkdf2bytes=await window.crypto.subtle.deriveBits(
        {"name": 'PBKDF2', "salt": pbkdf2salt, "iterations": pbkdf2iterations, "hash": 'SHA-256'}, 
        passphrasekey, 
        384
    ).catch((err) => {
        Logger.error(err);
    });

    Logger.debug('pbkdf2bytes derived');

    pbkdf2bytes=new Uint8Array(pbkdf2bytes);

    const keybytes = pbkdf2bytes.slice(0,32);
    const ivbytes = pbkdf2bytes.slice(32);
    cipherbytes = cipherbytes.slice(16);

    const key = await window.crypto.subtle.importKey(
        'raw', 
        keybytes, 
        {name: 'AES-CBC', length: 256}, 
        false, 
        ['decrypt']
    ) .catch((err) => {
        Logger.error(err);
    });

    Logger.debug('key imported');		

    let plaintextbytes = await window.crypto.subtle.decrypt(
        {name: "AES-CBC", iv: ivbytes}, 
        key, 
        cipherbytes
    ).catch((err) => {
        Logger.error(err);
    });

    Logger.debug('ciphertext decrypted');

    const blob = new Blob(
        [new Uint8Array(plaintextbytes)], 
        {type: 'application/download'}
    );

    const link = document.createElement("a");
    document.body.appendChild(link);
    link.style.display = "none";

    const objUrl = URL.createObjectURL(blob);

    link.href = objUrl;
    link.download = filename;
    link.click();

    window.URL.revokeObjectURL(objUrl);
};