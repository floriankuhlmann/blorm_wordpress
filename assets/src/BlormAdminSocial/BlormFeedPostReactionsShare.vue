<template>
       <span v-on:click="feedUser()" class="BlormFeedPostReactionsBlormhandle">{{getblormhandle}}</span>
</template>

<script>
    export default {
        props: ['shareReaction'],
        data: function () {
            return {}
        },
        mounted: function () {
            //console.log(this.shareReaction.user.data.data);
        },
        computed: {
            getblormhandle: function() {
                return this.shareReaction.user.data.data.blormhandle;
            },
        },
        methods: {
            feedUser: function () {
                //console.log(this.shareReaction.user.id);
                //this.$root.loadUserPage(this.shareReaction.user.id);
              let loadPromise = this.$root.loadUserData(this.shareReaction.user.id);
              loadPromise.then(
                  response => {
                    this.$root.loadUserPage(this.$store.state.user);
                  },
                  error => {
                    alert(error);
                    this.$root.loadAccountPage();
                  }
              );
            },
        },
    }
</script>