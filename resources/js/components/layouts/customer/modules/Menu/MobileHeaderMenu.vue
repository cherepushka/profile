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

                    <ul v-if="typeof menuItem.children !== 'undefined'">
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

    &.reversed {
        left: initial;
        right: 0;

        &:after{
            left: initial;
            right: 100%;
        }
    }

    & .menu-top {
        background-color: #f0f0f2;
    }

    & .menu-top ul {
        padding: 0;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: start;
        -ms-flex-pack: start;
        justify-content: flex-start;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }

    & .menu-top li {
        border-bottom: none;
        font-family: "IBMPlexSans", sans-serif;
        font-size: 15px;
        line-height: 1.73;
        text-align: center;
        color: #1d2e39;
        padding: 12px;
    }

    & .menu-top li.current {
        color: #3657ab;
        position: relative;
        background-color: #fff;
    }

    & .menu-top li.current:before {
        display: block;
        content: '';
        position: absolute;
        bottom: 0;
        width: calc(100% - 24px);
        height: 2px;
        background-color: #0096bb;
    }

    & ul {
        padding: 0 12px;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    & ul li {
        padding: 16px 0;
        font-family: "OpenSans", sans-serif;
        font-size: 13px;
        line-height: 1.38;
        color: #000000;
        border-bottom: 1px solid #edeeef;
    }

    & ul li.link-li {
        font-family: "OpenSans-Bold", sans-serif;
    }

    & ul li ul {
        display: none;
    }

    & .menu-about li:nth-child(2) {
        display: none;
    }

    & .menu-contacts {
        padding: 15px 12px 25px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: reverse;
        -ms-flex-direction: column-reverse;
        flex-direction: column-reverse;
    }

    & .menu-contacts .right-top {
        position: relative;
        text-align: center;
        font-family: "IBMPlexSans", sans-serif;
        font-size: 15px;
        line-height: 1.6;
        letter-spacing: -0.1px;
        color: #1d2e39;
        margin-bottom: 26px;
    }

    & .menu-contacts .right-top .region-button {
        height: 30px;
        margin: 0 10px;
        background-color: #edeeef;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        font-size: 10px;
    }

    & .menu-contacts .left-top {
        font-family: "OpenSans", sans-serif;
        font-size: 13px;
        line-height: 1.38;
        color: #000000;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }

    & .menu-contacts .left-top .icons a {
        margin-left: 10px;
    }

    & .menu-contacts-info {
        padding: 0 12px;
    }

    & .menu-contacts-info table {
        padding-top: 20px;
        border-top: 1px solid #edeeef;
        display: block;
    }

    & .menu-contacts-info table * {
        display: block;
    }

    & .menu-contacts-info table td {
        font-family: "OpenSans", sans-serif;
        font-size: 13px;
        line-height: 1.5;
        color: #7e8791;
    }

    & .menu-contacts-info table td:last-child {
        font-family: "OpenSans-Bold", sans-serif;
        color: #000000;
        padding-bottom: 20px;
    }

    & .menu-contacts-info table td a {
        text-decoration: underline;
    }

    & .info .text {
        font-size: 13px;
        margin-bottom: 20px;
    }

    & #close-menu-btn {
        margin-top: 40px;
        width: 100%;
        height: 50px;
        background-color: #edeeef;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        font-family: "OpenSans", sans-serif;
        font-size: 13px;
        line-height: 1.38;
        color: #1d2e39;
    }

    & #close-menu-btn img {
        margin-right: 10px;
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
