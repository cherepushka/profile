<template>
    <header>
        <div class="minimized">
            <div class="container minimized__wrapper">
                <div class="wrapper">
                    <div class="left-icons">
                        <img alt="" class="hamburger-menu left-icons__img" @click="toggleMobileMenu"
                             ref="mobileMenuToggleBtn"
                             id="menu-mobile-hamburger" src="/assets/svg/hamburger-menu.svg">
                        <img alt="" class="left-icons__img" id="show-unminimized-header"
                             src="/assets/svg/show-unminimized-header.svg">
                    </div>

                    <a href="/public">
                        <img alt="" id="logo-minimized" src="/assets/svg/logo-minimized.svg">
                    </a>

                    <mobile-header-menu class="menu-mobile-inner-fixed" :menu-items="menuList" :is-mobile-menu-shown="isMobileMenuShown"></mobile-header-menu>

                </div>
            </div>
        </div>
        <div class="fullsize">
            <div class="top">
                <div class="container">
                    <div class="wrapper">
                        <div class="top-nav">
                            <i class="fa fa-bars hamburger-menu" aria-hidden="true"></i>
                            <ul class="fullsize__menu">
                                <li>
                                    <a href="/public">Продукция</a>
                                </li>
                                <li>
                                    <a href="/news">Новости</a>
                                </li>
                                <li class="about" @click="toggleAboutInfo($refs.aboutCompanyMenuList)">
                                    О компании
                                    <img alt="Стрелочка вниз" src="/assets/svg/top-caret-down.svg">
                                    <ul class="sub-ul" ref="aboutCompanyMenuList">
                                        <li>
                                            <a href="/about/">О компании</a>
                                        </li>
                                        <li>
                                            <a href="/certificate/">Сертификаты</a>
                                        </li>
                                        <li>
                                            <a href="/rekvisity/">Реквизиты</a>
                                        </li>
                                        <li>
                                            <a href="/diler/">Региональные представительства</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="/contacts/">Контакты</a>
                                </li>
                                <li class="about sprav" @click="toggleAboutInfo($refs.referenceInformationMenuList)">
                                    Справочная информация
                                    <img src="/assets/svg/top-caret-down.svg" alt="Стрелочка вниз">
                                    <ul class="sub-ul" ref="referenceInformationMenuList">
                                        <li><a href="/edinica/">Перевод единиц измерения</a></li>
                                        <li><a href="/flowcalc/">Калькулятор значения CV и расхода</a></li>
                                        <li><a href="/koroziya/">Таблица коррозийной стойкости</a></li>
                                        <li><a href="/types-of-threads/">Типы резьб</a></li>
                                        <li><a href="/dostavka-i-oplata/">Доставка и оплата</a></li>
                                        <li><a href="/internet-magazin/">Инструкция по оформлению заказов</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>

                        <div class="top-right-block">
                            <geo-phone-change></geo-phone-change>
                        </div>
                    </div>
                </div>
            </div>
            <div class="logo">
                <div class="container">
                    <a href="/public">
                        <img src="/assets/svg/fl-logo.svg" alt="Логотип Fluid-Line" id="logo-img">
                    </a>
                </div>
            </div>
            <nav>
                <div class="container">
                    <div class="wrapper">
                        <ul class="navigation-list">
                            <li data-container="product-nav">
                                <a href="/public" style="text-decoration: none;" target="_blank">
                                    Продукция
                                </a>
                            </li>
                            <li data-container="catalog-order">
                                <a href="/public?show-catalog=1" style="text-decoration: none;" target="_blank">
                                    Заказать доставку каталогов
                                </a>
                            </li>
                            <li data-container="presentation-order">
                                <a href="/public#zakazat_prezentatciiu" style="text-decoration: none;" target="_blank">
                                    Заказать презентацию
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
</template>

<script>
import MobileHeaderMenu from "./modules/Menu/MobileHeaderMenu";
import GeoPhoneChange from "./modules/GeoPhoneChange";

