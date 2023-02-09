export default class Order{

    constructor(httpClient){
        this.httpClient = httpClient
    }

    async list(pageNum = 1){
        const endpoint = `/order/list/${pageNum}`;

        return await this.httpClient.get(endpoint);
    }

    async orderById(orderId){
        const endpoint = `/order/${orderId}`;

        return await this.httpClient.get(endpoint);
    }

    async setCustomValue(orderId, value){
        const endpoint = `/order/${orderId}/edit/custom-value`;

        const body = {
            value: value
        };

        return await this.httpClient.post(endpoint, body)
    }

}