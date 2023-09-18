export const serializeDeliveryDate = ({dateFromTimestamp, dateToTimestamp}) => {
    const from = dateFromTimestamp ? dateFromTimestamp : '';
    const to = dateToTimestamp ? dateToTimestamp : '';

    return `${from};${to}`
}

export const unserializeDeliveryDate = (value) => {
    const [dateFromTimestamp, dateToTimestamp] = value.split(';')

    return {
        dateFromTimestamp: dateFromTimestamp ? parseInt(dateFromTimestamp) : null,
        dateToTimestamp: dateToTimestamp ? parseInt(dateToTimestamp) : null,
    }
}

export const validateDeliveryDate = (dateFromTimestamp, dateToTimestamp) => {

    if (dateFromTimestamp === null && dateToTimestamp === null) {
        return ['Вы должны выбрать хотя бы одну дату']
    }

    if(dateToTimestamp && dateToTimestamp){
        if(dateToTimestamp < dateFromTimestamp){
            return ['Дата начала фильтра не должна быть больше даты конца']
        }
    }

    return []
}
