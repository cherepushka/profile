<template>

    <div class="filter-content">
        <div class="filter-item">
            <h3 class="filter-item__title">Дата доставки</h3>

            <ul class="filter__errors"
                v-if="errors.length !== 0"
                v-for="error in errors"
            >
                <li>{{ error }}</li>
            </ul>

            <form class="filter-item__control" @submit.prevent="submit">
                <div class="inputs">
                    <Calendar date-format="yy-mm-dd" v-model="from" placeholder="С" />
                    <Calendar date-format="yy-mm-dd" v-model="to" placeholder="По" />
                </div>
                <button class="button order-date__submit" type="submit">
                    Фильтровать
                </button>
            </form>
        </div>
    </div>

</template>

<script>
import Calendar from "primevue/calendar";
import {useOrderHistoryStorage} from "../../../../../storage/pinia/orderHistory/orderHistoryStorage";

export default {
    name: "DeliveryDate",
    components: {
        Calendar,
    },
    data(){
        return {
            from: null,
            to: null,
            errors: [],
        }
    },
    methods: {
        async submit(){
            this.errors = [];

            const to = this.to ? Date.parse(this.to) : null;
            const from = this.from ? Date.parse(this.from) : null;

            const errors = await useOrderHistoryStorage().pickDeliveryDateFilter(from, to)
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

        & button[type="submit"]{
            margin-top: 5px;
            border-radius: 3px;
        }
    }
}

</style>
