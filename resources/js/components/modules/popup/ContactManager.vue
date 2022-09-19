<template>

    <div class="content">

        <div class="content__top">
            <div class="title">
                <span class="manager-name">{{ managerInfo.name }}</span>
                &nbsp;
                <span class="manager-position">({{ managerInfo.position }})</span>
            </div>
        </div>

        <div class="content__middle">

            <img class="manager-avatar" :src="managerInfo.photo" alt="Фото менеджера">

            <table class="contacts">
                <tbody>
                    <tr class="contacts__item contact">
                        <th class="contact__label">Email:</th>
                        <td class="contact__value">
                            <a :href="'mailto:'+managerInfo.email" class="link">{{ managerInfo.email }}</a>
                        </td>
                    </tr>
                    <tr class="contacts__item contact">
                        <th class="contact__label">Телефон:</th>
                        <td class="contact__value">{{ managerInfo.phone }}</td>
                    </tr>
                    <tr class="contacts__item contact">
                        <th class="contact__label">WhatsApp:</th>
                        <td class="contact__value">{{ managerInfo.whatsApp }}</td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div class="content__bottom">
            <h3>Сообщение на имейл:</h3>

            <form class="contact-form">
                <textarea class="contact-form__input" placeholder="Сообщение"></textarea>
                <button class="contact-form__submit submit-btn" type="submit">Отправить</button>
            </form>
        </div>

    </div>

</template>

<script>
export default {
    name: "ManagerInfo",
    props: {
        managerInfo: {
            type: Object,
            required: true
        }
    }
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

    &__middle{
        margin-top: 20px;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-around;
    }

    &__bottom{
        margin-top: 20px;
    }
}

.title{
    color: rgb(84, 84, 84);
}

.manager-name{
    font-weight: 700;
}

.manager-avatar{
    display: block;
}

.contact{

    &__label{
        text-align: left;
    }

    &__value{

    }
}

.contact-form{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    &__input{
        box-sizing: border-box;
        height: 50px;
        max-height: 200px;
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    &__submit{
        margin-top: 10px;
        padding: 10px 35px;
    }
}

</style>
