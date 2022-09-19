<template>
    <div class="container">
        <div class="login-wrapper">
            <div class="contact-form">

                <h2 class="contact-form__head greeting-text">Добро пожаловать в личный кабинет клиента Fluid-Line</h2>
                <h6 class="contact-form__head auth-text">Пожалуйста, авторизуйтесь для входа</h6>

                <div class="form-wrapper">
                    <form class="login-form" action="" @submit.prevent="handleLogin">
                        <div class="login-form__item">
                            <input class="login-form__input input" type="email" placeholder="Имя пользователя">
                        </div>
                        <div class="login-form__item">
                            <input class="login-form__input input" type="tel" placeholder="Номер телефона">
                        </div>
                        <div class="login-form__item">
                            <input class="login-form__input input" type="password" placeholder="Пароль">
                        </div>
                        <div class="login-form__item">
                            <button class="login-form__submit submit-btn" type="submit">Авторизация</button>
                        </div>
                    </form>

                    <div class="form-wrapper__forgot-password">
                        <a href="" @click.prevent="toggleForgotPasswordPopup">
                            Забыли пароль?
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <popup-wrapper v-if="isForgotPasswordPopupOpen"
            @closePopup="isForgotPasswordPopupOpen = false"
        >
            <forgot-password></forgot-password>
        </popup-wrapper>

        <popup-wrapper v-if="isSmsVerificationPopupOpen"
            @closePopup="isSmsVerificationPopupOpen = false"
        >
            <sms-verification :phoneNumber="userInput.phone"></sms-verification>
        </popup-wrapper>
    </div>

</template>

<script>
import SmsVerification from '../modules/popup/SmsVerification';
import ForgotPassword from '../modules/popup/ForgotPassword';
import PopupWrapper from '../modules/base/PopupWrapper';

export default {
    name: "Login",
    data() {
        return {
            title: 'Авторизация',
            isForgotPasswordPopupOpen: false,
            isSmsVerificationPopupOpen: false,
            userInput: {
                email: 'kulpovvvan@gmail.com',
                phone: '79182319532',
                password: '123123',
                smsVerificationCode: '',
            }
        }
    },
    methods: {
        handleLogin() {
            alert('login');
        },
        toggleForgotPasswordPopup() {
            this.isForgotPasswordPopupOpen = !this.isForgotPasswordPopupOpen;
        },
        toggleSmsVerificationPopup() {
            this.isSmsVerificationPopupOpen = !this.isSmsVerificationPopupOpen
        }
    },
    components: {
        PopupWrapper,
        ForgotPassword,
        SmsVerification,
    }
}
</script>

<style lang="scss" scoped>

.login-wrapper{
    padding: 3rem 0;
    font-family: "Montserrat", sans-serif;
    color: #8d97ad;
    font-weight: 300;
    overflow: hidden;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.contact-form{
    width: 800px;
    display: flex;
    align-items: center;
    flex-direction: column;

    &__head{
        text-align: center;
    }

    @media only screen and (max-width: 539px) {
        width: 100%;
        margin: 0 10px;
    }
}

.greeting-text{
    color: #3e4555;
    font-weight: 300;
    font-family: "IBMPlexSans", sans-serif;
    font-size: 20px;
    line-height: 1.4;
    margin: 0 0 20px 0;
}

.auth-text{
    color: #8d97ad;
    line-height: 24px;
    margin: 50px 0 20px;
    font-size: 18px;
    font-weight: 400;
}

.form-wrapper{
    width: 500px;

    &__forgot-password{
        display: flex;
        justify-content: center;
    }

    @media only screen and (max-width: 539px) {
        width: 100%;
    }
}

.login-form{
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;

    &__item{
        width: 100%;
        margin-bottom: 1rem;
    }

    &__input{
        width: 100%;
        display: block;
        height: 40px;
        font-size: 16px;
        font-weight: 400;
        line-height: 1.5;
    }

    &__submit{
        width: 100%;
    }
}

</style>
