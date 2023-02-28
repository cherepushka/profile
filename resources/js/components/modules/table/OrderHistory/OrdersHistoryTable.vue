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
                    <input class="input order-date__input" type="date" v-model="filter.Date_sort.from">
                    По
                    <input class="input order-date__input" type="date" v-model="filter.Date_sort.to">
                    <button class="button order-date__submit" type="submit" @click="filterByDate">Отфильтровать</button>
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
                        <th class="head-column" @click="() => toggleSort('Date')">

                            <div class="head-column__sortable">
                                <Triangle 
                                    class="sort-direction"
                                    :style="{display: orderableColumns.Date.currentOrder !== undefined ? 'block' : 'none'}"
                                    :direction="orderableColumns.Date.currentOrder == 'ASC' ? 'up' : 'down'"
                                >
                                </Triangle>
                                <Triangle class="sort-direction" direction="down" 
                                    :style="{opacity: 0.2, display: orderableColumns.Date.currentOrder === undefined ? 'block' : 'none'}"
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
                        <th class="head-column" @click="() => toggleSort('lastShipmentDate')">

                            <div class="head-column__sortable">
                                <Triangle 
                                    class="sort-direction"
                                    :style="{display: orderableColumns.lastShipmentDate.currentOrder !== undefined ? 'block' : 'none'}"
                                    :direction="orderableColumns.lastShipmentDate.currentOrder == 'ASC' ? 'up' : 'down'"
                                >
                                </Triangle>
                                <Triangle class="sort-direction" direction="down" 
                                    :style="{opacity: 0.2, display: orderableColumns.lastShipmentDate.currentOrder === undefined ? 'block' : 'none'}"
                                >
                                </Triangle>
                                Дата последней отгрузки
                            </div>

                        </th>
                        <th class="head-column" @click="() => toggleSort('lastPaymentDate')">

                            <div class="head-column__sortable">
                                <Triangle 
                                    class="sort-direction"
                                    :style="{display: orderableColumns.lastPaymentDate.currentOrder !== undefined ? 'block' : 'none'}"
                                    :direction="orderableColumns.lastPaymentDate.currentOrder == 'ASC' ? 'up' : 'down'"
                                >
                                </Triangle>
                                <Triangle class="sort-direction" direction="down" 
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
                    <template v-for="tableRow in tableRows">
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
// validation package
import { requiredIf, helpers, maxValue } from '@vuelidate/validators'
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
                Date_sort: {
                    from: {
                        requiredIf: helpers.withMessage(
                            'Вы должны выбрать хотя бы одну дату', 
                            requiredIf(this.filter.Date_sort.to == undefined)
                        ),
                        lessToDate: helpers.withMessage(
                            'Дата начала фильтра не должна быть больше даты конца',
                            (value) => {
                                if(!this.filter.Date_sort.to){
                                    return true;
                                }

                                if(this.filter.Date_sort.from && this.filter.Date_sort.to < value){
                                    return false;
                                }

                                return true;
                            }
                        ),
                    },
                    to: {
                        requiredIf: helpers.withMessage(
                            'Вы должны выбрать хотя бы одну дату', 
                            requiredIf(this.filter.Date_sort.from == undefined)
                        ),
                        aboveFromDate: helpers.withMessage(
                            'Дата конца фильтра не должна быть меньше даты начала',
                            (value) => {
                                if(!this.filter.Date_sort.from){
                                    return true;
                                }

                                if(this.filter.Date_sort.to && this.filter.Date_sort.from > value){
                                    return false;
                                }

                                return true;
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
                Date_sort: {
                    from: undefined,
                    to: undefined,
                }
            },
            orderableColumns: {
                Date: {
                    currentOrder: undefined,
                    currentOrderIndex: undefined,
                    orders: ['ASC', 'DESC']
                },
                lastShipmentDate: {
                    currentOrder: undefined,
                    currentOrderIndex: undefined,
                    orders: ['ASC', 'DESC']
                },
                lastPaymentDate: {
                    currentOrder: undefined,
                    currentOrderIndex: undefined,
                    orders: ['ASC', 'DESC']
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
        for(const property in this.$route.query){
            if(this.orderableColumns[property] !== undefined) {
                this.orderableColumns[property].currentOrder = this.$route.query[property];

                this.orderableColumns[property].orders.forEach((item, i) => {
                    if(item === this.orderableColumns[property].currentOrder){
                        this.orderableColumns[property].currentOrderIndex = i;
                    }
                });
                break;
            }
        }

        // Ставим состояние фильтров
        for(const property in this.$route.query){
            if(this.filter[property] !== undefined){
                const dates = this.$route.query[property].split(';');

                this.filter[property].from = dates[0];
                this.filter[property].to = dates[1];
                break;
            }
        }

        this.fetchRows();
    },
    watch: {
        '$route': function(to, from) {
            if(to.query != from.query && this !== undefined){
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
                const res = (await this.$backendApi.order().list(this.$route.query)).data
                this.tableRows = res.items;
                this.tableRowsCount = res.count;
            } catch(e){
                this.$logger.error(e);
            } finally {
                this.loading = false;
            }
        },
        toggleSort(sortColumnName){
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
            for(const queryProp in this.$route.query){
                if (this.orderableColumns[queryProp] != undefined && queryProp != sortColumnName) {
                    delete this.$route.query[queryProp];

                    this.orderableColumns[queryProp].currentOrderIndex = undefined;
                    this.orderableColumns[queryProp].currentOrder = undefined;
                }
            }
            const query = {...this.$route.query, page: 1};

            if (currCol.currentOrder !== undefined) {
                query[sortColumnName] = currCol.currentOrder;
            } else {
                delete query[sortColumnName]; 
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

            const to = this.filter.Date_sort.to ? this.filter.Date_sort.to : '';
            const from = this.filter.Date_sort.from ? this.filter.Date_sort.from : '';

            this.$router.push({
                name: this.$route.name,
                params: {...this.$route.params},
                query: {...this.$route.query, Date_sort: from + ';' + to},
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

.sort-direction{
    margin-right: 5px;
}

</style>
