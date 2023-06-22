export const serializeCommercialOfferNumber = (value) => {
    return `commercialOfferNumber:${value}`
}

export const unserializeCommercialOfferNumber = (value) => {
    return value
}

export const validateCommercialOfferNumber = (value) => {
    if (!value){
        return ['Номер не должен быть пустым']
    }

    return []
}
