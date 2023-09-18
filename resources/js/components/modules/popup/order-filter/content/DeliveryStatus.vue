<template>

    <div class="filter-content">
        <div class="filter-item">
            <h3 class="filter-item__title">Поиск по статусу доставки</h3>

            <ul class="filter__errors"
                v-if="errors.length !== 0"
                v-for="error in errors"
            >
                <li>{{ error }}</li>
            </ul>

            <form class="filter-item__control" @submit.prevent="submit">
                <Dropdown v-model="value" :options="options"
                    option-label="title" option-value="value"
                    placeholder="Выберите статус"
                />
                <button type="submit" class="button">поиск</button>
            </form>
        </div>
    </div>

</template>

<script>
import Dropdown from "primevue/dropdown";
import {useOrderHistoryStorage} from "../../../../../storage/pinia/orderHistory/orderHistoryStorage";

export default {
    name: "DeliveryStatus",
    components: {
        Dropdown
    },
    data(){
        return {
            value: null,
            options: [],
            errors: [],
        }
    },
    async mounted() {
        const tmpOpts = await useOrderHistoryStorage().filter.filters.deliveryStatus.getAvailableOptions()
        for (const [key, value] of Object.entries(tmpOpts)){
            this.options.push({
                title: value,
                value: key
            })
        }
    },
    methods: {
        async submit(){
            this.errors = [];

            const errors = await useOrderHistoryStorage().pickDeliveryStatusFilter(this.value)
            if(errors.length > 0) {
                errors.forEach(message => {
                    this.errors.push(message);
                })
            }
        },
    },
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
