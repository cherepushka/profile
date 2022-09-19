<template>

    <div class="container">
        <table class="info-table">
            <tbody>
                <tr class="info-table__row">
                    <td class="info-table__cell heading-cell">Email:</td>
                    <td class="info-table__cell">{{ email }}</td>
                </tr>
                <tr class="info-table__row">
                    <td class="info-table__cell heading-cell">Номер телефона:</td>
                    <td class="info-table__cell">{{ phone }}</td>
                </tr>
                <tr class="info-table__row">
                    <td class="info-table__cell heading-cell">Дата регистрации:</td>
                    <td class="info-table__cell">{{ registrationDate }}</td>
                </tr>
            </tbody>
        </table>

        <div class="change-password">
            <h3 class="change-password__title">Изменить пароль</h3>

            <div class="change-password__form">
                <input class="input change-password__input" type="text" placeholder="новый пароль">
                <input class="input change-password__input" type="text" placeholder="повторите пароль">

                <button class="button">Изменить</button>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    name: "PersonalInfo",
    data() {
        return {
            email: 'mail@fluid-line.ru',
            phone: '74959844100',
            registrationDate: '22.03.2021 11:19:00',
        }
    }
}
</script>

<style lang="scss" scoped>

.info-table{
    border-collapse: collapse;
    text-align: left;

    &__row{
        border-bottom: 1px solid #eceeef;
    }

    &__cell{
        padding: 10px;
    }
}

.heading-cell{
    min-width: 250px;
}

.change-password{

    &__form{

    }

    &__input{
        margin-right: 15px;
    }
}

</style>
