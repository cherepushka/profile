export const serializeWaybillNumber = (value) => {
    return `waybillNumber:${value}`;
};

export const unserializeWaybillNumber = (value) => {
    return value;
};

export const validateWaybillNumber = (value) => {
    if (!value){
        return ['Номер не должен быть пустым'];
    }

    return [];
};
