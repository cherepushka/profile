<template>
    <header>
        <div class="minimized">
            <div class="container minimized__wrapper">
                <div class="wrapper">
                    
                    <div class="left-icons">
                        <img alt="" class="hamburger-menu left-icons__img" @click="toggleMobileMenu"
                            ref="mobileMenuToggleBtn"
                            id="menu-mobile-hamburger" src="/assets/svg/hamburger-menu.svg"
                        >
                        <img alt="" class="left-icons__img" id="show-unminimized-header"
                            src="/assets/svg/show-unminimized-header.svg"
                            @click="scrollToTop"
                        >
                    </div>

                    <a href="/">
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
                                    <a href="https://fluid-line.ru/" target="_blank">Продукция</a>
                                </li>
                                <li>
                                    <a href="https://fluid-line.ru/news" target="_blank">Новости</a>
                                </li>
                                <li class="about" @click="toggleAboutInfo($refs.aboutCompanyMenuList)">
                                    О компании
                                    <img alt="Стрелочка вниз" src="/assets/svg/top-caret-down.svg">
                                    <ul class="sub-ul" ref="aboutCompanyMenuList">
                                        <li>
                                            <a href="https://fluid-line.ru/about/" target="_blank">О компании</a>
                                        </li>
                                        <li>
                                            <a href="https://fluid-line.ru/certificate/" target="_blank">Сертификаты</a>
                                        </li>
                                        <li>
                                            <a href="https://fluid-line.ru/rekvisity/" target="_blank">Реквизиты</a>
                                        </li>
                                        <li>
                                            <a href="https://fluid-line.ru/diler/" target="_blank">Региональные представительства</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="https://fluid-line.ru/contacts/" target="_blank">Контакты</a>
                                </li>
                                <li class="about sprav" @click="toggleAboutInfo($refs.referenceInformationMenuList)">
                                    Справочная информация
                                    <img src="/assets/svg/top-caret-down.svg" alt="Стрелочка вниз">
                                    <ul class="sub-ul" ref="referenceInformationMenuList">
                                        <li><a href="https://fluid-line.ru/edinica/" target="_blank">Перевод единиц измерения</a></li>
                                        <li><a href="https://fluid-line.ru/flowcalc/" target="_blank">Калькулятор значения CV и расхода</a></li>
                                        <li><a href="https://fluid-line.ru/koroziya/" target="_blank">Таблица коррозийной стойкости</a></li>
                                        <li><a href="https://fluid-line.ru/types-of-threads/" target="_blank">Типы резьб</a></li>
                                        <li><a href="https://fluid-line.ru/dostavka-i-oplata/" target="_blank">Доставка и оплата</a></li>
                                        <li><a href="https://fluid-line.ru/internet-magazin/" target="_blank">Инструкция по оформлению заказов</a></li>
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
                                <a href="https://fluid-line.ru" style="text-decoration: none;" target="_blank">
                                    Продукция
                                </a>
                            </li>
                            <li data-container="catalog-order">
                                <a href="https://fluid-line.ru?show-catalog=1" style="text-decoration: none;" target="_blank">
                                    Заказать доставку каталогов
                                </a>
                            </li>
                            <li data-container="presentation-order">
                                <a href="https://fluid-line.ru#zakazat_prezentatciiu" style="text-decoration: none;" target="_blank">
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
            isMobileMenuShown: false,
            activePhone: '',
            menuList: [
                {
                    title: 'Продукция',
                    href: 'https://fluid-line.ru/public',
                },
                {
                    title: 'Новости',
                    href: 'https://fluid-line.ru/news',
                },
                {
                    title: 'О компании',
                    children: [
                        {
                            title: 'О компании',
                            href: 'https://fluid-line.ru/about'
                        },
                        {
                            title: 'Сертификаты',
                            href: 'https://fluid-line.ru/certificate'
                        },
                        {
                            title: 'Реквизиты',
                            href: 'https://fluid-line.ru/rekvisity'
                        },
                        {
                            title: 'Региональные представительства',
                            href: 'https://fluid-line.ru/diler'
                        },
                    ]
                },
                {
                    title: 'Контакты',
                    href: 'https://fluid-line.ru/contacts',
                },
                {
                    title: 'Справочная информация',
                    children: [
                        {
                            title: 'Перевод единиц измерения',
                            href: 'https://fluid-line.ru/edinica'
                        },
                        {
                            title: 'Калькулятор значения CV и расхода',
                            href: 'https://fluid-line.ru/flowcalc'
                        },
                        {
                            title: 'Таблица коррозийной стойкости',
                            href: 'https://fluid-line.ru/koroziya'
                        },
                        {
                            title: 'Типы резьб',
                            href: 'https://fluid-line.ru/types-of-threads'
                        },
                        {
                            title: 'Доставка и оплата',
                            href: 'https://fluid-line.ru/dostavka-i-oplata'
                        },
                        {
                            title: 'Инструкция по оформлению заказов',
                            href: 'https://fluid-line.ru/internet-magazin'
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
        });
    },
    methods: {
        toggleAboutInfo(menuList) {

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
        },
        scrollToTop() {
            window.scrollTo(0,0);
        },
    },
}
</script>

<style lang="scss" scoped>
@import '@scss/abstract/variables.scss';

header{

    @media screen and (max-width: $col-m-min) {
        padding-top: 60px;
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

    @media screen and (max-width: $col-m-min) {
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

.top-right-block {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
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

    & li:active {
        color: #0086a7;
    }

    & li a {
        display: block;
    }
}

.fullsize{
    &__menu{
        padding: 0;
        margin: 0;
        list-style-type: none;
    }

    @media screen and (max-width: $col-m-min) {
        display: none;
    }
}

.menu-mobile-inner-fixed{
    top: 60px;
}


</style>
