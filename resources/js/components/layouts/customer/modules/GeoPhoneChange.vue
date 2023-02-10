<template>

    <div class="geo" ref="citiesListWrapper">
        <button type="button" class="region-button" ref="regionButton" @click="toggleCitiesList">
            <span class="city-name">
                {{ selectedCity }}
            </span>
            <img src="/assets/svg/top-caret-down.svg" alt="Стрелочка вниз">
        </button>

        <div class="cities" ref="citiesList"
             :style="{top: citiesListStyles.top + 'px',
             left: citiesListStyles.left + 'px',
             display: isCitiesListShown === false ? 'none' : 'flex'
        }">
            <div v-for="(cityBLock, index) in citiesBlocks" :key="index">

                <a data-tel="" class="cities__item-link" v-for="(city) in cityBLock"
                   :style="{'font-weight': city === activeCity ? 'bold' : 'normal'}"
                   @click.prevent="changePhone"
                >
                    {{ city }}
                </a>
            </div>
        </div>

        <span class=main-phone>
            <a href="tel:{{ selectedPhone }}">{{ selectedPhone }}</a>
        </span>

    </div>

</template>

<script>
import {calculateScrollBarWidth} from "../../../../utils/functions/cssHelpers";

export default {
    name: "GeoPhoneChange",
    data() {
        return {
            isCitiesListShown: false,
            citiesListWidth: 0,
            citiesListHeight: 0,
            selectedCity: 'Москва',
            selectedPhone: '+74959844100',
            citiesListStyles: {
                top: 0,
                left: 0,
            },
            activeCity: 'Москва',
            citiesBlocks: [
                ["Астрахань", "Барнаул", "Вологда", "Воронеж", "Екатеринбург", "Иваново", "Ижевск", "Казань", "Калининград", "Калуга", "Кемерово", "Киров", "Краснодар", "Красноярск", "Курск", "Липецк", "Магнитогорск", "Москва", "Мурманск", "Набережные челны", "Нижний Новгород", "Новокузнецк", "Новосибирск"],
                ["Омск", "Орел", "Оренбург", "Пенза", "Пермь", "Ростов-на-Дону", "Рязань", "Самара", "Санкт-Петербург", "Саратов", "Смоленск", "Сочи", "Ставрополь", "Сургут", "Тверь", "Томск", "Тула", "Тюмень", "Ульяновск", "Уфа", "Хабаровск", "Челябинск", "Череповец", "Ярославль"],
                ["Алматы", "Нур-Султан", "Караганда", "Киев", "Днепропетровск", "Харьков", "Минск"]
            ],
        }
    },
    mounted() {
        if (this.citiesBlocks.length === 0) {
            //TODO вставить вызов API
        }
    },
    methods: {
        async toggleCitiesList() {
            //if list is already shown, hide it
            if (this.isCitiesListShown === true){
                this.isCitiesListShown = false;
                return;
            }

            await this.calcCitiesListBox();

            const wrapperRect = this.$refs.citiesListWrapper.getBoundingClientRect();

            this.setCitiesListTopStyles(wrapperRect);
            this.setCitiesListLeftStyles(wrapperRect);

            this.isCitiesListShown = true;
        },
        setCitiesListLeftStyles(wrapperRect){
            const halfBtnWidth = wrapperRect.width / 2;
            const halfCitiesListWidth = this.citiesListWidth / 2;

            //with this `left` offset the cities list will be center aligned with the button
            const desiredIndent = -1 * (halfCitiesListWidth - halfBtnWidth);

            const screenLeftBtnCenterOffset = wrapperRect.left + halfBtnWidth;
            const screenRightBtnCenterOffset = window.innerWidth - wrapperRect.right + halfBtnWidth;

            if (screenRightBtnCenterOffset - halfCitiesListWidth < 0) {
                //right side align
                const bonusOffset = screenRightBtnCenterOffset - halfCitiesListWidth - calculateScrollBarWidth();
                this.citiesListStyles.left = desiredIndent + bonusOffset;
            }
            else if(screenLeftBtnCenterOffset - halfCitiesListWidth < 0) {
                //left side align
                this.citiesListStyles.left = -1 * wrapperRect.left;
            }
            else {
                //center align
                this.citiesListStyles.left = desiredIndent;
            }
        },
        setCitiesListTopStyles(wrapperRect){
            const scrolledPixels = window.scrollY;

            const topReservedPixels = wrapperRect.top + scrolledPixels;

            if (topReservedPixels - this.citiesListHeight > 0) {
                this.citiesListStyles.top = -1 * this.citiesListHeight;
            } else {
                //if displaying on bottom
                this.citiesListStyles.top = wrapperRect.height;
            }
        },
        async calcCitiesListBox() {
            this.$refs.citiesList.style.visibility = 'hidden';
            this.isCitiesListShown = true;

            await this.$nextTick();

            const citiesListRect = this.$refs.citiesList.getBoundingClientRect();
            this.citiesListWidth = citiesListRect.right - citiesListRect.left;
            this.citiesListHeight = citiesListRect.bottom - citiesListRect.top;

            this.isCitiesListShown = false;
            this.$refs.citiesList.style.visibility = 'visible';

            await this.$nextTick();
        },
        changePhone(event) {
            //TODO оживить
            //     let city = this.innerHTML;
            //
            //     $this.ajax('/ajax/geo/novinkaBase.php', 'json', 'city=' + $(this).text(), function (response) {
            //         if (response.length > 0) {
            //             $('.city-name').text(city);
            //             $('.main-phone, .phone-number').html(`<a href="tel:${response[2].replace(/[^\d]/gi, '')}">${response[2]}</a>`);
            //             $('.cities').remove();
            //             // document.cookie = "city=" + city;
            //             // document.cookie = "phone=" + response[2];
            //         }
            //     });
            //     return false;
        }
    }
}
</script>

<style lang="scss" scoped>

.geo {
    position: relative;

    & .region-button{
        padding: 6px 10px;
        font-size: 10px;
        line-height: 1.2;
        color: #000;
        background-color: #edeeef;
        border: none;

        &:hover{
            background-color: #d0d0d0;
        }

        & img{
            margin-left: 4px;
        }
    }

    & .main-phone{
        line-height: 1;
        margin: 0 20px 0 10px;
        font-family: "OpenSans-Bold", sans-serif;
        font-size: 12px;
    }
}

.cities {
    display: flex;
    justify-content: space-between;
    flex-wrap: nowrap;
    align-items: flex-start;
    position: absolute;
    padding: 20px;
    z-index: 1000;
    background-color: #FFFFFF;
    box-shadow: 0 0 20px 0 rgba(50, 50, 50, 0.3);

    & > div{
        width: 120px;
    }

    & > div:not(:last-child) {
        margin-right: 20px;
        padding-right: 20px;
        border-right: 1px solid #f3eaea;
    }

    &__item-link {
        cursor: pointer;
        display: block;
        font-size: 12px;
        color: #000;
        text-align: center;
        font-family: "IBMPlexSans", sans-serif;
        line-height: 1.6;
        letter-spacing: -0.1px;

        &:hover {
            color: #0096bb;
        }
    }

    @media screen and (max-width: 720px) {
        max-width: 100%;
        left: 0;
        padding: 10px;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;

        & > div:not(:last-child) {
            margin-right: 10px;
            padding-right: 10px;
        }

        & a {
            font-size: 11px;
        }
    }

}

</style>
