import { defineStore } from "pinia";
import {router} from "../../bootstrap";
import { backendApi } from "../../bootstrap";
import { Logger } from "../../bootstrap";

export const useUserStorage = defineStore('user', {
    state: () => ({
        authorized: undefined,
        userId: null,
        email: getUserEmailFromLocalStorage(),
        phone: getUserPhoneFromLocalStorage(),
        registrationDate: null,
    }),
    getters: {
        async isAuthorized (state) {

            if (state.authorized !== undefined && state.email === null && state.phone === null){
                return state.authorized;
            }
            
            await useUserStorage().fetchUserRemoteInfo();

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
        setUserInfo({email, phone, userId, registrationDate}){

            this.email = this.email || email;
            this.phone = this.phone || phone;
            this.userId = this.userId || userId;
            this.registrationDate = this.registrationDate || registrationDate;

            localStorage.setItem('user_email', email)
            localStorage.setItem('user_phone', phone)
        },
        setIsAthorized(status) {

            switch (status) {
                case true:

                    this.authorized = true

                    break;
                case false:

                    this.$reset();
                    
                    localStorage.removeItem('user_email');
                    localStorage.removeItem('user_phone');

                    this.authorized = false;

                    router.push({name: 'login_page'});

                    break;
                default:
                    throw new Error('Неизвестное состояние акторизации: ' + status)
            }
        }
    },
});

const getUserEmailFromLocalStorage = () => {
    return localStorage.getItem('user_email');
};

const getUserPhoneFromLocalStorage = () => {
    return localStorage.getItem('user_phone');
};