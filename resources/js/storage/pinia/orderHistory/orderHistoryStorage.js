import { defineStore } from "pinia";
import { serializeInvoiceDate, unserializeInvoiceDate, validateInvoiceDate } from "./filters/invoiceDate";
import { serializeCommercialOfferNumber, unserializeCommercialOfferNumber, validateCommercialOfferNumber } from "./filters/commercialOfferNumber";
import { serializeWaybillNumber, unserializeWaybillNumber, validateWaybillNumber } from "./filters/waybillNumber";
import { serializeDeliveryTrackNumber, unserializeDeliveryTrackNumber, validateDeliveryTrackNumber } from "./filters/deliveryTrackNumber";
import {
    getAvailableOptions,
    serializeDeliveryStatus,
    unserializeDeliveryStatus,
    validateDeliveryStatus
} from "./filters/deliveryStatus";
import { serializeDeliveryDate, unserializeDeliveryDate, validateDeliveryDate } from "./filters/deliveryDate";
import { serializeInvoiceAmount, unserializeInvoiceAmount, validateInvoiceAmount } from "./filters/invoiceAmount";
import { backendApi } from "../../../bootstrap";

export const useOrderHistoryStorage = defineStore('orderHistory', {
    state: () => ({
        isLoading: false,
        rowsPerPage: 20,
        filter: {
            activeFilters: [],
            filters: {
                invoiceDate: {
                    value: {
                        dateFromTimestamp: null,
                        dateToTimestamp: null,
                    },
                    serializeFunc: serializeInvoiceDate,
                    unserializeFunc: unserializeInvoiceDate,
                    validateFunc: validateInvoiceDate,
                },
                commercialOfferNumber: {
                    value: '',
                    serializeFunc: serializeCommercialOfferNumber,
                    unserializeFunc: unserializeCommercialOfferNumber,
                    validateFunc: validateCommercialOfferNumber,
                },
                waybillNumber: {
                    value: '',
                    serializeFunc: serializeWaybillNumber,
                    unserializeFunc: unserializeWaybillNumber,
                    validateFunc: validateWaybillNumber,
                },
                deliveryTrackNumber: {
                    value: '',
                    serializeFunc: serializeDeliveryTrackNumber,
                    unserializeFunc: unserializeDeliveryTrackNumber,
                    validateFunc: validateDeliveryTrackNumber,
                },
                deliveryStatus: {
                    value: '',
                    getAvailableOptions: getAvailableOptions,
                    serializeFunc: serializeDeliveryStatus,
                    unserializeFunc: unserializeDeliveryStatus,
                    validateFunc: validateDeliveryStatus,
                },
                deliveryDate: {
                    value: {
                        dateFromTimestamp: null,
                        dateToTimestamp: null,
                    },
                    serializeFunc: serializeDeliveryDate,
                    unserializeFunc: unserializeDeliveryDate,
                    validateFunc: validateDeliveryDate,
                },
                invoiceAmount: {
                    value: {
                        from: 0,
                        to: 0,
                    },
                    serializeFunc: serializeInvoiceAmount,
                    unserializeFunc: unserializeInvoiceAmount,
                    validateFunc: validateInvoiceAmount,
                },
            }
        },
        sort: {
            active: null,
            sorts: {
                invoiceDate: {
                    currentOrder: null,
                    currentOrderIndex: null,
                    orders: ['asc', 'desc']
                },
                lastShipmentDate: {
                    currentOrder: null,
                    currentOrderIndex: null,
                    orders: ['asc', 'desc']
                },
                lastPaymentDate: {
                    currentOrder: null,
                    currentOrderIndex: null,
                    orders: ['asc', 'desc']
                },
                deliveryDates: {
                    currentOrder: null,
                    currentOrderIndex: null,
                    orders: ['asc', 'desc']
                }
            },
        },
        orderInfoColumns: {
            selected: [
                {
                    id: 'invoiceDate',
                    title: 'Дата заказа',
                },
                {
                    id: 'items',
                    title: 'Позиции',
                },
                {
                    id: 'fullPrice',
                    title: 'Стоимость с НДС',
                },
                {
                    id: 'managerName',
                    title: 'Менеджер',
                },
                {
                    id: 'mail_trigger',
                    title: 'Триггер письма',
                },
                {
                    id: 'payLink',
                    title: 'Ссылка оплаты',
                },
                {
                    id: 'paymentStatus',
                    title: 'Статус оплаты',
                },
                {
                    id: 'shipmentStatus',
                    title: 'Статус отгрузки',
                },
                {
                    id: 'lastPaymentDate',
                    title: 'Дата последней оплаты',
                },
                {
                    id: 'lastShipmentDate',
                    title: 'Дата последней отгрузки',
                },
                {
                    id: 'customFieldValue',
                    title: 'Произвольное поле',
                },
            ],
            allAvailable: [
                {
                    id: 'invoiceDate',
                    title: 'Дата заказа',
                },
                {
                    id: 'items',
                    title: 'Позиции',
                },
                {
                    id: 'fullPrice',
                    title: 'Стоимость с НДС',
                },
                {
                    id: 'managerName',
                    title: 'Менеджер',
                },
                {
                    id: 'mail_trigger',
                    title: 'Триггер письма',
                },
                {
                    id: 'payLink',
                    title: 'Ссылка оплаты',
                },
                {
                    id: 'paymentStatus',
                    title: 'Статус оплаты',
                },
                {
                    id: 'shipmentStatus',
                    title: 'Статус отгрузки',
                },
                {
                    id: 'lastPaymentDate',
                    title: 'Дата последней оплаты',
                },
                {
                    id: 'lastShipmentDate',
                    title: 'Дата последней отгрузки',
                },
                {
                    id: 'customFieldValue',
                    title: 'Произвольное поле',
                },
                {
                    id: 'commercialOfferNumber',
                    title: 'КП (номер коммерческого предложения)',
                },
                {
                    id: "deliveryStatuses",
                    title: 'Статусы отгрузок',
                },
                {
                    id: "deliveryDates",
                    title: 'Даты доставок',
                }
            ],
        },
        orders: [],
        currentPage: 1,
        maxPage: 1,
    }),
    actions: {
        async setStateFromSerialized({filters, sort, page}){
            this.currentPage = page !== null ? parseInt(page) : 1;

            this.sort.active = sort
            if (sort !== null) {
                const [sortColumn, sortValue] = sort.split('_');

                this.sort.sorts[sortColumn].currentOrder = sortValue
                this.sort.sorts[sortColumn].orders.forEach((item, i) => {
                    if(item === this.sort.sorts[sortColumn].currentOrder){
                        this.sort.sorts[sortColumn].currentOrderIndex = i;
                    }
                });
            }

            this.filter.activeFilters = []
            filters.forEach(filter => {
                const [filterName, filterValues] = filter.split(':');

                this.filter.activeFilters.push(filterName)

                this.filter.filters[filterName].value = this.filter.filters[filterName].unserializeFunc(filterValues)
            })

            await fetchOrders()
        },

        async flushActiveFilters(){
            this.filter.activeFilters = [];
            await fetchOrders()
        },

        // Фильтры
        async pickInvoiceDateFilter(dateFromTimestamp, dateToTimestamp){
            const validationErrs = this.filter.filters.invoiceDate.validateFunc(dateFromTimestamp, dateToTimestamp);
            if (validationErrs.length !== 0) {
                return validationErrs;
            }

            this.filter.filters.invoiceDate.value = {
                dateFromTimestamp,
                dateToTimestamp
            };
            await setFilterActive('invoiceDate');

            return [];
        },
        async pickCommercialOfferNumberFilter(value){
            const validationErrs = this.filter.filters.commercialOfferNumber.validateFunc(value)
            if (validationErrs.length !== 0) {
                return validationErrs
            }

            this.filter.filters.commercialOfferNumber.value = value
            await setFilterActive('commercialOfferNumber');

            return []
        },
        async pickWaybillNumberFilter(value){
            const validationErrs = this.filter.filters.waybillNumber.validateFunc(value);
            if (validationErrs.length !== 0) {
                return validationErrs;
            }

            this.filter.filters.waybillNumber.value = value;
            await setFilterActive('waybillNumber');

            return [];
        },
        async pickInvoiceAmountFilter(from, to){
            const validationErrs = this.filter.filters.invoiceAmount.validateFunc(from, to);
            if (validationErrs.length !== 0) {
                return validationErrs;
            }

            this.filter.filters.invoiceAmount.value = {
                from,
                to,
            }
            await setFilterActive('invoiceAmount');

            return [];
        },
        async pickDeliveryTrackNumberFilter(value){
            const validationErrs = this.filter.filters.deliveryTrackNumber.validateFunc(value);
            if (validationErrs.length !== 0) {
                return validationErrs;
            }

            this.filter.filters.deliveryTrackNumber.value = value;
            await setFilterActive('deliveryTrackNumber');

            return [];
        },
        async pickDeliveryStatusFilter(value){
            const validationErrs = await this.filter.filters.deliveryStatus.validateFunc(value)
            if (validationErrs.length !== 0){
                return validationErrs
            }

            this.filter.filters.deliveryStatus.value = value
            await setFilterActive('deliveryStatus')

            return []
        },
        async pickDeliveryDateFilter(dateFromTimestamp, dateToTimestamp){
            const validationErrs = this.filter.filters.deliveryDate.validateFunc(dateFromTimestamp, dateToTimestamp);
            if (validationErrs.length !== 0) {
                return validationErrs;
            }

            this.filter.filters.deliveryDate.value = {
                dateFromTimestamp,
                dateToTimestamp
            };
            await setFilterActive('deliveryDate');

            return [];
        },
        // /Фильтры

        // сортировка
        async toggleSort(sortColumnName){

            const currCol = this.sort.sorts[sortColumnName];

            if (currCol.currentOrderIndex === null){

                currCol.currentOrderIndex = 0;
                currCol.currentOrder = currCol.orders[0]
            } else if (currCol.currentOrderIndex + 1 < currCol.orders.length) {

                currCol.currentOrderIndex++;
                currCol.currentOrder = currCol.orders[currCol.currentOrderIndex]
            } else {

                await this.flushSortForColumn(sortColumnName, false);
            }

            if (currCol.currentOrder !== null){
                this.sort.active = sortColumnName + '_' + currCol.currentOrder
            } else {
                this.sort.active = null
            }

            // Инвалидируем сортировки по другим колонкам
            for(const sortableColumn in this.sort.sorts){
                if (sortableColumn !== sortColumnName) {
                    this.sort.sorts[sortableColumn].currentOrderIndex = null;
                    this.sort.sorts[sortableColumn].currentOrder = null;
                }
            }

            this.currentPage = 1;
            await fetchOrders();
        },
        async flushSortForColumn(sortColumnName, doFetchOrders = true){
            const currCol = this.sort.sorts[sortColumnName];

            currCol.currentOrderIndex = null;
            currCol.currentOrder = null;

            if(this.sort.active === null) {
                return;
            }

            if(this.sort.active.split('_')[0] === sortColumnName){
                this.sort.active = null;

                if(doFetchOrders){
                    await fetchOrders();
                }
            }
        },
        // /сортировка

        async setCurrentPage(page){
            this.currentPage = page

            await fetchOrders()
        },
        async applyColumnSelection(){
            if(this.sort.active === null){
                return;
            }

            const sortColumnName = this.sort.active.split('_')[0];
            await this.flushSortForColumn(sortColumnName);
        },
    }
});

// private methods
const fetchOrders = async () => {
    const storage = useOrderHistoryStorage();

    storage.isLoading = true

    let filters = [];
    storage.filter.activeFilters.forEach(filterName => {
        const f = storage.filter.filters[filterName]
        filters.push(filterName + ":" + f.serializeFunc(f.value))
    })

    let errors = []
    try{
        const res = await backendApi.order().list({
            page: storage.currentPage,
            sort: storage.sort.active === null ? '' : storage.sort.active,
            filters: filters
        })
        storage.orders = res.data.items
        storage.maxPage = res.data.count / storage.rowsPerPage + (res.data.count % storage.rowsPerPage > 0 ? 1 : 0)
    } catch (e) {
        if(e.response.status === 422) {
            for (const [key, value] of Object.entries(e.response.data.errors)) {
                errors.push(...value)
            }
        } else {
            errors.push(e.response.data.message)
        }
    }

    storage.isLoading = false

    return errors
}

const setFilterActive = async (filterName) => {
    const storage = useOrderHistoryStorage();

    storage.currentPage = 1;

    storage.filter.activeFilters = [];
    storage.filter.activeFilters.push(filterName);

    await fetchOrders();
}