export default {
    name: "Header",
    components: {
        GeoPhoneChange,
        MobileHeaderMenu,
    },
    data() {
        return {
            isMinimizedCitiesListShown: false,
            isFullsizeCitiesListShown: false,
            isTabletMenuShown: false,
            isMobileMenuShown: false,
            activePhone: '',
            menuList: [
                {
                    title: 'Продукция',
                    href: '/public',
                },
                {
                    title: 'Новости',
                    href: '/news',
                },
                {
                    title: 'О компании',
                    children: [
                        {
                            title: 'О компании',
                            href: '/about'
                        },
                        {
                            title: 'Сертификаты',
                            href: '/certificate'
                        },
                        {
                            title: 'Реквизиты',
                            href: '/rekvisity'
                        },
                        {
                            title: 'Региональные представительства',
                            href: '/diler'
                        },
                    ]
                },
                {
                    title: 'Контакты',
                    href: '/contacts',
                },
                {
                    title: 'Справочная информация',
                    children: [
                        {
                            title: 'Перевод единиц измерения',
                            href: '/edinica'
                        },
                        {
                            title: 'Калькулятор значения CV и расхода',
                            href: '/flowcalc'
                        },
                        {
                            title: 'Таблица коррозийной стойкости',
                            href: '/koroziya'
                        },
                        {
                            title: 'Типы резьб',
                            href: '/types-of-threads'
                        },
                        {
                            title: 'Доставка и оплата',
                            href: '/dostavka-i-oplata'
                        },
                        {
                            title: 'Инструкция по оформлению заказов',
                            href: '/internet-magazin'
                        },
                    ]
                },
            ]
        }
    },
    mounted() {
        document.addEventListener('click', e => {
            if (e.target.closest('.cities') === null && e.target.closest('.region-button') === null) {
                this.isMinimizedCitiesListShown = false;
                this.isFullsizeCitiesListShown = false;
            }

            if (e.target.closest('.sub-ul') === null && e.target.closest('li.about') === null){
                this.$el.querySelectorAll('.sub-ul').forEach(list => {
                    list.style.display = 'none';
                    list.classList.remove('visible');
                })
            }

            // if (e.target.closest('.mobile-menu-container-hamburger') === null && e.target.closest('#menu-mobile-hamburger') === null) {
                // $('.mobile-menu-container-hamburger').remove();
                // $('#menu-mobile-hamburger').removeClass('active');
            // }
        });
    },
    methods: {
        toggleAboutInfo(menuList) {
            //TODO поменять условие
            // if (menuList.closest('.mobile-menu-container')[0] !== undefined){
            //     return false;
            // }

            if (menuList.classList.contains('visible')) {
                menuList.style.display = 'none';
                menuList.classList.remove('visible')
            } else {
                this.$el.querySelectorAll('.sub-ul').forEach(list => {
                    list.style.display = 'none';
                    list.classList.remove('visible')
                });

                menuList.style.display = 'block';
                menuList.classList.add('visible');
            }
        },
        toggleMobileMenu() {
            this.isMobileMenuShown = !this.isMobileMenuShown
        }
    },
}
</script>

<style lang="scss" scoped>
@use 'resources/scss/abstract/variables.scss';

header{

    @media screen and (max-width: 720px) {
        padding-top: 60px;
    }

    @media screen and (max-width: 599px) {
        & #show-unminimized-header,
        & .geo {
            display: none;
        }
    }
}

.header-breadcrumbs{
    & .breadcrumbs-string{
        font-size: 15px;

        .current-resource{
            font-family: "OpenSans", sans-serif;
            color: #777d81;
        }
    }

    @media screen and (max-width: 599px){
        background-color: #f0f2f3;
        padding: 11px 0;
        margin-bottom: 18px;

        & .bread-crumbs,
        & .breadcrumbs-string {
            padding: 0;
            font-size: 12px;
            line-height: 1.33;
            color: #777d81;
            background-color: transparent;
        }

        & .bread-crumbs span.delimiter,
        & .breadcrumbs-string span.delimiter {
            font-size: 12px;
            line-height: 1.33;
            margin: 0 5px;
        }

        & .bread-crumbs .current-resource,
        & .breadcrumbs-string .current-resource {
            font-family: "OpenSans-Bold", sans-serif;
            color: #000;
        }
    }
}

