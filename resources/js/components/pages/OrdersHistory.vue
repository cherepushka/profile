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

export default {
    name: "OrdersHistory",
    components: {OrdersHistoryTable},
    data(){
        return {}
    },
    mounted(){
        if(!this?.$route?.query?.page){
            this.$router.push({name: 'order_history', query: {page: 1}})
        }
    },
    watch: {
        '$route': function(to) {
            if(to.name === 'order_history'){
                if(!to.query?.page){
                    this.$router.push({name: 'order_history', query: {page: 1}})
                }
            }
        },
    }
}
</script>

<style lang="scss" scoped>

</style>
