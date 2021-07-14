<template>
    <div :data-id="commentItemId" class="BlormFeedPostcomment">
        <div class="BlormFeedPostcomment_User">
            <img :src="commentItemUserPic" style="width:100%; height: auto;">
        </div>
        <div class="BlormFeedPostcomment_Content">
            <div class="BlormFeedPostcomment_Content_Text">
                <b><a href="#" :onclick="commentItemUserFeed">{{commentItemBlormHandle}}</a></b>
                <span v-html="commentItemText"></span>
            </div>
            <div class="BlormFeedPostcomment_Content_Date">
                <small>{{commentItemDate}}</small><br>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment'

    export default {
        name: 'BlormFeedComment',
        props: ['commentItem'],
        computed: {
            commentItemId: function() {
                return this.commentItem.id;
            },
            commentItemBlormHandle: function() {
                return this.commentItem.user.data.data.blormhandle;
            },
            commentItemUserPic: function() {
                return this.commentItem.user.data.data.photo_url;
            },
            commentItemText: function() {
                return ":&nbsp;"+this.commentItem.data.text;
            },
            commentItemDate: function() {
                var date = new Date(this.commentItem.updated_at);
                return moment(date.getTime() + 60*60*1000*2).fromNow();
            }
        },
        methods: {
            commentItemUserFeed: function() {
                this.$root.loadUserPage(this.commentItem.user.id);
            },
        }
    };
</script>