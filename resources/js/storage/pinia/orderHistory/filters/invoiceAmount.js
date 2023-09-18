export const serializeInvoiceAmount = ({from, to}) => {
    from = from ? from : '';
    to = to ? to : '';

    return `${from};${to}`
}

export const unserializeInvoiceAmount = (value) => {
    const [from, to] = value.split(';')

    return {
        from: from ? parseInt(from) : null,
        to: to ? parseInt(to) : null,
    }
}

export const validateInvoiceAmount = (from, to) => {

    if (from === null && to === null) {
        return ['Вы должны ввести хотя бы один параметр']
    }

    if(from && to){
        if(from > to){
            return ['Максимум суммы заказа больше минимальной суммы']
        }
    }

    return []
}
