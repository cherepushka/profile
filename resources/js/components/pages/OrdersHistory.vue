<template>

    <div class="container">
        <orders-history-table></orders-history-table>
    </div>

</template>

<script>
import OrdersHistoryTable from "../modules/table/OrderHistory/OrdersHistoryTable";

export default {
    name: "OrdersHistory",
    components: {OrdersHistoryTable}
}
</script>

<style lang="scss" scoped>

</style>