.minimized {
    padding: 12px 0 10px;
    background-color: #ffffff;
    z-index: 4;
    display: none;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    border-bottom: 1px solid #d2d4d6;

    &__wrapper{
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
    }

    & .container{

        & .wrapper{
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
            position: relative;
        }
    }

    & .left-icons img{
        cursor: pointer;
        transform: none !important;

        &:first-child{
            margin-right: 17px;
        }
    }

    img#logo-minimized{
        position: absolute;
        top: -1px;
        left: calc(50% - 15px);
    }

    .mobile-menu-container-hamburger {
        position: absolute;
        padding: 12px 8px;
        background-color: #fff;
        -webkit-box-shadow: 0 0 20px 0 rgba(50, 50, 50, 0.3);
        box-shadow: 0 0 20px 0 rgba(50, 50, 50, 0.3);
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

        & li{
            float: none;
            font-family: "OpenSans", sans-serif;
            font-size: 12px;
            line-height: 1.33;
            color: #000;
            white-space: nowrap;

            &.about{
                margin: 8px 0 13px;
                padding-top: 15px;
                border-top: 1px solid #d2d4d6;
                font-size: 10px;
                line-height: 1.2;
                color: #777d81;

                & img{
                    display: none;
                }
            }

            &:not(:last-child){
                margin-bottom: 7px;
            }
        }

        & .sub-ul{
            display: block !important;
            padding: 13px 0 8px;
            border-bottom: 1px solid #d2d4d6;
            margin-bottom: 13px;
            position: static;
            background-color: transparent;
            -webkit-box-shadow: none;
            box-shadow: none;

            &:before{
                display: none;
            }

            & li:not(:last-child){
                margin-bottom: 16px;
            }
        }
    }

    @media screen and (max-width: 720px) {
        height: 60px;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        display: flex !important;

        & .left-icons img:first-child {
            margin-right: 25px;
        }

        img#logo-minimized {
            top: -4px;
        }
    }
}

.nav-modals {
    z-index: 3;
}

.top-right-block {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;

    & .profile{
        position: relative;

        & img{
            cursor: pointer;

            &.logout-img{
                border-radius: 100%;
                background: #43ef24;
                width: 15px;
                height: 15px;
                padding: 3px;
                -webkit-box-sizing: initial;
                box-sizing: initial;
            }
        }

        & .forms, .auth-error p, .loginMessage{
            position: absolute;
            top: calc(100% + 10px);
            width: 200px;
            left: -155px;
            display: none;
            padding: 12px 8px;
            background-color: #fff;
            -webkit-box-shadow: 0 0 20px 0 rgba(50, 50, 50, 0.3);
            box-shadow: 0 0 20px 0 rgba(50, 50, 50, 0.3);
            z-index: 2;
        }

        & .forms:before, & .auth-error p:before, .loginMessage:before{
            display: block;
            content: '';
            position: absolute;
            border: 8px solid transparent;
            border-bottom: 6px solid #FFF;
            border-bottom: 6px solid #FFF;
            top: -14px;
            right: 30px;
        }

        & .forms input,
        & .forms button,
        & .auth-error p input,
        & .auth-error p button,
        & .loginMessage input,
        & .loginMessage button{
            display: block;
            width: 100%;
            height: 30px;
            border-radius: 5px;
            border: 1px solid rgba(50, 50, 50, 0.3);
            margin: 0 0 10px;
            font-size: 12px;
            text-indent: 10px;
            font-family: "OpenSans", sans-serif;
        }

        & .forms button,
        & .forms input[type=submit],
        & .auth-error p button,
        & .auth-error p input[type=submit],
        & .loginMessage button,
        & .loginMessage input[type=submit]{
            border: none;
            text-indent: 0;
            font-family: "OpenSans-Bold", sans-serif;
            background-color: #0096bb;
            color: #FFF;
        }

        & .forms p.error,
        & .auth-error p p.error,
        & .loginMessage p.error {
            font-size: 13px;
            text-align: center;
            color: #d81300;
        }

        & .forms .user.username,
        & .auth-error p .user.username,
        & .loginMessage .user.username {
            font-size: 12px;
            font-family: "OpenSans", sans-serif;
        }

        & .forms .user.username strong,
        & .auth-error p .user.username strong,
        & .loginMessage .user.username strong {
            font-family: "OpenSans-Bold", sans-serif;
            color: #0096bb;
        }

        & .forms .logout-form .inputs,
        & .auth-error p .logout-form .inputs,
        & .loginMessage .logout-form .inputs {
            display: none;
        }

        & .forms .logout-form label,
        & .auth-error p .logout-form label,
        & .loginMessage .logout-form label {
            font-size: 11px;
            font-family: "IBMPlexSans-Light", sans-serif;
            display: block;
            cursor: pointer;
            line-height: 1.3;
        }


        & .forms .logout-form #change-password,
        & .auth-error p .logout-form #change-password,
        & .loginMessage .logout-form #change-password {
            cursor: pointer;
        }

        & .forms .loginFPForm,
        & .auth-error p .loginFPForm,
        & .loginMessage .loginFPForm {
            display: none;
        }

        & .forms .loginFPForm .loginFPFieldset,
        & .auth-error p .loginFPForm .loginFPFieldset,
        & .loginMessage .loginFPForm .loginFPFieldset {
            padding: 0;
            border: none;
        }

        & .forms input[type=submit],
        & .auth-error p input[type=submit],
        & .loginMessage input[type=submit] {
            cursor: pointer;
        }

        & .auth-error p,
        & .loginMessage {
            display: block;
            color: red;
            cursor: pointer;
            margin: 0;
            text-align: center;
            font-size: 14px;
        }

        & .loginMessage {
            color: green;
        }

        & .login-options {
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
            margin-top: 15px;
        }

        & .login-options a {
            cursor: pointer;
            font-size: 11px;
            color: dodgerblue !important;
        }

        & .user-data {
            font-size: 11px;
        }

    }
}

