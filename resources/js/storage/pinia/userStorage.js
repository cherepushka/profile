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
        registrationDate: null,
    }),
    getters: {
        async isAuthorized(state) {

            if (state.authorized === undefined){
                await useUserStorage().fetchUserRemoteInfo();
            }
            
            return state.authorized;
        },
    }, 
    actions: {
        async fetchUserRemoteInfo(){
            try {
                const userInfo = (await backendApi.user().userInfo()).data;
                
                this.userId = userInfo.id;
                this.registrationDate = userInfo.registration_date;

                this.setIsAthorized(true);
            } catch (e) {
                Logger.error(e);
                this.setIsAthorized(false);
            }
        },
        setUserInfo({email, phone, password, userId, registrationDate}){

            this.email = this.email || email;
            this.phone = this.phone || phone;
            this.password = this.password || password;
            this.userId = this.userId || userId;
            this.registrationDate = this.registrationDate || registrationDate;

            localStorage.setItem('user_email', email);
            localStorage.setItem('user_phone', phone);
            localStorage.setItem('user_password', password);
        },
        setIsAthorized(status) {

            switch (status) {
                case true:

                    this.authorized = true;

                    break;
                case false:

                    this.$reset();
                    
                    localStorage.removeItem('user_email');
                    localStorage.removeItem('user_phone');
                    localStorage.removeItem('user_password');

                    this.authorized = false;

                    router.push({name: 'login_page'});

                    break;
                default:
                    throw new Error('Неизвестное состояние акторизации: ' + status);
            }
        }
    },
});