import {Logger} from '../../bootstrap'

export const decryptAndDownload = async (base64Blob, password, filename) => {

    base64Blob = new Uint8Array(await base64Blob.arrayBuffer());

    const pbkdf2iterations = 10000;
    const passwordBytes = (new TextEncoder()).encode(password);
    const pbkdf2salt = base64Blob.slice(8, 16);

    const passKey = await window.crypto.subtle.importKey(
        'raw', passwordBytes, 'PBKDF2',
        false, ['deriveBits']
    );

    Logger.debug('passKey imported');

    let pbkdf2bytes = await window.crypto.subtle.deriveBits({
        "name": 'PBKDF2', "salt": pbkdf2salt, "iterations": pbkdf2iterations, "hash": 'SHA-256'
    }, passKey, 384);

    Logger.debug('pbkdf2bytes derived');

    pbkdf2bytes = new Uint8Array(pbkdf2bytes);

    const keyBytes = pbkdf2bytes.slice(0,32);
    const ivBytes = pbkdf2bytes.slice(32);
    base64Blob = base64Blob.slice(16);

    const key = await window.crypto.subtle.importKey('raw', keyBytes, {
        name: 'AES-CBC',
        length: 256
    }, false, ['decrypt'])

    Logger.debug('key imported');

    let plaintextBytes = await window.crypto.subtle.decrypt({name: "AES-CBC", iv: ivBytes}, key, base64Blob);

    Logger.debug('ciphertext decrypted');

    plaintextBytes = new Uint8Array(plaintextBytes);

    const blob = new Blob([plaintextBytes], {type: 'application/download'});

    const link = document.createElement("a");
    document.body.appendChild(link);
    link.style.display = "none";

    const objUrl = URL.createObjectURL(blob);

    link.href = objUrl;
    link.download = filename;
    link.click();

    window.URL.revokeObjectURL(objUrl);
}