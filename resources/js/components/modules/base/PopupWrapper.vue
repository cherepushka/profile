<template>
    <div class="popup-overlay" @click="closePopup">
        <div class="popup" @click.stop>
            <div class="popup__content">

                <div class="popup__close" @click="closePopup">
                    <svg width="15" height="15" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2L25 25M2 25L25 2" stroke="black" stroke-width="5"/>
                    </svg>
                </div>

                <div class="body">
                    <slot></slot>
                </div>

            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "PopupWrapper",
    emits: ['closePopup'],
    mounted() {
        document.querySelector('body').style.overflow = 'hidden';
    },
    methods: {
        closePopup() {
            document.querySelector('body').style.overflow = 'auto';
            this.$emit('closePopup');
        }
    },
    unmounted() {
        document.querySelector('body').style.overflow = 'auto';
    }
}
</script>

<style lang="scss" scoped>
.popup-overlay{
    transform: translate3d(0, 0, 25px);
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 1500;

    &.hidden{
        opacity: 0;
        z-index: -25;
    }
}

.popup{
    -webkit-overflow-scrolling: touch;
    overflow-y: scroll;
    max-height: 80vh;
    transform: translate(-50%, -50%);
    top: 50%;
    left: 50%;
    position: fixed;
    z-index: 1501;
    background-color: #FFFFFF;
    border-radius: 5px;

    &__content{
        position: relative;
    }

    &__close{
        cursor: pointer;
        position: absolute;
        top: 5px;
        right: 5px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 12px;
        height: 12px;
    }

    @media only screen and (min-width: 1140px) {
        width: 800px;
    }
    @media only screen and (max-width: 1139px) and (min-width: 960px) {
        width: 800px;
    }
    @media only screen and (max-width: 959px) and (min-width: 720px) {
        width: 720px;
    }
    @media only screen and (max-width: 719px) and (min-width: 540px) {
        width: 540px;
    }
    @media only screen and (max-width: 539px) {
        width: 100%;
    }
}

.body{
    padding: 20px 0;
}

</style>
