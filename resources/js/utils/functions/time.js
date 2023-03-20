export const timestampTo_ISO_8601_Date = (timestamp) => {

    const dateFromTimestamp = new Date(parseInt(timestamp))

    const offset = dateFromTimestamp.getTimezoneOffset()
    let newDate = new Date(dateFromTimestamp.getTime() - (offset*60*1000))

    try{
        return newDate.toISOString().split('T')[0]
    } catch(Error ) {
        return null
    }
}