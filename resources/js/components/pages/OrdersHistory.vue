<template>

    <div class="container">
        <orders-history-table
            v-if="$route?.query?.page"
            :page="parseInt($route.query.page)"
        >
        </orders-history-table>
    </div>

</template>

<script>

import OrdersHistoryTable from "../modules/table/OrderHistory/OrdersHistoryTable";
import {useOrderHistoryStorage} from "../../storage/pinia/orderHistory/orderHistoryStorage";

export default {
    name: "OrdersHistory",
    components: {OrdersHistoryTable},
    data(){
        return {}
    },
    async mounted(){
        if(!this?.$route?.query?.page){
            this.$router.push({name: 'order_history', query: {page: 1}})
        }

        await useOrderHistoryStorage().setStateFromSerialized({
            page: this.$route.query?.page === undefined ? null : this.$route.query.page,
            sort: this.$route.query?.sort === undefined ? null : this.$route.query.sort,
            filters: this.$route.query?.filters === undefined ? [] : this.$route.query.filters.split(','),
        })

        useOrderHistoryStorage().$subscribe((mutation, state) => {
            let query = {...this.$route.query}

            if (state.sort.active !== null) {
                query.sort = state.sort.active
            } else {
                delete query.sort
            }

            if (state.filter.activeFilters.length !== 0) {

                let serializedFilters = []
                state.filter.activeFilters.forEach(filterName => {
                    const f = state.filter.filters[filterName]
                    serializedFilters.push(f.serializeFunc(f.value))
                })

                query.filters = serializedFilters.join(',')
            } else {
                delete query.filters
            }

            if (state.currentPage) {
                query.page = state.currentPage
            } else {
                state.currentPage = 1
            }

            this.$router.push({
                name: this.$route.name,
                params: this.$route.params,
                query: query,
            })
        })
    },
}
</script>

<style lang="scss" scoped>

</style>
