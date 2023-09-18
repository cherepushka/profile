export default class Order{

    constructor(httpClient){
        this.httpClient = httpClient
    }

    async list({page, sort, filters}){
        const endpoint = `/order/list/${page}`;

        const body = {
            order: sort,
            sort: filters,
        }

        return await this.httpClient.post(endpoint, body);
    }

    async orderById(orderId){
        const endpoint = `/order/${orderId}`;

        return await this.httpClient.get(endpoint);
    }

    async setCustomValue(orderId, value){
        const endpoint = `/custom-value/edit/${orderId}`;

        const body = {
            value: value
        };

        return await this.httpClient.post(endpoint, body)
    }

    async deliveryStatuses() {
        return await this.httpClient.get(`/order/delivery-statuses`)
    }

}
