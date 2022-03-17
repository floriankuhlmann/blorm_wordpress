<template>
    {{displayName}}
</template>

<script>
export default {
    props: ['fillWordSecondPerson','fillWordThirdPerson'],
    computed: {
        displayName () {
            if (this.$store.state.user.blormHandle === this.$store.state.account.blormHandle ) {
               return  "you" + this.getFillWordSecondPerson();
            } else {
                return this.$store.state.user.blormHandle + this.getFillWordThirdPerson();
            }
        }
    },
    methods: {
        getFillWordSecondPerson () {
            if (this.fillWordSecondPerson == null) {
                return ""
            }
            return this.fillWordSecondPerson;
        },
        getFillWordThirdPerson () {
            if (this.fillWordThirdPerson == null) {
                return ""
            }
            return this.fillWordThirdPerson;
        }
    },
};
</script>

<style lang="css">

</style>