.top{
    padding: 10px 0;
    background-color: #ffffff;
    position: relative;

    & .wrapper{
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

    .top-nav{

        & .hamburger-menu{
            display: none;
        }

        & #menu-mobile{
            position: relative;
        }

        & .mobile-menu-container{
            position: absolute;
            padding: 12px 8px;
            background-color: #fff;
            -webkit-box-shadow: 0 0 20px 0 rgba(50, 50, 50, 0.3);
            box-shadow: 0 0 20px 0 rgba(50, 50, 50, 0.3);
            display: none;
            top: 25px;
            left: 30px;
            z-index: 3;

            &:before{
                display: block;
                content: '';
                position: absolute;
                border: 8px solid transparent;
                border-bottom: 6px solid #FFF;
                top: -14px;
                left: 10px;
            }

            &.mobile-menu-container-hamburger{
                position: absolute;
                left: 0;
                top: 0;
            }

            & li{
                float: none;
                font-family: "OpenSans", sans-serif;
                font-size: 12px;
                line-height: 1.33;
                color: #000;
                white-space: nowrap;

                &.about{
                    margin: 8px 0 13px;
                    padding-top: 15px;
                    border-top: 1px solid #d2d4d6;
                    font-size: 10px;
                    line-height: 1.2;
                    color: #777d81;

                    & img{
                        display: none;
                    }
                }

                &:not(:last-child){
                    margin-bottom: 7px;
                }
            }

            & .sub-ul{
                padding: 13px 0 8px;
                border-bottom: 1px solid #d2d4d6;
                margin-bottom: 13px;
                display: block;
                position: static;
                background-color: transparent;
                -webkit-box-shadow: none;
                box-shadow: none;

                &:before{
                    display: none;
                }

                & li:not(:last-child){
                    margin-bottom: 16px;
                }
            }
        }

        & ul:after {
            display: block;
            content: '';
            clear: both;
        }

        & ul li {
            float: left;
            cursor: pointer;
            margin-right: 12px;
            line-height: 1.38;
            letter-spacing: normal;
            font-size: 13px;

            &#menu-mobile {
                display: none;
            }

            & a{
                text-decoration: none;
                color: #1d2e39;

                &:hover{
                    color: #000;
                }
            }

            & img {
                margin-left: 4px;
            }

            &.about{
                position: relative;
            }

            & .sub-ul{
                list-style: none;
                position: absolute;
                top: calc(100% + 10px);
                left: calc(-50% + 14px);
                display: none;
                padding: 12px 8px;
                background-color: #fff;
                -webkit-box-shadow: 0 0 20px 0 rgba(50, 50, 50, 0.3);
                box-shadow: 0 0 20px 0 rgba(50, 50, 50, 0.3);

                &:before{
                    display: block;
                    content: '';
                    position: absolute;
                    border: 8px solid transparent;
                    border-bottom: 6px solid #FFF;
                    top: -14px;
                    left: calc(50% - 3px);
                }

                & li{
                    float: none;
                    font-family: "OpenSans", sans-serif;
                    font-size: 12px;
                    line-height: 1.33;
                    color: #000;
                    white-space: nowrap;

                    &:not(:last-child){
                        margin-bottom: 7px;
                    }
                }
            }
        }
    }

    @media screen and (max-width: 999px) {
        & .wrapper {
            display: block;
            position: relative;
        }

        & .top-nav ul li#menu-mobile {
            display: block;
        }

        & .minimized img#logo-minimized {
            top: -3px;
        }

        & .top-right-block {
            position: absolute;
            top: 0;
            right: 0;
        }
    }
}

