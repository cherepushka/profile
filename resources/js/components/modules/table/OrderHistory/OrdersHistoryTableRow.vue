<template>

    <tr class="info-minimized-row" :class="{open: isExpandedContentShown}">

        <td style="cursor: pointer" @click="toggleTableExpandedContent">
            <Triangle :direction="isExpandedContentShown ? 'up' : 'down'"></Triangle>
        </td>
        <td v-if="selectedColumns.hasOwnProperty('invoiceDate')">
            {{ toDate(tableRow.orderDate) }}
        </td>
        <td v-if="selectedColumns.hasOwnProperty('items')">
            {{ tableRow.items }}
        </td>
        <td v-if="selectedColumns.hasOwnProperty('fullPrice')">
            {{ tableRow.fullPrice.toFixed(2) }} {{ tableRow.currency }}
        </td>
        <td v-if="selectedColumns.hasOwnProperty('managerName')">
            <a href="" class="link" @click.prevent="$emit('managerClick', tableRow.manager.id)">
                {{ tableRow.manager.name }}
            </a>
        </td>
        <td v-if="selectedColumns.hasOwnProperty('mail_trigger')">
            {{ tableRow.mailTrigger }}
        </td>
        <td v-if="selectedColumns.hasOwnProperty('payLink')">
            <a v-if="tableRow.payLink" class="link" :href="tableRow.payLink" target="_blank">
                Ссылка
            </a>
        </td>
        <td v-if="selectedColumns.hasOwnProperty('paymentStatus')">
            {{ tableRow.paymentStatus }}
        </td>
        <td v-if="selectedColumns.hasOwnProperty('shipmentStatus')">
            {{ tableRow.shipmentStatus }}
        </td>
        <td v-if="selectedColumns.hasOwnProperty('lastShipmentDate')">
            {{ toDate(tableRow.lastShipmentDate) }}
        </td>
        <td v-if="selectedColumns.hasOwnProperty('lastPaymentDate')">
            {{ toDate(tableRow.lastPaymentDate) }}
        </td>
        <td class="custom-value" v-if="selectedColumns.hasOwnProperty('customFieldValue')">
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
        <td v-if="selectedColumns.hasOwnProperty('commercialOfferNumber')">
            {{ tableRow.commercialOfferNumber }}
        </td>
        <td v-if="selectedColumns.hasOwnProperty('deliveryStatuses')">
            <template
                v-if="tableRow.lastEventGroups.length !== 0"
                v-for="eventGroup in tableRow.lastEventGroups"
            >
                {{ eventGroup }} <br><br>
            </template>
        </td>
        <td v-if="selectedColumns.hasOwnProperty('deliveryDates')">
            <template
                v-if="tableRow.lastDeliveryDates.length !== 0"
                v-for="deliveryDate in tableRow.lastDeliveryDates"
            >
                {{ deliveryDate }} <br><br>
            </template>
        </td>
    </tr>

    <tr v-if="isExpandedContentShown">
        <td colspan="12" style="padding: 0">
            <orders-history-row-expanded
                :offer-docs="expandedRowInfo.offerDocs"
                :offer-docs-zip="expandedRowInfo?.offerDocsZip"
                :shipment-docs="expandedRowInfo.shipmentDocs"
                :shipment-docs-zip="expandedRowInfo?.shipmentDocsZip"
                :currency="expandedRowInfo.currency"
                :order-id="tableRow.id"
                :products="expandedRowInfo.products"
                :items-count="expandedRowInfo.itemsCount"
                :shipped-count="expandedRowInfo.shippedCount"
                :purePrice="expandedRowInfo.purePrice"
                :vatPrice="expandedRowInfo.vatPrice"
                :delivery-statuses="expandedRowInfo.deliveryStatuses"
            >
            </orders-history-row-expanded>
        </td>
    </tr>

</template>

<script>
import {timestampTo_ISO_8601_Date} from "../../../../utils/functions/time";
import { useOrderHistoryStorage } from "../../../../storage/pinia/orderHistory/orderHistoryStorage";
import OrdersHistoryRowExpanded from "./OrdersHistoryRowExpanded";
import Triangle from "../../../icons/order/Triangle.vue";
import { mapStores } from "pinia";

export default {
    name: "OrdersHistoryTableRow",
    components: {
        OrdersHistoryRowExpanded,
        Triangle
    },
    data() {
        return {
            isExpandedContentShown: false,
            isCustomValueEditing: false,
            isExpandedRowInfoFetched: false,
            storageUnsubFuncs: [],
            selectedColumns: {},
            expandedRowInfo: {},
        }
    },
    beforeMount(){
        this.orderHistoryStore.orderInfoColumns.selected.forEach(col => {
            this.selectedColumns[col.id] = null;
        });

        this.storageUnsubFuncs.push(useOrderHistoryStorage().$onAction(({name, after}) => {
            if(name === 'applyColumnSelection'){
                after(() => {
                    this.selectedColumns = {}
                    this.orderHistoryStore.orderInfoColumns.selected.forEach(col => {
                        this.selectedColumns[col.id] = null;
                    });
                })
            }
        }))
    },
    unmounted(){
        this.storageUnsubFuncs.forEach(f => f())
    },
    computed: {
        ...mapStores(useOrderHistoryStorage)
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
            // TODO ошибку обработать в глобальном уведомлении
            this.$backendApi.order().setCustomValue(this.tableRow.id, this.tableRow.customFieldValue)
            this.isCustomValueEditing = false;
        },
        async toggleTableExpandedContent() {
            // Now we must fetch expanded content
            if (this.isExpandedRowInfoFetched === false) {
                this.expandedRowInfo = (await this.$backendApi.order().orderById(this.tableRow.id)).data.data;
                this.isExpandedRowInfoFetched = true;
            }

            this.isExpandedContentShown = !this.isExpandedContentShown;
        },
        toDate(timestamp) {
            if(timestamp === null){
                return null;
            }

            return timestampTo_ISO_8601_Date(timestamp * 1000)
        },
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
        min-width: 50px;
        width: stretch;

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
