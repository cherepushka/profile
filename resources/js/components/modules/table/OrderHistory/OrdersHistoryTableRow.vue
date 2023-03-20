<template>

    <tr class="info-minimized-row" :class="{open: isExpandedContentShown}">
        
        <td style="cursor: pointer" @click="toggleTableExpandedContent">
            <Triangle :direction="isExpandedContentShown ? 'up' : 'down'"></Triangle>
        </td>
        <td>{{ toDate(tableRow.orderDate) }}</td>
        <td>{{ tableRow.items }}</td>
        <td>{{ tableRow.fullPrice.toFixed(2) }} {{ tableRow.currency }}</td>
        <td>
            <a href="" class="link" @click.prevent="$emit('managerClick', tableRow.manager.id)">
                {{ tableRow.manager.name }}
            </a>
        </td>
        <td>{{ tableRow.mailTrigger }}</td>
        <td>
            <a v-if="tableRow.payLink" 
                class="link" :href="tableRow.payLink" target="_blank"
            >
                Ссылка
            </a>
        </td>
        <td>{{ tableRow.paymentStatus }}</td>
        <td>{{ tableRow.shipmentStatus }}</td>
        <td>{{ toDate(tableRow.lastShipmentDate) }}</td>
        <td>{{ toDate(tableRow.lastPaymentDate) }}</td>
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
                :offer-docs-zip="expandedRowInfo?.offerDocsZip"
                :shipment-docs="expandedRowInfo.shipmentDocs"
                :shipment-docs-zip="expandedRowInfo?.shipmentDocsZip"
                :currency="expandedRowInfo.currency"
                :order-id="tableRow.id"
                :products="expandedRowInfo.products"
                :items-count="expandedRowInfo.itemsCount"
                :shipped-count="expandedRowInfo.shippedCount"
                :purePrice="expandedRowInfo.purePrice"
                :vatPrice="expandedRowInfo.vatPrice">
            </orders-history-row-expanded>
        </td>
    </tr>

</template>

<script>
import {timestampTo_ISO_8601_Date} from "./../../../../utils/functions/time";
import OrdersHistoryRowExpanded from "./OrdersHistoryRowExpanded";
import Triangle from "../../../icons/order/Triangle.vue";

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
            expandedRowInfo: {},
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
