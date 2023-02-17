<template>
    <div class="container">
        <div class="login-wrapper">
            <div class="contact-form">

                <h2 class="contact-form__head greeting-text">Добро пожаловать в личный кабинет клиента Fluid-Line</h2>
                <h6 class="contact-form__head auth-text">Пожалуйста, авторизуйтесь для входа</h6>

                <div class="login-error" v-if="apiError != ''">
                        {{ this.apiError }}
                </div>

                <div class="form-wrapper">
                    <form class="login-form" action="" @submit.prevent="handleLogin">
                        <div class="login-form__item">

                            <ul class="login-form__input-errors">
                                <li v-for="error in userInput.email.errors">
                                    {{ error }}
                                </li>
                            </ul>
                            <input class="login-form__input input" 
                                ref="userInput.email.email"
                                type="email" 
                                placeholder="Email"
                                autocomplete="profile-user email"
                                v-model="userInput.email.email"
                            >
                        </div>
                        <div class="login-form__item">

                            <ul class="login-form__input-errors">
                                <li v-for="error in userInput.phone.errors">
                                    {{ error }}
                                </li>
                            </ul>
                            <input class="login-form__input input" 
                                ref="userInput.phone.phone"
                                type="tel" 
                                placeholder="Номер телефона"
                                autocomplete="profile-user phone"
                                v-model="userInput.phone.phone"
                            >
                        </div>
                        <div class="login-form__item">

                            <ul class="login-form__input-errors">
                                <li v-for="error in userInput.password.errors">
                                    {{ error }}
                                </li>
                            </ul>
                            <input class="login-form__input input" 
                                ref="userInput.password.password"
                                type="password" 
                                placeholder="Пароль"
                                autocomplete="profile-user password"
                                v-model="userInput.password.password"
                            >
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
            <sms-verification 
                :phone="userInput.phone.phone"
                :email="userInput.email.email"
                :password="userInput.password.password"
            >
            </sms-verification>
        </popup-wrapper>
    </div>

</template>

<script>
import SmsVerification from '../modules/popup/SmsVerification';
import ForgotPassword from '../modules/popup/ForgotPassword';
import PopupWrapper from '../modules/base/PopupWrapper';

// validation package
import { email, required, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'

export default {
    name: "Login",
    data() {
        return {
            title: 'Авторизация',
            isForgotPasswordPopupOpen: false,
            isSmsVerificationPopupOpen: false,
            apiError: '',
            userInput: {
                email: {
                    email: '',
                    errors: []
                },
                phone: {
                    phone: '',
                    errors: []
                },
                password: {
                    password: '',
                    errors: []
                },
                smsVerificationCode: {
                    smsVerificationCode: '',
                    errors: []
                },
            },
        }
    },
    setup: () => ({ v$: useVuelidate() }),
    validations() {
        return {
            userInput: {
                email: {
                    email: {
                        required: helpers.withMessage('обязательное поле', required),
                        email: helpers.withMessage('должно быть валидным имейлом', email),
                    }
                },
                phone: {
                    phone: {
                        required: helpers.withMessage('обязательное поле', required),
                    }
                },
                password: { 
                    password: {
                        required: helpers.withMessage('обязательное поле', required),
                    }
                },
            }
        }
    },
    methods: {
        async handleLogin() {
            this.apiError = '';

            this.flushErrorsForInput();
            this.v$.$validate();
            
            if(this.v$.$error){

                this.v$.$errors.forEach(err => {
                    this.setErrorsForInput(err.$property, err.$propertyPath, err.$message);
                });
                return;
            }

            try{
                await this.$backendApi.auth().login({
                    email: this.userInput.email.email, 
                    phone: this.userInput.phone.phone, 
                    password: this.userInput.password.password
                });

                this.isSmsVerificationPopupOpen = true;
            } catch (err) {
                this.apiError = err.response.data.message;
            }

        },
        setErrorsForInput(propertyName, inputRef, errorMessage) {
            this.$refs[inputRef].classList.add('validation-error');
            this.userInput[propertyName].errors.push(errorMessage)
        },
        flushErrorsForInput() {
            this.$el.querySelectorAll('input').forEach(input => {
                input.classList.remove('validation-error')
            });

            for (const [inputName, i] of Object.entries(this.userInput)) {
                this.userInput[inputName].errors = []
            }
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
@import '@scss/abstract/variables';

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

.login-error{
    padding: 20px 0;
    text-align: center;
    color: $default-error-color;
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

    &__input-errors{
        & li {
            color: $default-error-color;
        }
    }

    &__input{
        width: 100%;
        display: block;
        height: 40px;
        font-size: 16px;
        font-weight: 400;
        line-height: 1.5;

        &.validation-error{
            border-color: $default-error-color;
            border-width: 2px;
        }
    }

    &__submit{
        width: 100%;
    }
}

</style>
