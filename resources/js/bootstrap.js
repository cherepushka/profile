    import _ from 'lodash';

window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
    import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


    import {createApp} from 'vue';
// routes files
    import {createRouter} from 'vue-router';
    import {beforeEach, routerConf} from "./router"
// global-registered
    import Index from './components/layouts/customer/Index';
    import BackendApi from './composables/backendApi/BackendApi';
// plugins
    import {sendErrorsToServer} from "./config/plugins/vue-logger-plugin/custom-hooks";
    import {createLogger} from "vue-logger-plugin";
    import {createPinia} from 'pinia';

export const Logger = createLogger({
    enabled: true,
    level: 'debug',
    beforeHooks: [sendErrorsToServer],
});

const pinia = createPinia();

export const router = createRouter(routerConf);
router.beforeEach((from, to, next) => {
    beforeEach(from, to, next)
})

const app = createApp(Index);

// Setup backend api service
export const backendApi = new BackendApi(
    process.env.MIX_API_URL,
)
app.config.globalProperties.$backendApi = backendApi;

app.use(router);
app.use(Logger);
app.use(pinia);

app.mount('#app');

export default app