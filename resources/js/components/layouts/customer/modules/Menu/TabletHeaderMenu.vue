<template>
    <ul class="tablet-menu-container-hamburger">
        <li v-for="(menuItem) in menuItems" :class="{'with-children': typeof menuItem.children !== 'undefined'}">
            <template v-if="typeof menuItem.children === 'undefined'">
                <a :href="menuItem.href">
                    {{ menuItem.title }}
                </a>
            </template>

            <template v-else>
                {{ menuItem.title }}
                <ul class="sub-ul">
                    <li v-for="childItem in menuItem.children">
                        <a :href="childItem.href">{{ childItem.title }}</a>
                    </li>
                </ul>
            </template>
        </li>
    </ul>
</template>

<script>
export default {
    name: "TabletHeaderMenu",
    props: {
        menuItems: {
            type: Array,
            default: []
        }
    }
}
</script>

<style lang="scss" scoped>

.tablet-menu-container-hamburger {
    position: absolute;
    padding: 12px 8px;
    background-color: #fff;
    -webkit-box-shadow: 0 0 20px 0 rgb(50 50 50 / 30%);
    box-shadow: 0 0 20px 0 rgb(50 50 50 / 30%);
    top: 120%;
    left: -10px;

    &:before{
        display: block;
        content: '';
        position: absolute;
        border: 8px solid transparent;
        border-bottom: 6px solid #FFF;
        top: -14px;
        left: 10px;
    }

    li{
        float: none;
        font-family: "OpenSans", sans-serif;
        font-size: 12px;
        line-height: 1.33;
        color: #000;
        white-space: nowrap;
        margin-bottom: 10px;

        &.with-children{
            margin: 8px 0 13px;
            padding-top: 15px;
            border-top: 1px solid #d2d4d6;
            border-bottom: 1px solid #d2d4d6;
            font-size: 10px;
            line-height: 1.2;
            color: #777d81;
        }
    }
}

.sub-ul{
    padding-top: 5px;
}

</style>
