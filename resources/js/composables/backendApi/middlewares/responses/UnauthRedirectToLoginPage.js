export default class UnauthRedirectToLoginPage{

    onFulfilled(response){
        return response
    }

    onRejected(error){
        return Promise.reject(error);
    }
}