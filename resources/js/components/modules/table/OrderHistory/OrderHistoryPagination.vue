<template>

    <div class="pagination">

        <button
            class="pagination__item"
            @click="() => toPage(1)"
            :disabled="currentPage - 4 < 1"
        >
            &lt;&lt;
        </button>

        <button
            class="pagination__item"
            @click="() => toPage(currentPage - 1)"
            :disabled="currentPage - 1 < 1"
        >
            &lt;
        </button>

        <template v-for="num in paginationRange">
            <button
                class="pagination__item" :class="{'active': num === currentPage}"
                @click="() => toPage(num)"
            >
                {{ num }}
            </button>
        </template>

        <button
            class="pagination__item"
            @click="() => toPage(currentPage + 1)"
            :disabled="currentPage + 1 > maxPage"
        >
            >
        </button>

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

import {useOrderHistoryStorage} from "../../../../storage/pinia/orderHistory/orderHistoryStorage";

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
        toPage(pageNum){
            useOrderHistoryStorage().setCurrentPage(pageNum)
        },
    },
    computed: {
        paginationRange(){

            const maxEdgeLength = 6;

            let response = [];
            let beforeCurrentNums = 0;

            for (let num = this.currentPage - maxEdgeLength; num <= this.currentPage + maxEdgeLength; num++){
                if(num > 0 && num <= this.maxPage){

                    if(num < this.currentPage && beforeCurrentNums < maxEdgeLength / 2){
                        beforeCurrentNums++;
                    }

                    if(this.currentPage + maxEdgeLength - beforeCurrentNums < num){
                        break;
                    }

                    if(response.length === maxEdgeLength + 1) {
                        response.shift()
                    }
                    response.push(num);
                }
            }

            return response;
        },
    },
}

</script>

<style lang="scss" scoped>
@import "@scss/abstract/variables";

.pagination{
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;

    &__item{
        cursor: pointer;
        margin: 5px 10px 0 10px;
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
