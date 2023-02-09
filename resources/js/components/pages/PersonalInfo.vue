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
                    <td class="info-table__cell">{{ new Date(registrationDate * 1000).toLocaleString('ru') }}</td>
                </tr>
            </tbody>
        </table>

    </div>

</template>

<script>
import { mapState } from 'pinia';
import { useUserStorage } from '../../storage/pinia/userStorage';

export default {
    name: "PersonalInfo",
    data() {
        return {}
    },
    computed: {
        ...mapState(useUserStorage, ['email', 'phone', 'registrationDate'])
    },
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
