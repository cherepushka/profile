<template>

    <div>

        <div class="filters">
            <div class="order-date">
                <h3 class="order-date__title">Фильтр по дате заказа</h3>

                <div class="order-date__manipulation">
                    <input class="input order-date__input" type="date">
                    <input class="input order-date__input" type="date">
                    <button class="button order-date__submit" type="submit">Отфильтровать</button>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead class="table__head">
                    <tr>
                        <th class="head-column"></th>
                        <th class="head-column">№</th>
                        <th class="head-column">Дата заказа</th>
                        <th class="head-column">Позиции</th>
                        <th class="head-column">Стоимость с НДС</th>
                        <th class="head-column">Менеджер</th>
                        <th class="head-column">Триггер письма</th>
                        <th class="head-column">Ссылка оплаты</th>
                        <th class="head-column">Статус оплаты</th>
                        <th class="head-column">Статус отгрузки</th>
                        <th class="head-column">Дата последней отгрузки</th>
                        <th class="head-column">Дата последней оплаты</th>
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

export default {
    name: "OrdersHistoryTable",
    components: {
        OrdersHistoryTableRow,
        PopupWrapper,
        ContactManager,
    },
    data() {
        return {
            isManagerPopupShown: false,
            currentManager: {},
            managerInfo: {
                228: {
                    name: 'Евгения Каманина',
                    photo: 'https://fluid-line.ru/signatures/images/fl2.jpg',
                    email: 'kam@fluid-line.ru',
                    phone: '+7(495) 984-41-01 (доб.123)',
                    whatsApp: '+7(926) 834-17-32',
                    position: 'помощник менеджера'
                }
            },
            tableRows: [
                {
                    id: 30273,
                    orderDate: '08.09.2022',
                    items: 11,
                    fullPrice: 33065.00,
                    manager: {
                        id: 228,
                        name: 'Евгения Каманина',
                    },
                    mailTrigger: '#КАМ0938677',
                    payLink: 'https://fluid-line.ru/123123/123123',
                    orderStatus: 'не оплачен',
                    shipmentStatus: 'не доставлен',
                    lastShipmentDate: '2022.2022',
                    lastPaymentDate: '2021.2021',
                    customFieldValue: '21321321',
                },
                {
                    id: 30273,
                    orderDate: '08.09.2022',
                    items: 11,
                    fullPrice: 33065.01,
                    manager: {
                        id: 228,
                        name: 'Евгения Каманина',
                    },
                    mailTrigger: '#КАМ0938677',
                    payLink: 'https://fluid-line.ru/123123/123123',
                    orderStatus: 'не оплачен',
                    shipmentStatus: 'не доставлен',
                    lastShipmentDate: '2022.2022',
                    lastPaymentDate: '2021.2021',
                    customFieldValue: '21321321',
                }
            ]
        }
    },
    methods: {
        toggleManagerPopup(managerId) {
            if (!managerId) {
                this.$log.error({
                    component: 'OrdersHistoryTable', function: 'toggleManagerPopup',
                    message: '`managerId` not provided to function',
                });
            }

            //if we don`t already load manager info, do API call
            if (!this.managerInfo.hasOwnProperty(managerId)) {

            }

            this.currentManager = this.managerInfo[managerId];
            this.isManagerPopupShown = true;
        }
    }
}
</script>

<style lang="scss" scoped>

.filters{
    margin-bottom: 30px;
}

.order-date{

    &__input{
        margin-right: 15px;
    }
}

.table-wrapper{
    overflow-x: scroll;
    width: 100%;
}

.table{
    width: 100%;
    border-collapse: collapse;
    background-color: transparent;
    border-spacing: 3px;

    &__head{
        //position: sticky;
        top: 48px;
        background-color: #fff;
        z-index: 5;
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
}

</style>
