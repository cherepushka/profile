<template>

    <Dialog :visible="isShown" modal header="Вид таблицы" @update:visible="$emit('hide')">
        <div class="settings">
            <Button label="Применить" style="margin-bottom: 10px;" @click="selectionApplied" />
            <DataTable 
                v-model:selection="orderHistoryStore.orderInfoColumns.selected" 
                :value="orderHistoryStore.orderInfoColumns.allAvailable" 
                dataKey="id" tableStyle="min-width: 50rem"
            >
                <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                <Column field="title" header="Название колонки"></Column>
            </DataTable>
        </div>
    </Dialog>

</template>

<script>
import Dialog from "primevue/dialog";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import { mapStores } from "pinia";
import { useOrderHistoryStorage } from "../../../storage/pinia/orderHistory/orderHistoryStorage";

export default {
    name: "OrderHistoryTableSettings",
    components: {
        Dialog,
        DataTable,
        Column,
        Button,
    },
    props: {
        isShown: {
            type: Boolean,
            required: true
        }
    },
    computed: {
        ...mapStores(useOrderHistoryStorage)
    },
    methods: {
        selectionApplied(){
            useOrderHistoryStorage().applyColumnSelection();
        }
    },
}
</script>

<style lang="scss" scoped>
@import '@scss/abstract/variables';


</style>