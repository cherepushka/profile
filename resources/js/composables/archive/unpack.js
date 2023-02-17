import {Logger} from '../../bootstrap';
import CryptoJS from 'crypto-js';

export const decryptAndDownload = async (base64Blob, password, filename) => {

    base64Blob = new Uint8Array(await base64Blob.arrayBuffer());

    const pbkdf2iterations = 10000;
    const passwordBytes = (new TextEncoder("utf-8")).encode(password);
    const pbkdf2salt = base64Blob.slice(8, 16);

    // console.log(passwordBytes)

    // const passKey = await window.crypto.subtle.importKey(
    //     'raw', 
    //     passwordBytes, 
    //     {name: 'AES-CBC', length: 256},
    //     false, 
    //     ['decrypt']
    // );

    // return;

    // Logger.debug('passKey imported');

    // let pbkdf2bytes = await window.crypto.subtle.deriveBits(
    //     {
    //         "name": 'PBKDF2', 
    //         "salt": pbkdf2salt, 
    //         "iterations": pbkdf2iterations, 
    //         "hash": 'SHA-256'
    //     }, 
    //     passKey, 
    //     384
    // );

    // Logger.debug('pbkdf2bytes derived');

    // pbkdf2bytes = new Uint8Array(pbkdf2bytes);

    // const keyBytes = pbkdf2bytes.slice(0,32);

    // const key = await window.crypto.subtle.importKey(
    //     'raw', 
    //     keyBytes, 
    //     {name: 'AES-CBC', length: 256},
    //     false, 
    //     ['decrypt']
    // );

    Logger.debug('key imported');

    let plaintextBytes = await window.crypto.subtle.decrypt(
        {name: "AES-CBC", iv: base64Blob.slice(0, 16)}, 
        password,
        base64Blob.slice(17)
    );

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
};