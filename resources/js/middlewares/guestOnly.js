import {router} from "../bootstrap";
import { useUserStorage } from "../storage/pinia/userStorage";

export const guestOnly = async ({from, to, next}) => {
    
    if (await useUserStorage().isAuthorized === true) {
        return router.push({name: 'order_history'})
    }

    return next();
};