.logo {
    text-align: center;
    padding: 47px 0 23px;
    background-color: #f0f0f2;

    @media screen and (max-width: 999px) {
        padding-top: 23px;
    }
}

nav {
    text-align: center;
    padding-bottom: 20px;
    background-color: #f0f0f2;

    & li{
        display: inline;
        font-family: "IBMPlexSans-Light", sans-serif;
        font-size: 20px;
        line-height: 1;
        color: #1d2e39;
        margin: 0 calc(25px / 2) 2px;
        padding: 15px 0 11px;
        cursor: pointer;

        &.current{
            border-bottom: 2px solid #000;
        }

        &:hover{
            color: #000;
        }
    }

    @media screen and (max-width: 999px) {
        & li{
            font-size: 18px;
        }
    }

    @media screen and (max-width: 999px) and (max-width: 670px) {
        & li {
            font-size: 15px;
        }
    }
}

.navigation-list{
    padding: 0;
    margin: 0;
}

.container {
    padding-top: 0;
}

.product-nav {
    position: absolute;
    left: 0;
    display: none;
    width: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    text-align: center;
    overflow: auto;

    & span[data-id='categories'] {
        display: none;
    }

    & .title {
        font-family: "OpenSans-Bold", sans-serif;
        font-size: 12px;
        line-height: 1.33;
        color: #000;
        margin-bottom: 15px;
    }

    & .products-menu {
        text-align: left;
        display: inline-block;
        background-color: #fff;
        padding: 25px 40px 100px;
    }

    & .products-menu:after {
        display: block;
        content: '';
        clear: both;
    }

    & .clearfix:after {
        display: block;
        content: '';
        clear: both;
    }

    & .ul {
        width: 260px;
        max-width: 33.3%;
        float: left;
        height: 100%;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    & .ul.sub-1 {
        border-left: 1px solid #e3e4e5;
        border-right: 1px solid #e3e4e5;
    }

    & .navigation, & .categories {
        float: left;
    }

    & .navigation {
        max-width: 75%;
    }

    & li {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        padding: 9px 32px 9px 10px;
        background: url("../img/product-nav-item-arr.svg") calc(100% - 15px - 6px) center no-repeat;
        font-family: "OpenSans", sans-serif;
        font-size: 12px;
        line-height: 1.33;
        color: #000;
    }

    & li.empty-catalog {
        background-image: none !important;
    }

    & li:hover, & li.hover {
        background-image: url("../img/product-nav-item-arr-hover.svg");
        background-color: #f4f4f4;
    }

    & li:active {
        color: #0086a7;
    }

    & li a {
        display: block;
    }

    & .ul.sub-2 li {
        background-image: none;
    }

    & .product-nav-subcatalog {
        display: none;
    }

    & .categories {
        max-width: 25%;
    }

    & .categories li {
        background-image: none;
    }

    @media screen and (max-width: 999px) {
        & span[data-id='categories'] {
            display: inline;
        }

        & .products-menu {
            padding-top: 0;
            display: block;
        }

        & .navigation {
            max-width: 100%;
            width: 100%;
            float: none;
        }

        & .navigation .title {
            text-align: center;
            padding: 13px 0 22px;
            font-family: "IBMPlexSans", sans-serif;
            font-size: 15px;
            line-height: 1.73;
            color: #1d2e39;
        }

        & .navigation .title span {
            padding-bottom: 1px;
        }

        & .navigation .title span:first-child {
            margin-right: 30px;
        }

        & .navigation .title span.current-span {
            border-bottom: 2px solid #1d2e39;
        }

        & li.force-direct {
            list-style-type: none;
            border-bottom: 1px solid #1d2e39;
            margin-bottom: 13px;
            background-image: none;
        }

        & .categories {
            display: none;
            width: 100%;
            max-width: 100%;
            float: none;
        }
    }
}

.fullsize{
    &__menu{
        padding: 0;
        margin: 0;
        list-style-type: none;
    }

    @media screen and (max-width: 720px) {
        & .top,
        & .logo,
        & nav,
        & > .container {
            display: none;
        }
    }
}

.menu-mobile-inner-fixed{
    top: 60px;
}


</style>
