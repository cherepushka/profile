<template>

    <div>

        <div class="filters">
            <div class="order-date">
                <h3 class="order-date__title">Фильтр по дате заказа</h3>

                <ul class="filters__errors"
                    v-if="filter.errors.length !== 0"
                    v-for="error in filter.errors"
                >
                    <li>{{ error }}</li>
                </ul>

                <div class="order-date__manipulation">
                    С
                    <input class="input order-date__input" type="date" v-model="filter.invoice_date.from">
                    По
                    <input class="input order-date__input" type="date" v-model="filter.invoice_date.to">
                    <button class="button order-date__submit" type="submit" @click="filterByDate">
                        Отфильтровать
                    </button>
                </div>
            </div>
        </div>

        <div class="table-wrapper">

            <div class="loading-overlay" ref="loadingOverlay"
                :style="{visibility: loading ? 'visible' : 'hidden'}"
            >
            </div>

            <table class="table" ref="contentTable">
                <thead class="table__head">
                    <tr>
                        <th class="head-column"></th>
                        <th class="head-column" @click="() => toggleOrder('invoiceDate')">

                            <div class="head-column__sortable">
                                <Triangle
                                    class="order-direction"
                                    :style="{display: orderableColumns.invoiceDate.currentOrder !== undefined ? 'block' : 'none'}"
                                    :direction="orderableColumns.invoiceDate.currentOrder == 'invoiceDate_asc' ? 'up' : 'down'"
                                >
                                </Triangle>
                                <Triangle class="order-direction" direction="down"
                                    :style="{opacity: 0.2, display: orderableColumns.invoiceDate.currentOrder === undefined ? 'block' : 'none'}"
                                >
                                </Triangle>
                                Дата заказа
                            </div>

                        </th>
                        <th class="head-column">Позиции</th>
                        <th class="head-column">Стоимость с НДС</th>
                        <th class="head-column">Менеджер</th>
                        <th class="head-column">Триггер письма</th>
                        <th class="head-column">Ссылка оплаты</th>
                        <th class="head-column">Статус оплаты</th>
                        <th class="head-column">Статус отгрузки</th>
                        <th class="head-column" @click="() => toggleOrder('lastShipmentDate')">

                            <div class="head-column__sortable">
                                <Triangle
                                    class="order-direction"
                                    :style="{display: orderableColumns.lastShipmentDate.currentOrder !== undefined ? 'block' : 'none'}"
                                    :direction="orderableColumns.lastShipmentDate.currentOrder == 'lastShipmentDate_asc' ? 'up' : 'down'"
                                >
                                </Triangle>
                                <Triangle class="order-direction" direction="down"
                                    :style="{opacity: 0.2, display: orderableColumns.lastShipmentDate.currentOrder === undefined ? 'block' : 'none'}"
                                >
                                </Triangle>
                                Дата последней отгрузки
                            </div>

                        </th>
                        <th class="head-column" @click="() => toggleOrder('lastPaymentDate')">

                            <div class="head-column__sortable">
                                <Triangle
                                    class="order-direction"
                                    :style="{display: orderableColumns.lastPaymentDate.currentOrder !== undefined ? 'block' : 'none'}"
                                    :direction="orderableColumns.lastPaymentDate.currentOrder == 'lastPaymentDate_asc' ? 'up' : 'down'"
                                >
                                </Triangle>
                                <Triangle class="order-direction" direction="down"
                                    :style="{opacity: 0.2, display: orderableColumns.lastPaymentDate.currentOrder === undefined ? 'block' : 'none'}"
                                >
                                </Triangle>
                                Дата последней оплаты
                            </div>

                        </th>
                        <th class="head-column">Произвольное поле</th>
                    </tr>
                </thead>
                <tbody class="table__body">
                    <template v-for="tableRow in tableRows" :key="tableRow.id">
                        <orders-history-table-row
                            :table-row="tableRow"
                            @managerClick="toggleManagerPopup"
                        >
                        </orders-history-table-row>
                    </template>
                </tbody>
            </table>
        </div>

        <order-history-pagination class="pagination-container"
            :current-page="parseInt(page)" :max-page="tableRowsCount / 20 + (tableRowsCount % 20 > 0 ? 1 : 0)">
        </order-history-pagination>

        <popup-wrapper
            v-if="isManagerPopupShown"
            @closePopup="isManagerPopupShown = false"
        >
            <contact-manager :managerInfo="currentManager"></contact-manager>
        </popup-wrapper>
    </div>

