export default class User{

    constructor(httpClient){
        this.httpClient = httpClient
    }

    async userInfo(){
        const endpoint = '/user/info';

        return await this.httpClient.get(endpoint);
    }

    async logout(){
        const endpoint = '/user/logout';

        return await this.httpClient.get(endpoint);
    }
}
