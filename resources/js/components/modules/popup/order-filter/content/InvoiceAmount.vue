<template>

    <div class="filter-content">
        <div class="filter-item">
            <h3 class="filter-item__title">Цена заказа</h3>

            <p>(цена в валюте заказа)</p>

            <ul class="filter__errors"
                v-if="errors.length !== 0"
                v-for="error in errors"
            >
                <li>{{ error }}</li>
            </ul>

            <form class="filter-item__control" @submit.prevent="submit">

                <div class="inputs">
                    <InputNumber inputId="integeronly" v-model="from" placeholder="От" />
                    <InputNumber inputId="integeronly" v-model="to" placeholder="До" />
                </div>
                <button class="button order-date__submit" type="submit">
                    Фильтровать
                </button>
            </form>
        </div>
    </div>

</template>

<script>
import {useOrderHistoryStorage} from "../../../../../storage/pinia/orderHistory/orderHistoryStorage";
import InputNumber from "primevue/inputnumber";

export default {
    name: "InvoiceAmount",
    components: {
        InputNumber,
    },
    data(){
        return {
            from: 0,
            to: 0,
            errors: [],
        }
    },
    methods: {
        async submit(){
            this.errors = [];

            const errors = await useOrderHistoryStorage().pickInvoiceAmountFilter(this.from, this.to)
            if(errors.length > 0) {
                errors.forEach(message => {
                    this.errors.push(message);
                })
            }
        },
    }
}
</script>

<style lang="scss" scoped>
@import "@scss/abstract/variables";

.filter__errors{
    color: $default-error-color;
}

.filter-item {
    width: 100%;

    &__title{
        margin: 0;
    }

    &__control{
        margin-top: 5px;
        flex-direction: column;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        & input {
            width: 100%;
            margin-right: 20px;
        }

        & button[type="submit"]{
            margin-top: 5px;
            border-radius: 3px;
        }
    }
}

</style>
