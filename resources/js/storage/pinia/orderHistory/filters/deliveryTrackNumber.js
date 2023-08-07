export const serializeDeliveryTrackNumber = (value) => {
    return value;
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
