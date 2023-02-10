
export default class Download{

    constructor(httpClient){
        this.httpClient = httpClient
    }

    //openssl aes-256-cbc -e -salt -pbkdf2 -iter 10000 -in plaintextfilename -out encryptedfilename
    //openssl_encrypt(
    //  string $data - сами данные архивчика, лучше пусть не в base64,
    //  'aes-256-cbc',
    //  string $password - пароль пользователя
    //)
    async invoiceDocumentById(documentId){

        const endpoint = `/download/invoice-documents/${documentId}`;

        const fileBinary = 
            (await this.httpClient.get(endpoint, {}, {responseType: 'blob'}))
            .data;

        return fileBinary;
    }
}