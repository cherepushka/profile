<template>

    <div v-if="isMobileMenuShown" class="menu-mobile-inner-fixed" ref="mobileMenu">
        <div class="menu-overflow-content">

            <template v-for="(menuItem) in menuItems">
                <h2>
                    <a v-if="typeof menuItem.href !== 'undefined'" :href="menuItem.href" class="menu-link-underlined">
                        {{ menuItem.title }}
                    </a>

                    <template v-else>
                        {{ menuItem.title }}
                    </template>

                    <ul v-if="typeof menuItem.children !== 'undefined'" class="children-list">
                        <li v-for="(menuChild) in menuItem.children">
                            <a :href="menuChild.href">
                                {{ menuChild.title }}
                            </a>
                        </li>
                    </ul>
                </h2>
            </template>

            <geo-phone-change class="phone-geo"></geo-phone-change>
        </div>
    </div>

</template>

<script>
"use strict";

import GeoPhoneChange from "../GeoPhoneChange";

export default {
    name: "MobileHeaderMenu",
    components: {GeoPhoneChange},
    props: {
        isMobileMenuShown: {
            type: Boolean,
            default: false,
        },
        menuItems: {
            type: Array,
            default: []
        }
    },
    watch: {
        isMobileMenuShown() {
            if (this.isMobileMenuShown) {
                document.querySelector('body').style.overflow = 'hidden';
            } else {
                document.querySelector('body').style.overflow = 'initial';
            }
        }
    },
}

</script>

<style lang="scss" scoped>

.menu-mobile-inner-fixed {
    position: fixed;
    left: 0;
    z-index: 2;
    font-family: "OpenSans", sans-serif;
    font-size: 13px;
    line-height: 1.38;
    color: #000000;
    display: block;
    content: "";
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.7);
}

.children-list{
    padding: 0 12px;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;

    & li {
        list-style: none;
        padding: 10px 0;
        font-size: 13px;
        color: #000000;
        border-bottom: 1px solid #edeeef;
    }
}

.menu-overflow-content {
    padding: 20px;
    background-color: #fff;
    width: 330px;
    height: calc(100% - 60px);
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    scrollbar-width: thin;
    overflow: auto;

    &::-webkit-scrollbar{
        height: 11px;
        width: 11px;
        -webkit-appearance: none;
    }

    &::-webkit-scrollbar-thumb{
        background-color: rgba(47, 46, 46, 0.2);
        border: 3px solid #fbf6f6;
        border-radius: 8px;
    }
}

.menu-link-underlined{
    text-decoration: underline;
}

.phone-geo{
    display: flex;
    justify-content: center;
    align-items: center;
}

</style>
