<template>

    <div class="container">
        <menu class="menu" role="tablist">
            <li v-for="menuRoute in menuList" class="menu-item">
                <router-link :to="menuRoute.path"
                     :class="{'active': $route.name === menuRoute.name}"
                     class="menu-item__link"
                >
                    {{ menuRoute.meta.title }}
                </router-link>
            </li>
        </menu>
    </div>

</template>

<script>
export default {
    name: "TabMenu",
    data() {
        return {
            menuList: []
        }
    },
    mounted() {
        this.$router.options.routes.map( route => {
            if (route?.meta?.userTabMenuItem === true) {
                this.menuList.push(route);
            }
        });
    }
}
</script>

<style lang="scss" scoped>

.menu{
    padding: 0;
    margin: 10px 0 0 0;
    display: flex;
    flex-direction: row;
    list-style: none;
    overflow-x: scroll;
}

.menu-item{
    display: block;
    margin-bottom: -1px;
    padding: 0.5em 1em;

    &__link{
        white-space:nowrap;
        padding-bottom: 2px;

        &.active{
            border-bottom: 2px solid #000000;
        }
    }
}

</style>
