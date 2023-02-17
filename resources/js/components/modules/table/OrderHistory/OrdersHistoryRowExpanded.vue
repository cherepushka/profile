<template>

    <div class="expanded">

        <div class="invoice-docs">
            <h3 class="invoice-docs__title">Документы к заказу</h3>

            <div class="docs">
                <h4 class="docs__title">Коммерческое предложение:</h4>

                <ol class="docs__list" v-if="offerDocs.length > 0">
                    <li v-for="document in offerDocs">
                        <a class="doc-link" @click="() => downloadSingleDocument(document.id)">
                            {{ document.title }}
                        </a>
                    </li>
                </ol>
                <p class="docs__not-found" v-else>
                    Файлы отсутствуют
                </p>
            </div>
            <div class="docs">
                <h4 class="docs__title">Отгрузка:</h4>

                <ol class="docs__list" v-if="shipmentDocs.length > 0">
                    <li v-for="document in shipmentDocs">
                        <a class="doc-link" @click="() => downloadSingleDocument(document.id)">
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

        <a class="download-csv">Скачать в формате *.csv</a>

    </div>

</template>

<script>
import {decryptAndDownload} from "../../../../composables/archive/unpack"

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
        downloadAll(){
            // new Download().invoiceDocumentById(123, 123123123, 'test.zip');
        },
        async downloadSingleDocument(documentId){
            const blob = await this.$backendApi.download().documentById(documentId);
            decryptAndDownload(blob, '9fb77d8882d0ab935a60f04c7d31a266df5398897d795a9e3f6daf43bcbf5998', '123.pdf')
        },
    },
    props: {
        offerDocs: {
            type: Array,
            required: true,
        },
        shipmentDocs: {
            type: Array,
            required: true
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

    @media screen and (min-width: $col-xxl-min){
        max-width: $col-xxl-min;
    }

    @media screen and (max-width: $col-xl-max) and (min-width: $col-xl-min){
        max-width: $col-xl-min;
    }

    @media screen and (max-width: $col-l-max) and (min-width: $col-l-min){
        max-width: $col-l-min;
    }

    @media (max-width: $col-m-max) and (min-width: $col-m-min){
        max-width: $col-m-min;
    }

    @media (max-width: $col-s-max) and (min-width: $col-s-min){
        max-width: $col-s-min;
    }

    @media (max-width: $col-xs-max){
        max-width: 100%;
    }
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

    &:hover{
        text-decoration: underline;
    }
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
