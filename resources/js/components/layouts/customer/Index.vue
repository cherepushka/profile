<template>
    <div>
        <Header></Header>

        <tab-menu v-if="isAuthorized"></tab-menu>
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

export default {
    name: "Index",
    components: {
        TabMenu,
        Footer,
        Header
    },
    data() {
        return {
            isAuthorized: false,
        }
    },
    mounted() {
        this.isAuthorized = true;
    }
}
</script>

<style lang="scss" scoped>

main{
    padding: 0;
    min-height: 50vh;
    box-sizing: border-box;

    @media screen and (max-width: 720px){
        margin-top: 60px;
    }
}

</style>
