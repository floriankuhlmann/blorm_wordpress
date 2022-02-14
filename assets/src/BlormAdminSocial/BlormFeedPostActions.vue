<template>
    <div class="BlormFeedAction" :data-activityid="post.activityId" :data-originactivityid="post.originActivityId" :data-objectiri="post.object.iri" :data-objecttype="post.object.type">
        <hr>
        <button v-on:click="postShare($event)">share in timline</button> | <button v-on:click="postReblog($event)">reblog</button>
        <hr>
    </div>
</template>

<script>
    export default {
        props: ['post'],
        data: function () {
            return {}
        },
        methods: {
          postShare: function (event) {
            let responsePromise = this.$root.postShare('share', event, this.post);
            responsePromise.then(this.handleSuccess, this.handleError);
          },
          postReblog: function (event) {
            let responsePromise =  this.$root.postReblog('reblog', event, this.post);
            responsePromise.then(this.handleSuccess, this.handleError);
          },
          handleSuccess: function (response) {
            this.$root.getFeedDataTimeline(0);
          },
          handleError: function (response) {
            this.$root.logMsgToCons("sharing", response);
          },
        },
    }
</script>