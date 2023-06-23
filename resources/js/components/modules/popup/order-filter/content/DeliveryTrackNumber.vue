<template>

    <div class="filter-content">
        <div class="filter-item">
            <h3 class="filter-item__title">Поиск по трек-номеру лоставки</h3>

            <ul class="filter__errors"
                v-if="errors.length !== 0"
                v-for="error in errors"
            >
                <li>{{ error }}</li>
            </ul>

            <form class="filter-item__control" @submit.prevent="submit">
                <input type="text" class="input" v-model.trim="value">
                <button type="submit" class="button">поиск</button>
            </form>
        </div>
    </div>

</template>

<script>
import {useOrderHistoryStorage} from "../../../../../storage/pinia/orderHistory/orderHistoryStorage";

export default {
    name: "DeliveryTrackNumber",
    data(){
        return {
            value: null,
            errors: [],
        }
    },
    methods: {
        async submit(){
            this.errors = [];

            const errors = await useOrderHistoryStorage().pickDeliveryTrackNumberFilter(this.value)
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
        display: flex;
        align-items: center;
        justify-content: space-between;

        & input {
            width: 100%;
            margin-right: 20px;
        }

        & button[type="submit"]{
            border-radius: 3px;
        }
    }
}

</style>
