<template>
    <div class="content">

        <div class="content__top">
            <div class="title">Для продолжения введите код из смс</div>
        </div>

        <div class="content__bottom">
            <form class="verify-form" @submit.prevent="handleSmsVerification">

                <div class="verify-form__error" v-if="error">
                    {{ errorMessage }}
                </div>

                <div class="verify-form__item">
                    <p class="verify-form__description">
                        В течение минуты на телефон "{{ phone }}" придёт sms с кодом. 
                        Введите данный код для продолжения.
                    </p>
                </div>

                <div class="verify-form__item">
                    <input class="verify-form__input input" 
                        type="phone" placeholder="Введите код из СМС" v-model="smsCode">
                </div>

               <div class="verify-form__item">
                    <div class="verify-form__submit">
                        <button class="submit-btn">
                            Отправить
                        </button>
                    </div>
                </div>

                <div class="verify-form__item">
                    <div class="verify-form__resend">
                        <a href="">Выслать смс еще раз</a>
                    </div>
                </div>

            </form>
        </div>

    </div>
</template>

<script>

// validation package
import { email, required, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useUserStorage } from '../../../storage/pinia/userStorage';
import { sha256 } from '../../../utils/functions/crupto';

export default {
    name: "SmsVerification",
    data() {
        return {
            smsCode: '',
            error: false,
            errorMessage: '',
        }
    },
    setup: () => ({ v$: useVuelidate() }),
    validations() {
        return {
            smsCode: {
                required: helpers.withMessage('обязательное поле', required),
            }
        }
    },
    props: {
        phone: {
            type: String,
            required: true
        },
        email: {
            type: String,
            required: true
        },
        password: {
            type: String,
            required: true
        }
    },
    methods: {
        async handleSmsVerification() {
            this.error = false;
            this.errorMessage = '';

            this.v$.$validate();
            
            if(this.v$.$error){
                this.error = true
                this.errorMessage = this.v$.$errors[0].$message;
                return;
            }

            try{
                const res = await this.$backendApi.auth().smsSend({
                    email: this.email,
                    phone: this.phone,
                    password: this.password,
                    smsCode: this.smsCode
                })
 
                const userStorage = useUserStorage();

                userStorage.setIsAthorized(true)

                userStorage.setUserInfo({
                    email: this.email,
                    phone: this.phone,
                    password: await sha256(this.password),
                    userId: res.data.id,
                    registrationDate: res.data.registrationDate
                })

                this.$backendApi.setAuthUserToken(res.data.message.token);

                this.$router.push({name: 'order_history'});

            } catch(err) {
                this.error = true,
                this.errorMessage = err.response.data.message
            }
        }
    },
}
</script>

<style lang="scss" scoped>
@import '@scss/abstract/variables';


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

.verify-form{
    width: 500px;

    &__error{
        text-align: center;
        color: $default-error-color;
    }

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

    &__submit{
        margin-top: 20px;
    }

    &__resend{
        margin-top: 15px;
        width: 100%;
        display: flex;
        justify-content: flex-end;

        & a{
            text-decoration: underline;
            color: #8d97ad;
            font-size: 14px;
            font-weight: 400;
        }
    }

    @media only screen and (max-width: 539px) {
        width: 100%;
    }
}

</style>