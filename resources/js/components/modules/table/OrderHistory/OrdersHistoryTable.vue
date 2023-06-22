<template>

    <div>

        <div class="control-bar">
            <span></span>
            <i class="control-bar__filter pi pi-filter"
               style="color: rgb(55, 58, 60); font-size: 1.5rem"
                @click="filter.isModalShown = true"></i>
        </div>

        <div class="table-wrapper">

            <div class="loading-overlay" ref="loadingOverlay"
                :style="{visibility: this.orderHistoryStore.isLoading ? 'visible' : 'hidden'}"
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
                                    :style="{display: orderHistoryStore.sort.sorts.invoiceDate.currentOrder !== null ? 'block' : 'none'}"
                                    :direction="orderHistoryStore.sort.sorts.invoiceDate.currentOrder === 'asc' ? 'up' : 'down'"
                                >
                                </Triangle>
                                <Triangle class="order-direction" direction="down"
                                    :style="{opacity: 0.2, display: orderHistoryStore.sort.sorts.invoiceDate.currentOrder === null ? 'block' : 'none'}"
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
                                    :style="{display: orderHistoryStore.sort.sorts.lastShipmentDate.currentOrder !== null ? 'block' : 'none'}"
                                    :direction="orderHistoryStore.sort.sorts.lastShipmentDate.currentOrder === 'asc' ? 'up' : 'down'"
                                >
                                </Triangle>
                                <Triangle class="order-direction" direction="down"
                                    :style="{opacity: 0.2, display: orderHistoryStore.sort.sorts.lastShipmentDate.currentOrder === null ? 'block' : 'none'}"
                                >
                                </Triangle>
                                Дата последней отгрузки
                            </div>

                        </th>
                        <th class="head-column" @click="() => toggleOrder('lastPaymentDate')">

                            <div class="head-column__sortable">
                                <Triangle
                                    class="order-direction"
                                    :style="{display: orderHistoryStore.sort.sorts.lastPaymentDate.currentOrder !== null ? 'block' : 'none'}"
                                    :direction="orderHistoryStore.sort.sorts.lastPaymentDate.currentOrder === 'asc' ? 'up' : 'down'"
                                >
                                </Triangle>
                                <Triangle class="order-direction" direction="down"
                                    :style="{opacity: 0.2, display: orderHistoryStore.sort.sorts.lastPaymentDate.currentOrder === null ? 'block' : 'none'}"
                                >
                                </Triangle>
                                Дата последней оплаты
                            </div>

                        </th>
                        <th class="head-column">Произвольное поле</th>
                    </tr>
                </thead>
                <tbody class="table__body">
                    <template v-for="tableRow in orderHistoryStore.orders" :key="tableRow.id">
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
            :current-page="parseInt(orderHistoryStore.currentPage)" :max-page="this.orderHistoryStore.maxPage">
        </order-history-pagination>

        <popup-wrapper
            v-if="isManagerPopupShown"
            @closePopup="isManagerPopupShown = false"
        >
            <contact-manager :managerInfo="currentManager"></contact-manager>
        </popup-wrapper>

        <order-filter :is-shown="filter.isModalShown" @hide="filter.isModalShown = false"></order-filter>
    </div>

</template>

<script>
import OrdersHistoryTableRow from "./OrdersHistoryTableRow";
import PopupWrapper from "../../base/PopupWrapper";
import ContactManager from "../../popup/ContactManager";
import OrderHistoryPagination from "./OrderHistoryPagination.vue";
import Triangle from "../../../icons/order/Triangle.vue";
import OrderFilter from "../../popup/order-filter/Filter.vue";
import {useOrderHistoryStorage} from "../../../../storage/pinia/orderHistory/orderHistoryStorage";
import {mapStores} from "pinia";

export default {
    name: "OrdersHistoryTable",
    components: {
        OrdersHistoryTableRow,
        PopupWrapper,
        ContactManager,
        OrderHistoryPagination,
        Triangle,
        OrderFilter,
    },
    data() {
        return {
            isManagerPopupShown: false,
            currentManager: {},
            managerInfo: {},
            filter: {
                isModalShown: true,
            },
        }
    },
    computed: {
        ...mapStores(useOrderHistoryStorage)
    },
    watch: {
        'orderHistoryStore.isLoading'(){
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
        toggleOrder(sortColumnName){
            useOrderHistoryStorage().toggleSort(sortColumnName)
        },
    },
}
</script>

<style lang="scss" scoped>
@import "@scss/abstract/variables";

.pagination-container{
    margin: 20px auto;
    width: fit-content;
}

.control-bar{
    width: 100%;
    padding: 10px 0;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;

    &__filter{
        cursor: pointer;
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
