    import './bootstrap';
    import { createApp } from 'vue';
// routes files
    import router from "./routes"
// global-registered components
    import Index from './components/layouts/customer/Index';
// plugins
    import {sendErrorsToServer} from "./plugins/vue-logger-plugin/custom-hooks";
    import {createLogger} from "vue-logger-plugin";

const Logger = createLogger({
    enabled: true,
    level: 'debug',
    beforeHooks: [sendErrorsToServer],
});

const app = createApp(Index);

app.use(router);
app.use(Logger);

app.mount('#app')
