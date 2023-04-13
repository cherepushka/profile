import { useUserStorage } from "../../../../storage/pinia/userStorage";

export default class UnauthRedirectToLoginPage{

    onFulfilled(response){
        return response
    }

    onRejected(error){
        if(error.isAxiosError && error?.response?.status === 401){
            useUserStorage().setIsAthorized(false)
        }
        return Promise.reject(error);
    }
}