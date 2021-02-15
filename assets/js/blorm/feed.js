jQuery(document).ready(function() {

    console.log("feed.js loaded");

    /*
    * component
    * 'blorm-feed-comment'
    *
    */
    Vue.component('blorm-feed-comment', {
    props: ['latest_reactions'],
    methods: {
        showdate: function (ISOdate) {
            var date = new Date(ISOdate   );
            return moment(date.getTime() + 60*60*1000*2).fromNow();
        },
        getUrl: function (url) {
            return "http://"+url;
        }
    },
    template: //'<div v-if="latest_reactions.comment">' +
        '<div class="blorm-feed-comment>">' +
        '<template v-for="commentitem in latest_reactions.comment">' +
        '<div :data-id="commentitem.id" class="BlormFeedPostcomment">' +
        '<div class="BlormFeedPostcomment_User"><img :src="commentitem.user.data.data.photo_url" style="width:100%; height: auto;"></div>' +
        '<div class="BlormFeedPostcomment_Content">' +
        '<div class="BlormFeedPostcomment_Content_Text">' +
        '<b><a :href="getUrl(commentitem.user.data.data.website)">{{commentitem.user.data.data.name}}</a></b> ' +
        '<span v-html="commentitem.data.text"> {{commentitem.data.text}}</span></div>' +
        '<div class="BlormFeedPostcomment_Content_Date"><small>{{showdate(commentitem.updated_at)}}</small><br></div>' +
        '</div>' +
        '</div>' +
        '</template>' +
        '</div>'
    });

    /*
    * component
    * 'blorm-feed-post-actions'
    *
    */
    Vue.component('blorm-feed-post-actions', {
    props: ['post','blormapp.core'],
    methods: {
        postShare: function (verb, event) {

            responsePromise = blormapp.core.postShare(verb, event, this.post);
            responsePromise.then(this.handleShareSuccess, this.handleShareError);
        },
        postReblog: function (verb, event) {
            blormapp.core.postReblog(verb, event, this.post);
        },
        handleShareSuccess: function (response) {
            blormapp.core.feedTimeline();
        },
        handleShareError: function (response) {
            console.log("sharing error:");
            console.log(response);
        },

    },
    template:
        '<div class="BlormFeedAction" :data-activityid="post.activityId" :data-objectiri="post.object.iri" :data-objecttype="post.object.type">\n' +
        '<hr>\n' +
        '<button v-on:click="postShare(\'share\', $event)">share in timline</button> | <button v-on:click="postReblog(\'reblog\', $event)">reblog</button>\n' +
        '</div>\n'
    });

    /*
   * component
   * 'blorm-feed-post-headline'
   *
   */

    Vue.component('blorm-feed-post-headline', {
        props: ['post','blormapp.core'],
        methods: {
            renderDate: function (ISOdate) {
                var date = new Date(ISOdate   );
                return moment(date.getTime() + 60*60*1000*2).fromNow();
            },
            renderIcon: function(verb) {
                let icon = "";
                let imgsrc = templateUrl+"/blorm/assets/icons/";
                switch (verb) {
                    case "share":
                        icon = "circle-sync-backup-1-glyph.png";
                        break;
                    case "reblog":
                        icon = "editor-copy-2-duplicate-outline-stroke.png";
                        break;
                    case "create":
                        icon = "other-arrow-right-other-outline-stroke.png";
                        break;
                    default:
                        imgsrc = "other-arrow-right-other-outline-stroke.png";
                        break;
                }
                return imgsrc + icon;
            },
            renderAction: function(verb) {
                switch (verb) {
                    case "share":
                        action = "shared this";
                        break;
                    case "reblog":
                        action = "reblogged this";
                        break;
                    case "create":
                        action = "posted this";
                        break;
                    default:
                        action = "";
                        break;
                }
                return action;
            },
            renderUser: function(post) {
                if (post.isOwner) {
                    return "You";
                }
                return post.actor.name;
            },
            feedUser: function(id) {
                console.log(id);
                blormapp.core.feedUser(id);
            },
        },
        template:
            '<div class="blorm-feed-post-headline">' +
            '<div style=\"margin-bottom: 0.5rem;\">' +
            '<i style=\"color:grey\">{{renderDate(post.object.time)}}</i>' +
            '</div>' +
            '<div>' +
            '<img :src="renderIcon(post.object.verb)" style=\"height: 1.5rem; margin-bottom:-0.5rem; margin-right: 0.5rem;\">' +
            '<template v-if="post.isOwner === true">' +
                '<b>{{renderUser(post)}} {{renderAction(post.object.verb)}}</b>' +
            '</template>' +
            '<template v-else>' +
                '<b><span v-on:click="feedUser(post.actor.id)" class="BlormFeedHeadlineUser">{{renderUser(post)}}</span> {{renderAction(post.object.verb)}}</b>' +
            '</template>' +
            '</div>' +
            '</div>'
    });


    /*
    * component
    * 'blorm-feed-post'
    *
    */
    Vue.component('blorm-feed-post', {
    props: ['post','blormusername','newcomment'],
    computed: {
        postImage: function() {
            if (this.post.object.image === "non" || this.post.object.image == null ) {
                return "";
            }
            return  "<div class=\"BlormFeedImage\">" +
                    "<img src=\""+this.post.object.image+"\">" +
                    "</div>";
        }
    },
    methods: {
        showdate: function (ISOdate) {
            var date = new Date(ISOdate   );
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
        postDelete: function (activityId) {
            blormapp.core.postDelete(activityId);
            blormapp.core.feedTimeline();
        },
        reblogUndo: function (activityId) {
            console.log("reblogundo");
            console.log(activityId);
            blormapp.core.reblogUndo(activityId);
            blormapp.core.feedTimeline();
        },
        shareUndo: function (activityId) {
            blormapp.core.shareUndo(activityId);
            blormapp.core.feedTimeline();
        },
        feedUser: function(id) {
            console.log(feedUser);
            blormapp.core.feedUser(id);
        },
    },
    template: '<span v-if="post.error === false">' +
        '       <template v-if="post.teaser === true">' +
        '           <div class="BlormFeedPost" :class="post.object.verb" :data-activityid="post.activityId" :data-objectiri="post.object.iri" :data-objecttype="post.object.type">\n' +
        '                <div class="BlormFeedEdit">' +
        '                   <div class="BlormFeedEdit--Date">' +
        '                   <blorm-feed-post-headline v-bind:post="post"></blorm-feed-post-headline>' +
        '                       <span v-if="post.isOwner === true">' +
        '                       <template v-if="post.object.verb === \'create\'">'  +
        '                           <div class="BlormFeedEdit--Mod">' +
        '                               <button v-on:click="postDelete(post.activityId)">delete</button>' +
        '                           </div>\n' +
        '                       </template>\n' +
        '                       <template v-if="post.object.verb === \'reblog\'">'  +
        '                           <div class="BlormFeedEdit--Mod">' +
        '                               <button v-on:click="reblogUndo(post.activityId)">undo</button>' +
        '                           </div>\n' +
        '                       </template>\n' +
        '                       <template v-if="post.object.verb === \'share\'">'  +
        '                           <div class="BlormFeedEdit--Mod">' +
        '                               <button v-on:click="shareUndo(post.activityId)">undo</button>' +
        '                           </div>\n' +
        '                       </template>\n' +
        '                       </span>' +
        '                       <hr class="BlormFeedBorder">\n' +
        '                   </div>' +
        '                </div>\n' +
        '                <div class="BlormFeedTitle">\n' +
        '                    <h2 class="BlormFeedTitle"><a :href="post.object.url">{{ post.object.headline }}</a></h2>\n' +
        '                </div>\n' +
        '                <span v-html="postImage"></span>' +
        '                <div class="BlormFeedContent">\n' +
        '                    <p>{{ post.object.text }}</p>\n' +
        '                </div>\n' +
        '                <div class="BlormFeedURL">\n' +
        '                    <a :href="post.object.url"><i>read this</i></a>\n' +
        '                </div>\n' +
        '                <span v-if="post.isOwner === false">' +
        '                   <blorm-feed-post-actions v-bind:post="post"></blorm-feed-post-actions>' +
        '                </span>'+
        '               <blorm-feed-comment' +
        '               v-bind:latest_reactions="post.latestReactions">\n' +
        '               </blorm-feed-comment>\n' +
        '               <div class="BlormFeedAction--comment" :data-id="post.activityId">            ' +
        '               <div contenteditable class="BlormFeedAction--comment-textbox" v-html="newcomment" v-on:keyup="commentchanged" v-on:blur="commentchanged" v-on:paste="commentchanged" v-on:delete="commentchanged" v-on:focus="commentchanged">' +
        '               </div>\n' +
        '                   <div class="BlormFeedAction">\n' +
        '                       <button v-on:click="postComment">comment</button>' +
        '                   </div>\n' +
        '               </div>\n' +
        '            </div>' +
        '       </template>' +
        '       <template v-else>' +
        '           <div class="BlormFeedPost">' +
        '               There is no post in your timeline.<br>Why dont you share something or follow someone?' +
        '           </div>' +
        '       </template>' +
        '</span>'

    });

});