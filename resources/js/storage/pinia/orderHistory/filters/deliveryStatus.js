import {backendApi} from "../../../../bootstrap";

let availableOptions = {};

export const getAvailableOptions = async () => {
    if (availableOptions.length > 0){
        return availableOptions
    }

    availableOptions = (await backendApi.order().deliveryStatuses()).data

    return availableOptions
}

export const serializeDeliveryStatus = (value) => {
    return value;
};

export const unserializeDeliveryStatus = (value) => {
    return value;
};

export const validateDeliveryStatus = async (value) => {

    if (!value){
        return ['Не должен быть пустым'];
    }

    const statuses = await getAvailableOptions()
    if (typeof statuses[value] === "undefined"){
        return ['Неизвестное значение для фильтра']
    }

    return [];
};
