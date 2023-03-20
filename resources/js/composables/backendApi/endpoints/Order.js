export default class Order{

    constructor(httpClient){
        this.httpClient = httpClient
    }

    async list({page, order, sort}){
        const endpoint = `/order/list/${page}`;

        let sortArr = [];
        if(sort){
            sort.split(',').forEach(item => {
                sortArr.push(item)
            });
        }
        

        const body = {
            order: order,
            sort: sortArr,
        }

        return await this.httpClient.post(endpoint, body);
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
