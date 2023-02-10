import {router} from "../bootstrap";
import { useUserStorage } from "../storage/pinia/userStorage";

export const needAuth = async ({from, to, next}) => {

    if (await useUserStorage().isAuthorized === false) {
        return router.push({name: 'login_page'})
    }

    return next();
};