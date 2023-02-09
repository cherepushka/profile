<template>

    <div class="pagination">

        <button 
            class="pagination__item" 
            @click="() => toPage(1)" 
            :disabled="currentPage - 4 < 1"
        >
            &lt;&lt;
        </button>

        <template v-for="num in range(3, currentPage-3)">
            <button 
                class="pagination__item" 
                v-if="num > 0" 
                @click="() => toPage(num)"
            >
                {{ num }}
            </button>
        </template>

        <button class="pagination__item active">
            {{ currentPage }}
        </button>

        <template v-for="num in range(3, currentPage+1)">
            <button 
                class="pagination__item" 
                v-if="num <= maxPage" 
                @click="() => toPage(num)"
            >
                {{ num }}
            </button>
        </template>
       
        <button 
            class="pagination__item" 
            @click="() => toPage(maxPage)" 
            :disabled="currentPage + 4 > maxPage"
        >
            >>
        </button>
    </div>

</template>

<script>

export default{
    name: 'OrderHistoryPagination',
    props: {
        currentPage: {
            type: Number,
            required: true,
        },
        maxPage: {
            type: Number,
            required: true
        }
    },
    methods: {
        range(size, startAt = 0) {
            return [...Array(size).keys()].map(i => i + startAt);
        },
        toPage(pageNum){
            this.$router.push({name: 'order_history', query: {page: pageNum}})
        },
    },
}

</script>

<style lang="scss" scoped>
@import "@scss/abstract/variables";

.pagination{
    display: block;
    width: fit-content;

    &__item{
        cursor: pointer;
        margin: 0 10px;
        padding: 9px;
        border-radius: 3px;

        &:hover{
            background-color: darken($default-btns-color, 30%);
        }

        &.active{
            background-color: rgb(90, 90, 90);
            color: #FFFFFF;
        }
    }
}

</style>