</template>

<script>
import OrdersHistoryTableRow from "./OrdersHistoryTableRow";
import PopupWrapper from "../../base/PopupWrapper";
import ContactManager from "../../popup/ContactManager";
import OrderHistoryPagination from "./OrderHistoryPagination.vue";
import Triangle from "../../../icons/order/Triangle.vue";
import {timestampTo_ISO_8601_Date} from "../../../../utils/functions/time";
// validation package
import { requiredIf, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'

export default {
    name: "OrdersHistoryTable",
    components: {
        OrdersHistoryTableRow,
        PopupWrapper,
        ContactManager,
        OrderHistoryPagination,
        Triangle
    },
    setup: () => ({ v$: useVuelidate() }),
    validations() {
        return {
            filter: {
                date: {
                    from: {
                        requiredIf: helpers.withMessage(
                            'Вы должны выбрать хотя бы одну дату',
                            requiredIf(
                                this.filter.invoice_date.from == undefined
                                && this.filter.invoice_date.to == undefined
                            )
                        ),
                        lessToDate: helpers.withMessage(
                            'Дата начала фильтра не должна быть больше даты конца',
                            (value) => {
                                if(!this.filter.invoice_date.to){
                                    return true;
                                }

                                if(this.filter.invoice_date.from && this.filter.invoice_date.to < value){
                                    return false;
                                }

                                return true;
                            }
                        ),
                    },
                    to: {
                        requiredIf: helpers.withMessage(
                            'Вы должны выбрать хотя бы одну дату',
                            requiredIf(
                                this.filter.invoice_date.from == undefined
                                && this.filter.invoice_date.to == undefined
                            )
                        ),
                        aboveFromDate: helpers.withMessage(
                            'Дата конца фильтра не должна быть меньше даты начала',
                            (value) => {
                                if(!this.filter.invoice_date.from){
                                    return true;
                                }

                                return !(this.filter.invoice_date.to && this.filter.invoice_date.from > value);
                            }
                        ),
                    },
                }
            }
        }
    },
    data() {
        return {
            loading: true,
            isManagerPopupShown: false,
            currentManager: {},
            managerInfo: {},
            tableRows: [],
            tableRowsCount: 0,
            filter: {
                errors: [],
                invoice_date: {
                    from: undefined,
                    to: undefined,
                }
            },
            orderableColumns: {
                invoiceDate: {
                    currentOrder: undefined,
                    currentOrderIndex: undefined,
                    orders: ['invoiceDate_asc', 'invoiceDate_desc']
                },
                lastShipmentDate: {
                    currentOrder: undefined,
                    currentOrderIndex: undefined,
                    orders: ['lastShipmentDate_asc', 'lastShipmentDate_desc']
                },
                lastPaymentDate: {
                    currentOrder: undefined,
                    currentOrderIndex: undefined,
                    orders: ['lastPaymentDate_asc', 'lastPaymentDate_desc']
                }
            }
        }
    },
    props: {
        page: {
            type: Number,
            required: true
        },
    },
    async mounted(){

        // Ставим положение треугольничков сортировки
        if(this.$route.query?.order) {
            const orderColumn = this.$route.query.order.split('_')[0];

            this.orderableColumns[orderColumn].currentOrder = this.$route.query.order

            this.orderableColumns[orderColumn].orders.forEach((item, i) => {
                if(item === this.orderableColumns[orderColumn].currentOrder){
                    this.orderableColumns[orderColumn].currentOrderIndex = i;
                }
            });
        }

        // Ставим состояние фильтров
        if(this.$route.query?.sort){
            const dates = this.$route.query.sort.split(':')[1].split(';');

            this.filter.invoice_date.from = timestampTo_ISO_8601_Date(dates[0]);
            this.filter.invoice_date.to = timestampTo_ISO_8601_Date(dates[1]);
        }

        this.fetchRows();
    },
    watch: {
        '$route': function(to, from) {
            if(to.name === from.name && to.query != from.query && this !== undefined){
                this.fetchRows();
            }
        },
        loading(){
            this.$refs.loadingOverlay.style.width = this.$refs.contentTable.scrollWidth + 'px';
        }
    },
    methods: {
        async toggleManagerPopup(managerId) {
            if (!managerId) {
                this.$log.error({
                    component: 'OrdersHistoryTable', function: 'toggleManagerPopup',
                    message: '`managerId` not provided to function',
                });
            }

            //if we don`t already load manager info, do API call
            if (!this.managerInfo.hasOwnProperty(managerId)) {
                let data = (await this.$backendApi.manager().infoById(managerId)).data
                data.id = managerId
                this.managerInfo[managerId] = data
            }

            this.currentManager = this.managerInfo[managerId];
            this.isManagerPopupShown = true;
        },
        async fetchRows(){
            this.loading = true;
            try{
                const res = (await this.$backendApi.order().list(this.$route.query)).data;
                this.tableRows = res.items;
                this.tableRowsCount = res.count;
            } catch(e){

                if(e.response.status == 422) {
                    for (const [key, value] of Object.entries(e.response.data.errors)) {
                        this.filter.errors.push(...value)
                    }
                } else {
                    this.filter.errors.push(e.response.data.message)
                }

                this.$logger.error(e);
            } finally {
                this.loading = false;
            }
        },
        toggleOrder(sortColumnName){
            const currCol = this.orderableColumns[sortColumnName];

            if (currCol.currentOrderIndex === undefined){
                currCol.currentOrderIndex = 0;
                currCol.currentOrder = currCol.orders[0]
            } else if (currCol.currentOrderIndex + 1 < currCol.orders.length) {
                currCol.currentOrderIndex++;
                currCol.currentOrder = currCol.orders[currCol.currentOrderIndex]
            } else {
                currCol.currentOrderIndex = undefined;
                currCol.currentOrder = undefined;
            }

            // Инвалидируем сортировки по другим колонкам
            for(const orderableColumn in this.orderableColumns){
                if (this.orderableColumns[orderableColumn] != undefined && orderableColumn !== sortColumnName) {

                    this.orderableColumns[orderableColumn].currentOrderIndex = undefined;
                    this.orderableColumns[orderableColumn].currentOrder = undefined;
                }
            }

            const query = {...this.$route.query, page: 1};

            if (currCol.currentOrder !== undefined) {
                query.order = currCol.currentOrder;
            } else {
                delete query.order;
            }

            this.$router.push({
                name: this.$route.name,
                params: this.$route.params,
                query: query,
            })
        },
        filterByDate(){
            this.filter.errors = [];
            this.v$.$validate();

            if(this.filter.invoice_date.from && this.filter.invoice_date.to){
                if(this.v$.$error){
                    let errors = {};

                    this.v$.$errors.forEach(err => {
                        errors[err.$message] = err.$message;
                    });

                    for(const err in errors){
                        this.filter.errors.push(err)
                    }
                    return;
                }
            }

            const to = this.filter.invoice_date.to
                ? new Date(this.filter.invoice_date.to).getTime()
                : '';
            const from = this.filter.invoice_date.from
                ? new Date(this.filter.invoice_date.from).getTime()
                : '';

            let query = {...this.$route.query, page: 1}

            if(this.filter.invoice_date.from || this.filter.invoice_date.to){
                query.sort = 'invoice_date:' + from + ';' + to
            } else {
                delete query.sort
            }

            this.$router.push({
                name: this.$route.name,
                params: this.$route.params,
                query: query,
            })
        }
    },
}
</script>

<style lang="scss" scoped>
@import "@scss/abstract/variables";

.pagination-container{
    margin: 20px auto;
    width: fit-content;
}

.filters{
    margin-bottom: 30px;

    &__errors{
        color: $default-error-color;
    }
}

.order-date{

    &__input{
        margin-right: 15px;
    }
}

.table-wrapper{
    overflow-x: scroll;
    width: 100%;
    position: relative;
}

.loading-overlay{
    position: absolute;
    height: 100%;
    z-index: 5;
    background-color: rgba(250, 250, 250, 0.7);
}

.table{
    width: 100%;
    border-collapse: collapse;
    background-color: transparent;
    border-spacing: 3px;

    &__head{
        position: sticky;
        background-color: #fff;
        z-index: 0;
        border-top: 1px solid #eceeef;
        border-bottom: 2px solid #eceeef;
    }
}

.head-column{
    width: 0;
    vertical-align: center;
    padding: 0.75rem;
    font-size: 13px;
    font-weight: 700;
    color: rgb(55, 58, 60);

    &__sortable{
        user-select: none;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
}

.order-direction{
    margin-right: 5px;
}

</style>
