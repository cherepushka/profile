    import axios from "axios";
// endpoint groups
    import Auth from "./endpoints/Auth";
    import User from "./endpoints/User";
    import Order from "./endpoints/Order";
    import Manager from "./endpoints/Manager";
    import Download from "./endpoints/Download";
// middlewares
    import UnauthRedirectToLoginPage from "./middlewares/responses/UnauthRedirectToLoginPage";

export default class BackendApi{

    // Самостоятельная имплементация middlewere
    // Документация: https://axios-http.com/docs/interceptors
    globalMiddlewares = {
        onRequest: [],
        onResponse: [
            new UnauthRedirectToLoginPage,
        ]
    }

    constructor(baseUrl) {
       this.setHttpClient(baseUrl);
       this.setGlobalMiddlewares(this.httpClient);
       this.setAuthUserToken();
    }

    setHttpClient(baseUrl){
        this.httpClient = axios.create({
            baseURL: baseUrl,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
        });
    }

    setGlobalMiddlewares(httpClient){

        this.globalMiddlewares.onRequest.forEach(middleware => {
            httpClient.interceptors.request.use(middleware.onFulfilled, middleware.onRejected)
        })

        this.globalMiddlewares.onResponse.forEach(middleware => {
            httpClient.interceptors.request.use(middleware.onFulfilled, middleware.onRejected)
        })
    }

    setAuthUserToken(token){
        this.httpClient.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }

    unsetAuthUserToken(){
        this.httpClient.defaults.headers.common['Authorization'] = undefined;
    }

    download(){
        return new Download(this.httpClient)
    }

    auth(){
        return new Auth(this.httpClient)
    }

    user(){
        return new User(this.httpClient)
    }

    order(){
        return new Order(this.httpClient);
    }

    manager(){
        return new Manager(this.httpClient)
    }

}
