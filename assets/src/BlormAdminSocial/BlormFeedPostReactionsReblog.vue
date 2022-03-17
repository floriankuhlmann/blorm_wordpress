<template>
    <span v-on:click="feedUser()" class="BlormFeedPostReactionsBlormhandle">{{getblormhandle}}</span>
</template>

<script>

    export default {
        props: ['reblogReaction'],
        data: function () {
            return {}
        },
        mounted: function () {
            //console.log(this.reblogReaction);
        },
        computed: {
            getblormhandle: function() {
                return this.reblogReaction.user.data.data.blormhandle;
            },
        },
        methods: {
            feedUser: function () {
                //console.log(this.reblogReaction.user.id);
                //this.$root.loadUserPage(this.reblogReaction.user);
              let loadPromise = this.$root.loadUserData(this.reblogReaction.user.id);
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