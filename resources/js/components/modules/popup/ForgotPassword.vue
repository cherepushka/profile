<template>
    <div class="content">
        <div class="content__top">
            <div class="title">Восстановление пароля</div>
        </div>

        <div class="content__bottom">
            <form class="recovery-form">
                <div class="recovery-form__item">
                    <input class="recovery-form__input input" type="email" placeholder="Введите свой email">
                </div>

                <div class="recovery-form__item">

                    <p class="recovery-form__description">
                        На указанный выше email будет отправлен новый сгенерированный
                        пароль от Вашего личного кабинета
                    </p>

                    <div class="recovery-form__submit">
                        <button class="submit-btn">
                            Восстановить
                        </button>
                    </div>

                </div>
            </form>
        </div>
        
    </div>
</template>

<script>

export default {
    name: "ForgotPassword",
}
</script>

<style lang="scss" scoped>

.content{
    box-sizing: border-box;
    width: 100%;
    padding: 10px 20px;

    &__top{
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1rem 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    &__bottom{
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
}

.title{
    color: #3e4555;
    font-size: 16px;
    font-weight: 700;
}

.recovery-form{
    width: 500px;

    &__item{
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    &__input{
        width: 100%;
    }

    &__description{
        color: #8d97ad;
        text-align: center;
    }

    @media only screen and (max-width: 539px) {
        width: 100%;
    }
}

</style>