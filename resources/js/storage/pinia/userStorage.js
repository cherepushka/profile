import { defineStore } from "pinia";
import {router} from "../../bootstrap";
import { backendApi } from "../../bootstrap";
import { Logger } from "../../bootstrap";

export const useUserStorage = defineStore('user', {
    state: () => ({
        authorized: undefined,
        userId: null,
        email: localStorage.getItem('user_email'),
        phone: localStorage.getItem('user_phone'),
        password: localStorage.getItem('user_password'),
        authToken: localStorage.getItem('auth_token'),
        registrationDate: null,
    }),
    getters: {
        async isAuthorized(state) {

            if (state.authorized === undefined){
                await this.fetchUserRemoteInfo();
            }
            
            return state.authorized;
        },
    }, 
    actions: {
        async fetchUserRemoteInfo(){
            try {
                backendApi.setAuthUserToken(this.authToken)
                const userInfo = (await backendApi.user().userInfo()).data.data;
                
                this.userId = userInfo.id;
                this.registrationDate = userInfo.registrationDate;

                this.setIsAthorized(true);
            } catch (e) {
                Logger.error(e);
                this.setIsAthorized(false);
            }
        },
        setUserInfo({email, phone, password, userId, registrationDate, authToken}){

            this.email = email || this.email;
            this.phone = phone || this.phone;
            this.password = password || this.password;
            this.userId = userId || this.userId;
            this.registrationDate = registrationDate || this.registrationDate ;
            this.authToken = authToken || this.authToken;

            localStorage.setItem('user_email', email);
            localStorage.setItem('user_phone', phone);
            localStorage.setItem('user_password', password);
            localStorage.setItem('auth_token', authToken)
        },
        setIsAthorized(status) {

            switch (status) {
                case true:

                    this.authorized = true;
                    backendApi.setAuthUserToken(this.authToken)
                    
                    Logger.debug('auth token set to backend API')
                    break;
                case false:

                    if(this.authorized === false){
                        break
                    }

                    this.$reset();

                    backendApi.unsetAuthUserToken()

                    Logger.debug('auth token unset from backend API')
                    
                    localStorage.removeItem('user_email');
                    localStorage.removeItem('user_phone');
                    localStorage.removeItem('user_password');
                    localStorage.removeItem('auth_token')

                    this.authorized = false;

                    router.push({name: 'login_page'});

                    break;
                default:
                    throw new Error('Неизвестное состояние акторизации: ' + status);
            }
        }
    },
});