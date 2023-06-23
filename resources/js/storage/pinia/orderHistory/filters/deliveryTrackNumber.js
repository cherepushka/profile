export const serializeDeliveryTrackNumber = (value) => {
    return `deliveryTrackNumber:${value}`;
};

export const unserializeDeliveryTrackNumber = (value) => {
    return value;
};

export const validateDeliveryTrackNumber = (value) => {
    if (!value){
        return ['Трек-номер не должен быть пустым'];
    }

    return [];
};
