<template>

    <Dialog :visible="isShown" modal header="Фильтр" @update:visible="$emit('hide')">
        <div class="filter">
            <div class="filter__sidebar">
                <PanelMenu :model="sidebar" ref="panelMenu"></PanelMenu>
            </div>
            <div class="filter__content">
                <template v-if="current_filter !== null">
                    <component :is="current_filter"></component>
                </template>
            </div>
        </div>
    </Dialog>

</template>

<script>
import Dialog from "primevue/dialog";
import PanelMenu from "primevue/panelmenu";
// Filter content
import CommercialOffer from "./content/CommercialOffer.vue";
import InvoiceDate from "./content/InvoiceDate.vue";
import Waybill from "./content/Waybill.vue";
import DeliveryTrackNumber from "./content/DeliveryTrackNumber.vue";
import DeliveryStatus from "./content/DeliveryStatus.vue";
import DeliveryDate from "./content/DeliveryDate.vue";
import InvoiceAmount from "./content/InvoiceAmount.vue";

export default {
    name: "OrderFilter",
    components: {
        Dialog,
        PanelMenu
    },
    data(){
        return {
            current_filter: null,
            sidebar: [
                {
                    label: 'Дата заказа',
                    command: () => {
                        this.current_filter = InvoiceDate
                    },
                },
                {
                    label: 'Сумма заказа',
                    command: () => {
                        this.current_filter = InvoiceAmount
                    },
                },
                {
                    label: 'Доставка',
                    items: [
                        {
                            label: 'Статус доставки',
                            command: () => {
                                this.current_filter = DeliveryStatus
                            },
                        },
                        {
                            label: 'Трек-номер доставки',
                            command: () => {
                                this.current_filter = DeliveryTrackNumber
                            },
                        },
                        {
                            label: 'Дата доставки',
                            command: () => {
                                this.current_filter = DeliveryDate
                            },
                        },
                    ]
                },
                {
                    label: 'Оплата',
                    items: [
                        {
                            label: 'КП (коммерческое предложение)',
                            command: () => {
                                this.current_filter = CommercialOffer
                            },
                        },
                    ]
                },
                {
                    label: 'Отгрузка',
                    items: [
                        {
                            label: 'ТН (транспортная накладная)',
                            command: () => {
                                this.current_filter = Waybill
                            },
                        }
                    ]
                }
            ]
        }
    },
    props: {
        isShown: {
            type: Boolean,
            required: true
        }
    },
    methods: {

    },
}
</script>

<style lang="scss" scoped>
@import "@scss/abstract/variables";

.filter{

    display: flex;

    &__sidebar{
        border-right: 3px solid #D9D9D9;
        padding-right: 5px;

        @media screen and (min-width: $col-xxl-min){
            width: 400px;
        }

        @media screen and (max-width: $col-xl-max) and (min-width: $col-xl-min){
            width: 300px;
        }

        @media screen and (max-width: $col-l-max) and (min-width: $col-l-min){
            width: 250px;
        }

        @media (max-width: $col-m-max) and (min-width: $col-m-min){
            width: 180px;
        }

        @media (max-width: $col-s-max) and (min-width: $col-s-min){
            width: 180px;
        }

        @media (max-width: $col-xs-max){
            width: 100%;
        }
    }

    &__content{
        padding: 0 20px;

        @media screen and (min-width: $col-xxl-min){
            width: $col-xxl-min - 600px;
        }

        @media screen and (max-width: $col-xl-max) and (min-width: $col-xl-min){
            width: $col-xl-min - 400px;
        }

        @media screen and (max-width: $col-l-max) and (min-width: $col-l-min){
            width: $col-l-min - 320px;
        }

        @media (max-width: $col-m-max) and (min-width: $col-m-min){
            width: $col-m-min - 220px;
        }

        @media (max-width: $col-s-max) and (min-width: $col-s-min){
            width: $col-s-min - 220px;
        }

        @media (max-width: $col-xs-max){
            width: 100%;
        }
    }
}

</style>
