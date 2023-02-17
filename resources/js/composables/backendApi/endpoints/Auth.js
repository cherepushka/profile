export default class Auth{

    constructor(httpClient){
        this.httpClient = httpClient
    }

    async login({email, phone, password}){
        const endpoint = '/auth/login';

        const body = {
            email,
            phone,
            password,
        };

        return await this.httpClient.post(endpoint, body);
    }

    async smsResend({email, phone, password}){
        const endpoint = '/auth/sms/resend';

        const body = {
            email,
            phone,
            password,
        };

        return await this.httpClient.post(endpoint, body)
    }

    async smsSend({email, phone, password, smsCode}){
        const endpoint = '/auth/sms/send';

        const body = {
            email,
            phone,
            password,
            sms_code: smsCode,
        };

        return await this.httpClient.post(endpoint, body)
    }
}
