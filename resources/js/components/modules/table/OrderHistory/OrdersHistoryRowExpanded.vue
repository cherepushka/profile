<template>

    <div class="expanded">

        <table class="table">
            <thead class="table__head">
            <tr>
                <th class="heading-column">#</th>
                <th class="heading-column">Наименование</th>
                <th class="heading-column">Количество</th>
                <th class="heading-column">Стоимость за шт. без НДС</th>
                <th class="heading-column">Стоимость с НДС</th>
                <th class="heading-column">Отгружено</th>
            </tr>
            </thead>
            <tbody class="table__body">

            <tr v-for="(item, key) in products" class="item">
                <td class="item__cell">
                    {{ key + 1 }}
                </td>
                <td class="item__cell item__title">
                    {{ item.title }}
                </td>
                <td class="item__cell">
                    {{ item.count }} {{ item.unit }}
                </td>
                <td class="item__cell">
                    {{ item.purePrice.toFixed(2) }} {{ currentCurrencySymbol }}
                </td>
                <td class="item__cell">
                    {{ item.vatPrice.toFixed(2) }} {{ currentCurrencySymbol }}
                </td>
                <td class="item__cell">
                    {{ item.shippedCount ?? '-' }}
                </td>
            </tr>

            <tr class="total">
                <td class="item__cell">Итого:</td>
                <td class="item__cell"></td>
                <td class="item__cell">
                    <template v-if="itemsCount" v-for="k, v in itemsCount">
                        {{ k }} {{ v }}
                        <br>
                    </template>
                </td>
                <td class="item__cell">{{ purePrice.toFixed(2) }} {{ currentCurrencySymbol }}</td>
                <td class="item__cell">{{ vatPrice.toFixed(2) }} {{ currentCurrencySymbol }}</td>
                <td class="item__cell">
                    <template v-if="shippedCount" v-for="k, v in shippedCount">
                        {{ k }} {{ v }}
                        <br>
                    </template>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="invoice-docs">
            <Accordion :multiple="true">
                <AccordionTab>
                    <template v-slot:header>
                        <h3 class="invoice-docs__title">
                            Документы к заказу

                            <button class="fc-button" @click="downloadAll" style="
                                border: 1px solid #000000;
                                background-color: transparent;
                                border-radius: 5px;
                                padding: 5px 20px;"
                                :disabled="offerDocs.length <= 0 && shipmentDocs <= 0"
                            >
                                Скачать всё
                            </button>
                        </h3>
                    </template>

                    <template v-slot:default>
                        <TabView>
                            <TabPanel header="Коммерческое предложение" :disabled="offerDocs.length <= 0">

                                <div v-if="offerDocs.length > 0">
                                    <ol class="docs__list">
                                        <li v-for="document in offerDocs">
                                            <a class="doc-link" @click="() => downloadDocument(document.id, document.title)">
                                                {{ document.title }}
                                            </a>
                                        </li>
                                    </ol>

                                    <button class="docs__download" @click.prevent="() => downloadDocument(offerDocsZip?.id, offerDocsZip?.title)">
                                        {{ 'Скачать (' + offerDocs.length + ')' }}
                                    </button>
                                </div>

                            </TabPanel>

                            <TabPanel header="Отгрузка" :disabled="shipmentDocs.length <= 0">

                                <div v-if="shipmentDocs.length > 0">
                                    <ol class="docs__list">
                                        <li v-for="document in shipmentDocs">
                                            <a class="doc-link" @click="() => downloadDocument(document.id, document.title)">
                                                {{ document.title }}
                                            </a>
                                        </li>
                                    </ol>

                                    <button class="docs__download"  @click.prevent="() => downloadDocument(shipmentDocsZip?.id, shipmentDocsZip?.title)">
                                        {{ 'Скачать (' + offerDocs.length + ')' }}
                                    </button>
                                </div>

                            </TabPanel>
                        </TabView>
                    </template>
                </AccordionTab>
            </Accordion>
         </div>

        <Accordion v-if="deliveryStatuses.length > 0" :multiple="true">
            <AccordionTab header="Отгрузка">
                <TabView>
                    <TabPanel
                        v-for="v in deliveryStatuses"
                        :header="'от ' + timestampTo_ISO_8601_Date(v.shippingDate * 1000)"
                        :disabled="offerDocs.length <= 0"
                    >
                        <table>
                            <tbody>
                                <tr>
                                    <td>Номер ТН (транспортной накладной):</td>
                                    <td style="font-weight: bold">{{ v.realizationNumber }}</td>
                                </tr>
                                <tr>
                                    <td>Дата отгрузки:</td>
                                    <td style="font-weight: bold">{{ timestampTo_ISO_8601_Date(v.shippingDate * 1000) }}</td>
                                </tr>
                                <tr>
                                    <td>Транспортная компания:</td>
                                    <td style="font-weight: bold">{{ v.transportCompany }}</td>
                                </tr>
                                <tr>
                                    <td>Трек номер:</td>
                                    <td style="font-weight: bold">{{ v.trackingCode }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table">
                            <thead class="table__head">
                            <tr>
                                <th class="heading-column">#</th>
                                <th class="heading-column">Статус</th>
                                <th class="heading-column">Местоположение</th>
                                <th class="heading-column">Время</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(deliveryStatus, key) in v.history">
                                <td class="item__cell">
                                    {{ key + 1 }}
                                </td>
                                <td class="item__cell">
                                    {{ deliveryStatus.title }}
                                </td>
                                <td class="item__cell">
                                    {{ deliveryStatus.geo }}
                                </td>
                                <td class="item__cell">
                                    {{ deliveryStatus.datetime }}
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </TabPanel>
                </TabView>
            </AccordionTab>
        </Accordion>
    </div>

</template>

<script>
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import Accordion from "primevue/accordion";
import AccordionTab from "primevue/accordiontab";
import {decryptAndDownload} from "../../../../composables/archive/unpack"
import { useUserStorage } from "../../../../storage/pinia/userStorage";
import {timestampTo_ISO_8601_Date} from "../../../../utils/functions/time";

export default {
    name: "OrdersHistoryRowExpanded",
    components: {
        TabView,
        TabPanel,
        Accordion,
        AccordionTab,
    },
    data() {
        return {
            currencySymbols: {
                RUB: '₽',
                USD: '$',
                EUR: '€',
            },
            currentCurrencySymbol: ''
        }
    },
    mounted() {
        if (this.currencySymbols.hasOwnProperty(this.currency)) {
            this.currentCurrencySymbol = this.currencySymbols[this.currency];
        } else {
            this.currentCurrencySymbol = this.currency

            this.$log.error({
                component: 'OrdersHistoryRowExpanded', function: 'mounted',
                message: `can\`t assign currency symbol because '${this.currency}' is unknown`,
            });
        }

    },
    methods: {
        timestampTo_ISO_8601_Date,
        async downloadAll(){
            const blob = await this.$backendApi.download().allDocuments(this.orderId);
            await decryptAndDownload(blob, useUserStorage().password, 'order.zip')
        },
        async downloadDocument(documentId, documentTitle){
            if(!documentId){
                return;
            }

            const blob = await this.$backendApi.download().documentById(documentId);
            await decryptAndDownload(blob, useUserStorage().password, documentTitle)
        },
    },
    props: {
        offerDocs: {
            type: Array,
            required: true,
        },
        offerDocsZip: {
            type: Object,
            required: false,
        },
        shipmentDocs: {
            type: Array,
            required: true
        },
        shipmentDocsZip: {
            type: Object,
            required: false,
        },
        currency: {
            type: String,
            required: true
        },
        orderId: {
            type: String,
            required: true,
        },
        products: {
            type: Array,
            required: true,
        },
        itemsCount: {
            type: Object,
            required: false,
        },
        shippedCount: {
            type: Object,
            required: false,
        },
        purePrice: {
            type: Number,
            required: true,
        },
        vatPrice: {
            type: Number,
            required: true,
        },
        deliveryStatuses: {
            type: Array,
            required: true,
        }
    }
}
</script>

<style lang="scss" scoped>
@import "@scss/abstract/variables";

.expanded{
    border: 1px solid #000000;
    padding: 10px;
    box-sizing: border-box;
}

.invoice-docs{
    margin-bottom: 20px;
}

.docs{

    &__title{
        margin: 5px 0;
    }

    &__download{
        margin-left: 20px;
    }
}

.invoice-docs{

    &__title{
        margin: 0;
    }
}

.doc-link{
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    text-decoration: underline;
}

.table{
    background-color: #FFFFFF;
    width: 100%;
    max-width: 100%;
    margin-bottom: 1rem;
    border-collapse: collapse;

    &__head{
        border-bottom: 2px solid #eceeef;
    }
}

.heading-column{
    padding: 15px;
    text-align: left;
    vertical-align: center;
    font-size: 13px;
}

.item{

    &__cell{
        text-align: left;
        padding: 10px;
        border-bottom: 1px solid #eceeef;
    }

}

.download-csv{
    cursor: pointer;
    text-decoration: underline;
}

</style>
