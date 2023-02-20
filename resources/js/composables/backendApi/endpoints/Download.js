
export default class Download{

    constructor(httpClient){
        this.httpClient = httpClient
    }

    async allDocuments(orderId){
        const endpoint = `/invoice-documents/all/${orderId}`;

        const fileBinary = 
            (await this.httpClient.get(endpoint, {responseType: 'blob'}))
            .data;

        return fileBinary;
    }

    /**
     * @returns Uint8Array
    **/
    async documentById(documentId){

        const endpoint = `/download/invoice-documents/${documentId}`;

        const fileBinary = 
            (await this.httpClient.get(endpoint, {responseType: 'blob'}))
            .data;

        return fileBinary;
    }
}