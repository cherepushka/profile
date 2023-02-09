<template>

    <div></div>

</template>

<script>

import { useUserStorage } from '../../storage/pinia/userStorage';

export default {
    name: "Logout",
    data() {
        return {
            title: 'Выйти',
        }
    },
    beforeRouteEnter(to, from, next){
        useUserStorage().setIsAthorized(false);
    }
}
</script>

<style lang="scss" scoped>

</style>
