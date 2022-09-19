// Vue main router
    import { createRouter, createWebHistory } from 'vue-router';
// components
    import Login from './components/pages/Login';
    import OrdersHistory from "./components/pages/OrdersHistory";
    import PersonalInfo from "./components/pages/PersonalInfo";

const routes =[
    {
        path: '/login',
        component: Login,
        name: 'login_page'
    },
    {
        path: '/order-history',
        component: OrdersHistory,
        name: 'order_history',
        meta: {
            title: 'История заказов',
            userTabMenuItem: true
        }
    },
    {
        path: '/personal-info',
        component: PersonalInfo,
        name: 'personal_info',
        meta: {
            title: 'Персональная информация',
            userTabMenuItem: true
        }
    },
    {
        path: '/logout',
        component: OrdersHistory,
        name: 'logout',
        meta: {
            title: 'Выйти',
            userTabMenuItem: true
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        return { top: 0 }
    }
});

export default router;
