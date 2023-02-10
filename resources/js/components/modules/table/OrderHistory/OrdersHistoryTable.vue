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
                        <th class="head-column">№</th>
                        <th class="head-column" @click="() => toggleSort('Date')">

                            <div class="head-column__sortable">
                                <Triangle 
                                    class="sort-direction"
                                    :style="{visibility: orderableColumns.Date.currentOrder !== undefined ? 'visible' : 'hidden'}"
                                    :direction="orderableColumns.Date.currentOrder == 'ASC' ? 'up' : 'down'"
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
                                    :style="{visibility: orderableColumns.lastShipmentDate.currentOrder !== undefined ? 'visible' : 'hidden'}"
                                    :direction="orderableColumns.lastShipmentDate.currentOrder == 'ASC' ? 'up' : 'down'"
                                >
                                </Triangle>
                                Дата последней отгрузки
                            </div>

                        </th>
                        <th class="head-column" @click="() => toggleSort('lastPaymentDate')">

                            <div class="head-column__sortable">
                                <Triangle 
                                    class="sort-direction"
                                    :style="{visibility: orderableColumns.lastPaymentDate.currentOrder !== undefined ? 'visible' : 'hidden'}"
                                    :direction="orderableColumns.lastPaymentDate.currentOrder == 'ASC' ? 'up' : 'down'"
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
            :current-page="parseInt(page)" :max-page="10">
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

export default {
    name: "OrdersHistoryTable",
    components: {
        OrdersHistoryTableRow,
        PopupWrapper,
        ContactManager,
        OrderHistoryPagination,
        Triangle
    },
    data() {
        return {
            loading: true,
            isManagerPopupShown: false,
            currentManager: {},
            managerInfo: {},
            tableRows: [],
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
        for(const property in this.$route.query){
            // Ставим положение треугольничков сортировки
            if(this.orderableColumns[property] !== undefined) {
                this.orderableColumns[property].currentOrder = this.$route.query[property];

                this.orderableColumns[property].orders.forEach((item, i) => {
                    if(item === this.orderableColumns[property].currentOrder){
                        this.orderableColumns[property].currentOrderIndex = i;
                    }
                });
            }
            // Ставим состояние фильтров
            if(this.filter[property] !== undefined){
                const dates = this.$route.query[property].split(';');

                this.filter[property].from = dates[0];
                this.filter[property].to = dates[1];
            }
        }

        this.tableRows = this.fetchRows();
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
                this.managerInfo[managerId] = (await this.$backendApi.manager().infoById(managerId)).data
            }

            this.currentManager = this.managerInfo[managerId];
            this.isManagerPopupShown = true;
        },
        async fetchRows(){
            this.loading = true;
            try{
                this.tableRows = (await this.$backendApi.order().list(this.$route.query)).data;
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
            
            const from = this.filter.Date_sort.from ? this.filter.Date_sort.from : '';
            const to = this.filter.Date_sort.to ? this.filter.Date_sort.to : '';

            if(from === '' && to === '') {
                this.filter.errors.push('Вы должны выбрать хотя бы одну дату');
                return;
            }

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
