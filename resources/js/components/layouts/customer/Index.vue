<template>
    <div>
        <Header></Header>

        <tab-menu v-if="authorized"></tab-menu>

        <main>
            <router-view></router-view>
        </main>

        <Footer></Footer>
    </div>
</template>

<script>
import Header from "./Header";
import Footer from "./Footer";
import TabMenu from "./TabMenu";
import {useUserStorage} from "../../../storage/pinia/userStorage"
import {mapState} from 'pinia'
export default {
    name: "Index",
    components: {
        TabMenu,
        Footer,
        Header
    },
    computed: {
        ...mapState(useUserStorage, ['authorized'])
    }
}
</script>

<style lang="scss" scoped>

main{
    padding: 0;
    min-height: 80vh;
    box-sizing: border-box;

    @media screen and (max-width: 720px){
        margin-top: 60px;
    }
}

</style>
