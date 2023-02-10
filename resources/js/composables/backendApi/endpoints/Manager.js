import { useUserStorage } from "../../../storage/pinia/userStorage";

export default class Manager{

    constructor(httpClient){
        this.httpClient = httpClient
    }

    async infoById(id){
        const endpoint = `/manager/${id}/info`;

        return await this.httpClient.get(endpoint);
    }

    async sendMessageById(managerId, message){
        const endpoint = `manager/${managerId}/send-message`;

        const body = {
            email: useUserStorage().email,
            phone: useUserStorage().phone,
            message: message,
        };

        return await this.httpClient.post(endpoint, body)
    }


}
