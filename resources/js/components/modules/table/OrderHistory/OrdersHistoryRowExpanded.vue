<template>

    <div class="expanded">

        <div class="invoice-docs">
            <h3 class="invoice-docs__title">Документы к заказу</h3>

            <div class="docs">

                <a href="#" class="docs__title"
                    :style="{textDecoration: offerDocs.length > 0 ? 'underline' : 'none'}"
                    @click.prevent="() => downloadDocument(offerDocsZip?.id, offerDocsZip?.title)"
                >
                    Коммерческое предложение:
                </a>

                <ol class="docs__list" v-if="offerDocs.length > 0">
                    <li v-for="document in offerDocs">
                        <a class="doc-link" @click="() => downloadDocument(document.id, document.title)">
                            {{ document.title }}
                        </a>
                    </li>
                </ol>
                <p class="docs__not-found" v-else>
                    Файлы отсутствуют
                </p>
            </div>
            <div class="docs">

                <a href="#" class="docs__title"
                    :style="{textDecoration: shipmentDocs.length > 0 ? 'underline' : 'none'}"
                    @click.prevent="() => downloadDocument(shipmentDocsZip?.id, shipmentDocsZip?.title)"
                >
                    Отгрузка:
                </a>

                <ol class="docs__list" v-if="shipmentDocs.length > 0">
                    <li v-for="document in shipmentDocs">
                        <a class="doc-link" @click="() => downloadDocument(document.id, document.title)">
                            {{ document.title }}
                        </a>
                    </li>
                </ol>
                <p class="docs__not-found" v-else>
                    Файлы отсутствуют
                </p>
            </div>

            <a class="invoice-docs__download-all" @click="downloadAll">Скачать всё</a>
        </div>

        <table class="table">
            <thead class="table__head">
                <tr>
                    <th class="heading-column">#</th>
                    <th class="heading-column">Наименование</th>
                    <th class="heading-column">Количество</th>
                    <th class="heading-column">Стоимость</th>
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

        <!-- <a class="download-csv">Скачать в формате *.csv</a> -->

    </div>

</template>

<script>
import {decryptAndDownload} from "../../../../composables/archive/unpack"
import { useUserStorage } from "../../../../storage/pinia/userStorage";

export default {
    name: "OrdersHistoryRowExpanded",
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
            this.$log.error({
                component: 'OrdersHistoryRowExpanded', function: 'mounted',
                message: `can\`t assign currency symbol because '${this.currency}' is unknown`,
            });
        }

    },
    methods: {
        async downloadAll(){
            const blob = await this.$backendApi.download().allDocuments(this.orderId);
            decryptAndDownload(blob, useUserStorage().password, 'order.zip')
        },
        async downloadDocument(documentId, documentTitle){
            if(!documentId){
                return;
            }

            const blob = await this.$backendApi.download().documentById(documentId);
            decryptAndDownload(blob, useUserStorage().password, documentTitle)
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
    }
}
</script>

<style lang="scss" scoped>
@import "@scss/abstract/variables";

.expanded{
    border-top: 1px solid #000000;
    padding: 10px;
    background-color: #e6ecf3;
    box-sizing: border-box;
}

.invoice-docs{
    margin-bottom: 20px;

    &__download-all{
        font-weight: 700;
        cursor: pointer;
        text-decoration: underline;
    }
}

.docs{

    &__title{
        margin: 5px 0;
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
