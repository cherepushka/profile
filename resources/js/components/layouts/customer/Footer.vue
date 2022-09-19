<template>

    <footer>
        <div class="top">
            <div class="left-top socials">
                <span>Мы доступны в:</span>
                <span class="icons" style="font-size: x-large;">

                    <a href="https://www.youtube.com/channel/UCsD72HATFzpKD0kJJPjF57A?sub_confirmation=1" target="_blank">
                        <youtube width="24px" height="24px" iconColor="#000000"></youtube>
                    </a>
                    <a href="https://vk.com/fluidline" target="_blank">
                        <VK width="24px" height="24px" iconColor="#000000"></VK>
                    </a>
                    <a href="https://www.linkedin.com/company/fluidline/" target="_blank">
                        <linked-in width="24px" height="24px" iconColor="#000000"></linked-in>
                    </a>
                    <a href="https://t.me/fluidline_ru/" target="_blank">
                        <telegram width="24px" height="24px" iconColor="#000000"></telegram>
                    </a>

                </span>
            </div>

            <geo-phone-change></geo-phone-change>

        </div>
        <div class="bottom">
            <div class="wrapper">
                <div class="logo">
                    <img src="/assets/svg/footer-logo.svg" alt="FL">
                    {{ (new Date()).getFullYear() }}
                    ООО "Флюид-лайн"
                </div>
                <div class="info">
                    <table>
                        <tr>
                            <td>Телефон</td>
                            <td>
                                <a href="tel:+74959844100">
                                    +7 495 984 41 00
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>
                                <a href="mailto:mail@fluid-line.ru">
                                    mail@fluid-line.ru
                                </a>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>Адрес</td>
                            <td>Москва, Большая Семеновская ул. д.49</td>
                        </tr>
                        <tr>
                            <td>Отгрузка</td>
                            <td>Москва, Семеновская ул. д.49</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </footer>

</template>

<script>
import Youtube from "../../icons/socials/Youtube";
import VK from "../../icons/socials/VK";
import LinkedIn from "../../icons/socials/LinkedIn";
import Telegram from "../../icons/socials/Telegram";
import GeoPhoneChange from "./modules/GeoPhoneChange";

export default {
    name: "Footer",
    components: {GeoPhoneChange, Telegram, VK, Youtube, LinkedIn},
    data() {
        return {
        }
    },
    mounted() {

    },
    methods: {

    },
}
</script>

<style lang="scss" scoped>

.top {
    border-top: 1px solid #d2d4d6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;

    & > div{
        width: 50%;
        height: 60px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }

    & .left-top {
        font-family: "OpenSans", sans-serif;
        font-size: 13px;
        line-height: 1.38;
        color: #000000;

        & span.icons{
            display: flex;

            & a {
                margin-left: 13px;
                display: flex;
                align-items: center;

                & img {
                    -webkit-transition: all .2s ease-in-out;
                    transition: all .2s ease-in-out;
                }
                & img:hover {
                    transform: scale(1.1);
                }
            }
        }
    }

    & .right-top {
        position: relative;
        text-align: center;
        font-family: "IBMPlexSans", sans-serif;
        font-size: 15px;
        line-height: 1.6;
        letter-spacing: -0.1px;
        color: #1d2e39;
        background-color: #f0f2f3;

        & .region-button {
            height: 30px;
            margin: 0 10px;
            border: 1px solid #000;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        & .cities {
            top: -400px;
            left: -23%;

            &:before{
                border: 8px solid transparent;
                border-left: 6px solid #FFF;
                top: 86%;
                left: 100%;
            }
        }
    }

    @media screen and (max-width: 599px) {
        -webkit-box-orient: vertical;
        -webkit-box-direction: reverse;
        -ms-flex-direction: column-reverse;
        flex-direction: column-reverse;

        & > div {
            width: 100%;
        }

        & .left-top {
            border-top: 1px solid #d2d4d6;
        }

        & .right-top .cities {
            left: 0;
            top: initial;
            bottom: 60px;
            width: 100%;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }
    }

    @media screen and (max-width: 599px) and (min-width: 400px) {
        & .right-top .cities {
            max-width: 360px;
            left: calc(50% - 180px);
        }
    }

    @media screen and (max-width: 599px) {
        & .right-top .cities:before {
            top: initial;
            bottom: -15px;
            left: 35%;
            transform: rotate(90deg);
        }
    }
}

footer address {
    font-style: normal;
}

.bottom {
    background-color: #1d2e39;
    padding: 20px 0 30px;
    font-family: "OpenSans", sans-serif;
    font-size: 12px;
    line-height: 1.33;
    color: #FFF;

    & .wrapper:after {
        display: block;
        content: '';
        clear: both;
    }

    & .logo {
        float: left;

        & img {
            display: block;
            margin-bottom: 10px;
        }
    }

    & .info {
        float: right;
    }

    & td:first-child {
        opacity: .7;
        padding-right: 26px;
    }

    & td a {
        text-decoration: underline;
    }

    @media screen and (max-width: 599px) {
        & {
            padding: 25px 0 30px;
        }

        & .logo, & .info {
            float: none;
            width: 100% !important;
        }

        & .logo {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            justify-content: center;
            text-align: center;
            margin-bottom: 28px;
        }

        & .logo img {
            margin: 0 10px 0 0;
        }

        & .info table * {
            display: block !important;
            font-size: 14px;
        }

        & .info tr:not(:last-child) {
            margin-bottom: 20px;
        }
    }
}


</style>
