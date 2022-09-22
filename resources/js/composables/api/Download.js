import BaseApi from "./BaseApi";
import axios from "axios";

export default class Download extends BaseApi{

    async invoiceDocumentById(documentId, password, filename){

        const methodUrl = new URL(`download/invoice-documents/${documentId}`, new URL(this.baseUrl).toString());

        const fileBinary = (await (await axios.post(methodUrl.toString(), {
            id: 'test'
        }, {
            responseType: 'blob',
        })).data);

        await this.decryptFile(fileBinary, password, filename);
    }

    async decryptFile(base64Blob, password, filename) {

        base64Blob = new Uint8Array(await base64Blob.arrayBuffer());

        const pbkdf2iterations = 10000;
        const passwordBytes = (new TextEncoder()).encode(password);
        const pbkdf2salt = base64Blob.slice(8, 16);

        const passKey = await window.crypto.subtle.importKey(
            'raw', passwordBytes, 'PBKDF2',
            false, ['deriveBits']
        );

        console.log('passKey imported');

        let pbkdf2bytes = await window.crypto.subtle.deriveBits({
            "name": 'PBKDF2', "salt": pbkdf2salt, "iterations": pbkdf2iterations, "hash": 'SHA-256'
        }, passKey, 384);

        console.log('pbkdf2bytes derived');

        pbkdf2bytes = new Uint8Array(pbkdf2bytes);

        const keyBytes = pbkdf2bytes.slice(0,32);
        const ivBytes = pbkdf2bytes.slice(32);
        base64Blob = base64Blob.slice(16);

        const key = await window.crypto.subtle.importKey('raw', keyBytes, {
            name: 'AES-CBC',
            length: 256
        }, false, ['decrypt'])

        console.log('key imported');

        let plaintextBytes = await window.crypto.subtle.decrypt({name: "AES-CBC", iv: ivBytes}, key, base64Blob);

        console.log('ciphertext decrypted');

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

}

//openssl aes-256-cbc -e -salt -pbkdf2 -iter 10000 -in plaintextfilename -out encryptedfilename

//openssl_encrypt(
//  string $data - сами данные архивчика, лучше пусть не в base64,
//  'aes-256-cbc',
//  string $password - пароль пользователя
//
// )
