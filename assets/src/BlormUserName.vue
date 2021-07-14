<template>
    {{displayName}}
</template>

<script>
export default {
    props: ['fillWordSecondPerson','fillWordThirdPerson'],
    computed: {
        displayName () {
            if (this.$store.state.user.blormhandle === this.$store.state.account.blormhandle ) {
               return  "you" + this.getFillWordSecondPerson();
            } else {
                return this.$store.state.user.blormhandle + this.getFillWordThirdPerson();
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
