jQuery(document).ready(function() {

    console.log("feed.js loaded");

    Vue.component('blorm-feed-comment', {
    props: ['latest_reactions'],
    methods: {
        showdate: function (ISOdate) {
            var date = new Date(ISOdate   );
            //var timeString = date.toUTCString();

            return moment(date.getTime() + 60*60*1000*2).fromNow();
        },
        getUrl: function (url) {
            return "http://"+url;
        }
    },
    template: //'<div v-if="latest_reactions.comment">' +
        '<div class="Blorm_Blormfeed_Postcomments>">' +
        '<template v-for="commentitem in latest_reactions.comment">' +
        '<div :data-id="commentitem.id" class="Blorm_Blormfeed_Postcomment">' +
        '<div class="Blorm_Blormfeed_Postcomment_User"><img :src="commentitem.user.data.data.photo_url" style="width:100%; height: auto;"></div>' +
        '<div class="Blorm_Blormfeed_Postcomment_Content">' +
        '<div class="Blorm_Blormfeed_Postcomment_Content_Text">' +
        '<b><a :href="getUrl(commentitem.user.data.data.website)">{{commentitem.user.data.data.name}}</a></b> ' +
        '<span v-html="commentitem.data.text"> {{commentitem.data.text}}</span></div>' +
        '<div class="Blorm_Blormfeed_Postcomment_Content_Date"><small>{{showdate(commentitem.updated_at)}}</small><br></div>' +
        '</div>' +
        '</div>' +
        '</template>' +
        '</div>'
    });

    Vue.component('blorm-feed-post-actions', {
    props: ['post','blormapp.core'],
    methods: {
        postShare: function (verb, event) {
            blormapp.core.postShare(verb, event, this.post);
        },
        postReblog: function (verb, event) {
            blormapp.core.postReblog(verb, event, this.post);
        }
    },
    template:
        '<div class="Blorm_Blormfeed_Action" :data-activityid="post.activityId" :data-objectiri="post.object.iri" :data-objecttype="post.object.type">\n' +
        '<hr>\n' +
        '<button v-on:click="postShare(\'share\', $event)">share in timline</button> | <button v-on:click="postReblog(\'reblog\', $event)">reblog</button>\n' +
        '</div>\n'
});

    Vue.component('blorm-feed-post', {
    props: ['post','blormusername','newcomment'],
    computed: {
        postHeadline: function() {
            switch (this.post.object.verb) {
                case "share":
                    imgsrc = "/blorm/assets/icons/circle-sync-backup-1-glyph.png";
                    action = "shared this";
                    /*statusline = "<div style=\"margin-bottom: 0.5rem\";><i style='color:grey'>"+this.showdate(this.post.object.time)+"</i></div>" +
                        "<div style=\"width:75%;display: inline-block;\"'><img src='"+templateUrl+"/blorm/assets/icons/circle-sync-backup-1-glyph.png' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                        "<b><a href='http://"+this.post.actor.website+"'>"+this.post.actor.name+"</a> shared this</b></div>";*/
                    break;
                case "reblog":
                    imgsrc = "/blorm/assets/icons/editor-copy-2-duplicate-outline-stroke.png";
                    action = "reblogged this";
                    /*statusline = "<div style=\"margin-bottom: 0.5rem\";><i style='color:grey'>"+this.showdate(this.post.object.time)+"</i></div>" +
                        "<div style=\"width:75%;display: inline-block;\"'><img src='"+templateUrl+"/blorm/assets/icons/editor-copy-2-duplicate-outline-stroke.png' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                        "<b><a href='http://"+this.post.actor.website+"'>"+this.post.actor.name+"</a> reblogged this</b></div>";*/
                    break;
                case "create":
                    imgsrc = "/blorm/assets/icons/other-arrow-right-other-outline-stroke.png";
                    action = "posted this";
                    /*statusline = "<div style=\"margin-bottom: 0.5rem\";><i style='color:grey'>"+this.showdate(this.post.object.time)+"</i></div>" +
                        "<div style=\"width:75%;display: inline-block;\"'><img src='"+templateUrl+"/blorm/assets/icons/other-arrow-right-other-outline-stroke.png' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                        "<b><a href='http://"+this.post.actor.website+"'>"+this.post.actor.name+"</a> posted this</b></div>";*/
                    break;
                default:
                    statusline = "<span>Welcome to BLORM</span>";
                    break;
            }

            return "<div style=\"margin-bottom: 0.5rem\";><i style='color:grey'>"+this.showdate(this.post.object.time)+"</i></div>" +
                "<div style=\"width:75%;display: inline-block;\"'><img src='"+templateUrl+imgsrc+"' style='height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;'>" +
                "<b><a href='http://"+this.post.actor.website+"'>"+this.post.actor.name+"</a> "+action+"</b></div>";
        },
        postImage: function() {
            if (this.post.object.image === "non" || this.post.object.image == null ) {
                return "";
            }

            return '<div class="Blorm_Blormfeed_Image"><img src="'+this.post.object.image+'"></div>';
        }
    },
    methods: {
        showdate: function (ISOdate) {
            var date = new Date(ISOdate   );
            //var timeString = date.toUTCString();

            return moment(date.getTime() + 60*60*1000*2).fromNow();
        },
        getUserFromActor: function() {
            return "username[1]";
        },
        commentchanged: function(event) {
            if ($(event.target).html() == blormapp.core.data.initCommentText ) {
                console.log($(event.target).html());
                $(event.target).html("");
            }
            blormapp.commentdata_text = $(event.target).html().trim();
            blormapp.commentdata_id = $(event.target).parent().data('id');
        },
        postComment: function (event) {
            blormapp.core.postComment(
                blormapp.commentdata_text,
                blormapp.commentdata_id
            );
        },
        postDelete: function (event) {
            blormapp.core.postDelete(
                $(event.target).data('activityid')
            );
            blormapp.core.feedTimeline();
        },
        reblogUndo: function (activityId) {
            console.log("reblogundo");
            console.log(activityId);
            blormapp.core.reblogUndo(
                activityId
            );
            blormapp.core.feedTimeline();
        },
        shareUndo: function (activityId) {
            blormapp.core.shareUndo(
                activityId
            );
            blormapp.core.feedTimeline();
        }
    },
    template: '<span v-if="post.error === false">' +
        '           <span v-if="post.teaser === true">' +
        '           <div class="Blorm_Blormfeed_Post" :class="post.object.verb" :data-activityid="post.activityId" :data-objectiri="post.object.iri" :data-objecttype="post.object.type">\n' +
        '                <div class="Blorm_Blormfeed_Edit">' +
        '                    <div class="Blorm_Blormfeed_Edit--Date">' +
        '                       <span v-html="postHeadline"></span>' +
        '                       <template v-if="post.object.verb === \'create\'">'  +
        '                           <div class="Blorm_Blormfeed_Edit--Mod">' +
        '                               <button v-on:click="postDelete(post.activityId)">delete</button>' +
        '                           </div>\n' +
        '                       </template>\n' +
        '                       <template v-if="post.object.verb === \'reblog\'">'  +
        '                           <div class="Blorm_Blormfeed_Edit--Mod">' +
        '                               <button v-on:click="reblogUndo(post.activityId)">undo</button>' +
        '                           </div>\n' +
        '                       </template>\n' +
        '                       <template v-if="post.object.verb === \'share\'">'  +
        '                           <div class="Blorm_Blormfeed_Edit--Mod">' +
        '                               <button v-on:click="shareUndo(post.activityId)">undo</button>' +
        '                           </div>\n' +
        '                       </template>\n' +
        '                       <hr class="Blorm_Blormfeed_Border">\n' +
        '                   </div>' +
        '                </div>\n' +
        '                <div class="Blorm_Blormfeed_Title">\n' +
        '                    <h2 class="Blorm_Blormfeed_Title"><a :href="post.object.url">{{ post.object.headline }}</a></h2>\n' +
        '                </div>\n' +
        '                <span v-html="postImage"></span>' +
        '                <div class="Blorm_Blormfeed_Content">\n' +
        '                    <p>{{ post.object.text }}</p>\n' +
        '                </div>\n' +
        '                <div class="Blorm_Blormfeed_URL">\n' +
        '                    <a :href="post.object.url"><i>read this</i></a>\n' +
        '                </div>\n' +
        '                <blorm-feed-post-actions' +
        '                   v-bind:post="post">\n'+
        '                </blorm-feed-post-actions>'+
        '               <blorm-feed-comment' +
        '               v-bind:latest_reactions="post.latestReactions">\n' +
        '               </blorm-feed-comment>\n' +
        '               <div class="Blorm_Blormfeed_Action--comment" :data-id="post.activityId">            ' +
        '               <div contenteditable class="Blorm_Blormfeed_Action--comment-textbox" v-html="newcomment" v-on:keyup="commentchanged" v-on:blur="commentchanged" v-on:paste="commentchanged" v-on:delete="commentchanged" v-on:focus="commentchanged">' +
        '               </div>\n' +
        '                   <div class="Blorm_Blormfeed_Action">\n' +
        '                       <button v-on:click="postComment">comment</button>' +
        '                   </div>\n' +
        '               </div>\n' +
        '            </div>' +
        '           </span>' +
        '           <span v-else>' +
        '           <div class="Blorm_Blormfeed_Post">' +
        '               There is no post in your timeline.<br>Why dont you share something or follow someone?' +
        '           </div>' +
        '           </span>' +
        '</span>'

    });

});