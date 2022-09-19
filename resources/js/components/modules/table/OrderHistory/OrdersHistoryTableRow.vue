<template>

    <tr class="info-minimized-row" :class="{open: isExpandedContentShown}">
        <td style="cursor: pointer" @click="toggleTableExpandedContent">
            <div class="triangle-wrapper">
                <div :class="{
                    'triangle-up': isExpandedContentShown,
                    'triangle-down': !isExpandedContentShown
                    }" class="triangle"
                >
                </div>
            </div>
        </td>
        <td>
            <b>{{ tableRow.id }}</b>
        </td>
        <td>{{ tableRow.orderDate }}</td>
        <td>{{ tableRow.items }}</td>
        <td>{{ tableRow.fullPrice.toFixed(2) }}</td>
        <td>
            <a href="" class="link" @click.prevent="$emit('managerClick', tableRow.manager.id)">
                {{ tableRow.manager.name }}
            </a>
        </td>
        <td>{{ tableRow.mailTrigger }}</td>
        <td>
            <a class="link" :href="tableRow.payLink" target="_blank">Ссылка</a>
        </td>
        <td>{{ tableRow.orderStatus }}</td>
        <td>{{ tableRow.shipmentStatus }}</td>
        <td>{{ tableRow.lastShipmentDate }}</td>
        <td>{{ tableRow.lastPaymentDate }}</td>
        <td class="custom-value">
            <input class="custom-value__input input" type="text"
                   v-model="tableRow.customFieldValue"
                   :disabled="!isCustomValueEditing">

            <button v-if="!isCustomValueEditing" class="custom-value__button" type="button"
                @click.prevent="isCustomValueEditing = true">
                Редактировать
            </button>

            <button v-else class="custom-value__button" type="button"
                @click.prevent="saveCustomFieldValue">
                Сохранить
            </button>
        </td>
    </tr>

    <tr v-if="isExpandedContentShown">
        <td colspan="100%" style="padding: 0">
            <orders-history-row-expanded
                :offer-docs="expandedRowInfo.offerDocs"
                :shipment-docs="expandedRowInfo.shipmentDocs"
                :currency="expandedRowInfo.currency"
                :order-id="tableRow.id"
                :items="expandedRowInfo.items"
                :total-info="expandedRowInfo.total">
            </orders-history-row-expanded>
        </td>
    </tr>

</template>

<script>
import OrdersHistoryRowExpanded from "./OrdersHistoryRowExpanded";

export default {
    name: "OrdersHistoryTableRow",
    components: {
        OrdersHistoryRowExpanded
    },
    data() {
        return {
            isExpandedContentShown: false,
            isCustomValueEditing: false,
            expandedRowInfo: {
                offerDocs: [
                    {
                        id: 228,
                        title: 'Коммерческое предложение и счет на оплату №28080 от 16 августа 2022 г..pdf',
                        link: 'https://fluid-line.ru/assets/snippets/profile/Profile.class.php?invoice=28080&file=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D1%80%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5%20%D0%BF%D1%80%D0%B5%D0%B4%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D1%81%D1%87%D0%B5%D1%82%20%D0%BD%D0%B0%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D1%83%20%E2%84%9628080%20%D0%BE%D1%82%2016%20%D0%B0%D0%B2%D0%B3%D1%83%D1%81%D1%82%D0%B0%202022%C2%A0%D0%B3..pdf&method=showOfferPdf',
                        fileExtension: 'pdf'
                    },
                    {
                        id: 228,
                        title: 'Коммерческое предложение и счет на оплату №28080 от 16 августа 2022 г..xlsx',
                        link: 'https://fluid-line.ru/assets/snippets/profile/Profile.class.php?invoice=28080&file=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D1%80%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5%20%D0%BF%D1%80%D0%B5%D0%B4%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D1%81%D1%87%D0%B5%D1%82%20%D0%BD%D0%B0%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D1%83%20%E2%84%9628080%20%D0%BE%D1%82%2016%20%D0%B0%D0%B2%D0%B3%D1%83%D1%81%D1%82%D0%B0%202022%C2%A0%D0%B3..pdf&method=showOfferPdf',
                        fileExtension: 'xlsx'
                    },
                ],
                shipmentDocs: [
                    {
                        id: 228,
                        title: 'Реализация товаров и услуг 00000009576 от 05.09.2022.xlsx',
                        link: 'https://fluid-line.ru/assets/snippets/profile/Profile.class.php?invoice=28080&file=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D1%80%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5%20%D0%BF%D1%80%D0%B5%D0%B4%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D1%81%D1%87%D0%B5%D1%82%20%D0%BD%D0%B0%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D1%83%20%E2%84%9628080%20%D0%BE%D1%82%2016%20%D0%B0%D0%B2%D0%B3%D1%83%D1%81%D1%82%D0%B0%202022%C2%A0%D0%B3..pdf&method=showOfferPdf',
                        fileExtension: 'xlsx',
                    },
                    {
                        id: 228,
                        title: 'Счет-фактура выданный 000000016004 от 05.09.2022.xlsx',
                        link: 'https://fluid-line.ru/assets/snippets/profile/Profile.class.php?invoice=28080&file=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D1%80%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5%20%D0%BF%D1%80%D0%B5%D0%B4%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D1%81%D1%87%D0%B5%D1%82%20%D0%BD%D0%B0%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D1%83%20%E2%84%9628080%20%D0%BE%D1%82%2016%20%D0%B0%D0%B2%D0%B3%D1%83%D1%81%D1%82%D0%B0%202022%C2%A0%D0%B3..pdf&method=showOfferPdf',
                        fileExtension: 'xlsx',
                    },
                ],
                currency: 'RUB',
                items: [
                    {
                        title: 'ACBU-6M; Соединитель с креплением на панель из нержавеющей стали O.D. 6мм, серия CBU',
                        count: 1,
                        purePrice: 987.20,
                        VatPrice: 1184.64,
                        shippedCount: 1,
                    }
                ],
                total: {
                    itemsCount: 28,
                    itemsLength: 18,
                    purePrice: 27554.40,
                    VatPrice: 33065.28,
                    shippedCount: null
                }
            },
        }
    },
    emits: ['managerClick'],
    props: {
        tableRow: {
            type: Object,
            required: true
        }
    },
    methods: {
        saveCustomFieldValue() {
            this.isCustomValueEditing = false;
        },
        toggleTableExpandedContent() {

            // Now we must fetch expanded content
            if (!this.isExpandedContentShown && this.expandedRowInfo === {}) {

            }

            this.isExpandedContentShown = !this.isExpandedContentShown;
        }
    }
}
</script>

<style lang="scss" scoped>

.info-minimized-row{

    &:not(:last-child){
        border-bottom: 1px solid #eceeef;
    }

    &:hover{
        background-color: #fafafa;
    }

    & > td{
        text-align: center;
        font-size: 13px;
        padding: 10px 5px;
    }

    &.open{
        background-color: #e6ecf3;
    }
}

.triangle-wrapper{
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.triangle{
    margin: 0;
    padding: 0;
    width: 0;
    height: 0;
    border-width: 0 6px 8px 6px;
    border-radius: 2px;
    border-color: transparent transparent rgb(55, 58, 60) transparent;
    border-style: inset;
}

.triangle-up{
    transform: rotate(0deg);
}

.triangle-down{
    transform: rotate(180deg);
}

.custom-value{
    max-width: 140px;

    &__input{
        width: 100%;
        &:disabled{
            cursor: not-allowed;
        }
    }

    &__button{
        width: 100%;
        max-width: 100%;
        margin-top: 5px;
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border-radius: 0.25rem;
    }
}

</style>
