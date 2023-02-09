// Vue main router
    import {createWebHistory} from 'vue-router';
// components
    import Login from './components/pages/Login';
    import Logout from './components/pages/Logout';
    import OrdersHistory from "./components/pages/OrdersHistory";
    import PersonalInfo from "./components/pages/PersonalInfo";
// middlewares
    import {needAuth} from './middlewares/needAuth';
    import {guestOnly} from './middlewares/guestOnly';

const routes = [
    {
        path: '/',
        redirect: () => {
            return {name: 'order_history'}
        }
    },
    {
        path: '/login',
        component: Login,
        name: 'login_page',
        meta: {
            middleware: [guestOnly],
        }
    },
    {
        path: '/order-history',
        component: OrdersHistory,
        name: 'order_history',
        meta: {
            title: 'История заказов',
            userTabMenuItem: true,
            middleware: [needAuth],
        }
    },
    {
        path: '/personal-info',
        component: PersonalInfo,
        name: 'personal_info',
        meta: {
            title: 'Персональная информация',
            userTabMenuItem: true,
            middleware: [needAuth],
        }
    },
    {
        path: '/logout',
        component: Logout,
        name: 'logout',
        meta: {
            title: 'Выйти',
            userTabMenuItem: true,
            middleware: [needAuth],
        }
    }
];

export const routerConf = {
    history: createWebHistory(),
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        return { top: 0 }
    },
}

// Creates a `nextMiddleware()` function which not only
// runs the default `next()` callback but also triggers
// the subsequent Middleware function.
const nextFactory = (context, middlewares, index) => {
    const subsequentMiddleware = middlewares[index];

    // If no subsequent Middleware exists,
    // the default `next()` callback is returned.
    if (!subsequentMiddleware) {
        return context.next;
    }

    return (...parameters) => {
        // Run the default Vue Router `next()` callback first.
        context.next(...parameters);

        // Then run the subsequent Middleware with a new
        // `nextMiddleware()` callback.
        const nextMiddleware = nextFactory(context, middlewares, index + 1);
        subsequentMiddleware({ ...context, next: nextMiddleware });
    };
}


export const beforeEach = ((to, from, next) => {

    if (to?.meta?.middleware) {
        const middlewares = Array.isArray(to.meta.middleware)
            ? to.meta.middleware
            : [to.meta.middleware];

        const context = {
            from,
            next,
            to,
        };
        const nextMiddleware = nextFactory(context, middlewares, 1);

        return middlewares[0]({ ...context, next: nextMiddleware });
    }

    next()
});