
<script>
import axios from 'axios';
import { useStore } from 'vuex'

export default {

    setup () {
        const store = useStore()
    },
    mounted: function () {
      jQuery("#wpcontent").css("background-color","#000");
      this.$store.commit('setUserData', this.$store.state.account);
      this.logMsgToCons("blorm | feedTimeline user id:", this.$store.state.user.id);
      this.renderPage(this.$store.state.account.id);
      this.getFeedDataTimeline(0);
    },
    data: function() {
        return {
            initCommentText: "Leave a comment. Please remember, be nice!",
            commentdata_text: "",
            commentdata_id: "",
        }
    },
    methods: {
      /**
       * getUserData
       * @param userId
       * @returns {Promise<any>}
       */
      setUserData: function (userId) {
        this.logMsgToCons("getUserData", userId);

        if (userId === "") {
          this.$store.commit('setUserData', this.$store.state.account);
          return;
        }

        let responsePromise = this.getBlormApiData(restapiVars.root+'blormapi/v1/user/data/userid/'+userId);
        responsePromise.then(
            this.handleGetUserDataSuccess,
            this.handleGetUserDataError
        );

      },
      handleGetUserDataSuccess: function (response) {
        this.logMsgToCons("handleGetUserDataSuccess", response.data);
        this.$store.commit('setUserData', response.data);
      },
      handleGetUserDataError: function (response) {
        this.logMsgToCons("handleGetUserDataResponseError", response);
      },
      renderPage: function(userId) {
        let bgColor = "";
        this.feedNotification();
        this.getFollowersOfUser(userId);
        this.getFollowingUsers(userId);
        this.setUserData(userId);
        if (!this.isAccountDataOnDisplay(userId)) {
          bgColor = "FFFFFF";
          jQuery("#BlormDashboardWidgetNewPost").css("display", "none");
          jQuery("#BlormDashboardWidgetSearchUser").css("display", "none");
        } else {
          bgColor = "FFFF94";
          jQuery("#BlormDashboardWidgetNewPost").css("display", "block");
          jQuery("#BlormDashboardWidgetSearchUser").css("display", "block");
        }
        this.changePageDesign(bgColor);
      },
      changePageDesign: function (color) {
        jQuery("#wpcontent .wrap h1").css("color", "#"+color);
        jQuery( "#BlormDashboardWidgetFeed" ).css("background-color", "#"+color);
        jQuery( "#BlormDashboardWidgetUserProfile" ).css("background-color", "#"+color);
        jQuery( "#BlormDashboardWidgetFollowers" ).css("background-color", "#"+color);
        jQuery( "#BlormDashboardWidgetFollowing").css("background-color", "#"+color);
        jQuery( "#BlormDashboardWidgetFeed .blormImage").attr('src', blormPluginUrl+"/assets/images/blorm_logo_world_"+color+".png");

      },
      reloadAccountPage: function() {
        this.logMsgToCons("reloadAccountPage:", this.$store.state.account.name);
        this.renderPage(this.$store.state.account.id);
        this.$store.commit('setUserData', this.$store.state.account);
        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";
        this.getFeedDataTimeline(0);
      },
      loadUserPage: function(userId) {
        this.logMsgToCons("loadUserPage:", userId);
        this.renderPage(userId);
        this.getFollowersOfUser(userId);
        this.getFollowingUsers(userId);
        this.feedNotification();
        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";
        this.getFeedDataUser(userId);
      },
      loadSinglePost: function(activityId, userId) {
        this.logMsgToCons("loadSinglePost with id:", activityId);
        this.renderPage(userId);
        this.getFollowersOfUser(userId);
        this.getFollowingUsers(userId);
        this.feedNotification();
        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";
        this.getSinglePostData(userId);
      },
      getSinglePostData: function(id) {

        let responsePromise = this.getBlormApiData(restapiVars.root+'blormapi/v1/feed/byactivity/'+id);
        responsePromise.then(
            this.handleGetSinglePostDataSuccess,
            this.handleGetSinglePostDataError
        );

      },
      handleGetSinglePostDataSuccess: function (response) {
        var postData = {};

        if(!this.isResponseStatusOk(response)) {
          this.logMsgToCons("ResponseStatusIsNotOk", response);
          return
        }

        if (response.data.length > 0) {
          postData = this.processFeedData(response.data);
        }

        if ( postData.length > 0) {
          this.$store.commit('setFeed', postData);
          this.$store.commit('setFeedOffset', postData.length + this.$store.state.feedOffset);
        }

        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";
      },
      handleGetSinglePostDataError: function() {
        this.logMsgToCons(" oadSinglePost error", error);
        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";
      },
      /**
       * * get the timeline
       */
      getFeedDataUser: function(userId) {
        this.logMsgToCons("feedUser", userId);
        //document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";

        axios.get(
            restapiVars.root+'blormapi/v1/feed/user/'+userId,
            {
              headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': restapiVars.nonce,}
            }
        ).then(response => {
              var postData = {};

              if(!this.isResponseStatusOk(response)) {
                return
              }

              if (response.data.length > 0) {
                postData = this.processFeedData(response.data);
              }

              this.$store.commit('setFeed', postData);
              document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";

            }
        ).catch(error => {
          this.logMsgToCons("feeduser error", error);
          document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";
        });
      },
      /**
       * get the timeline
       */
      getFeedDataTimeline: function(offSet) {
        this.logMsgToCons("feedTimeline offset:", offSet);
        // reload after undoReblog, undoShare, reblog, share: the offset is ste to 0 to load the updated feed
        if (typeof offSet !== "undefined") {
          this.logMsgToCons("feedTimeline feedOffset:", offSet);
          this.$store.commit('setFeedOffset', offSet);
        }

        // if undefined we want the offset
        if (typeof offSet === "undefined") {
          offSet = this.$store.state.feedOffset;
        }

        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";

        axios.get(
            restapiVars.root+'blormapi/v1/feed/timeline?limit='+this.$store.state.feedLimit+'&offset='+offSet,
            {
              headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': restapiVars.nonce,
              }
            }
        ).then(response => {
              var postData = {};

              if(!this.isResponseStatusOk(response)) {
                return
              }

              this.logMsgToCons("feeddata", response);

              if (response.data.length > 0) {
                postData = this.processFeedData(response.data);
              }

              if ( postData.length > 0) {
                this.$store.commit('setFeed', postData);
                this.$store.commit('setFeedOffset', postData.length + this.$store.state.feedOffset);
                this.logMsgToCons("new feedOffset:", this.$store.state.feedOffset);
                document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";
              }
            }
        ).catch(error => {

          this.logMsgToCons("feedTimeline error", error);
          document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";

        });
      },
      processFeedData: function(posts) {
            this.logMsgToCons("processFeedData", posts);
            let postData = [];
            //posts.map(function (value) {
            Array.from(posts).forEach(function(value){

                var data = {};
                // check for errors in the data
                if (value.object.data.error) {
                    data.error = true;
                    data.activityId = value.id;
                    data.errortype = value.object.data.error;
                    return;
                }

                if (typeof(value.object.data.data) == "undefined") {
                    data.error = true;
                    data.activityId = value.id;
                    data.errortype = "data_undefined";
                    return;
                }

                // if we have an referenced object
                if (value.object) {
                    data.error = false;
                    data.teaser = true;
                    data.activityId = value.id;
                    data.originActivityId = value.id;
                    if (typeof value.originId !== "undefined") {
                      data.originActivityId = value.originId;
                    }
                    data.object = {
                        iri: value.object.id,
                        type: "teaser",
                        verb: value.verb,
                        time: value.time,
                        headline: value.object.data.data.headline,
                        text: value.object.data.data.text,
                        image: value.object.data.data.image,
                        url: value.object.data.data.url,
                    };
                    data.actor = {
                        id: value.actor.id,
                        name: value.actor.data.data.name,
                        userName: value.actor.data.data.username,
                        photoUrl: value.actor.data.data.photoUrl,
                        website: value.actor.data.data.website,
                    };
                    data.isOwner = (this.$store.state.user.id === value.actor.id);
                    data.ownReactions = value.own_reactions;
                    data.reactionCounts = value.reaction_counts;
                    data.latestReactions = value.latest_reactions;

                    data.comments = {
                        hasReactions: false,
                    };

                    if (typeof value.latest_reactions !== "undefined") {
                      if (typeof value.latest_reactions.comment !== "undefined") {
                        data.comments = {
                          hasReactions: true,
                          noOfReactions: value.reaction_counts.comment,
                          theReactions: value.latest_reactions.comment,
                        }
                      }

                      data.reblogs = {
                        hasReactions: false,
                      };

                      if (typeof value.latest_reactions.reblog !== "undefined") {
                        data.reblogs = {
                          hasReactions: true,
                          noOfReactions: value.reaction_counts.reblog,
                          theReactions: value.latest_reactions.reblog,
                        }
                      }

                      data.shares = {
                        hasReactions: false,
                      };

                      if (typeof value.latest_reactions.share !== "undefined") {
                        data.shares = {
                          hasReactions: true,
                          noOfReactions: value.reaction_counts.share,
                          theReactions: value.latest_reactions.share,
                        }
                      }
                    }
                    postData.push(data);
                }
            }, this);
            return postData;
        },
        feedNotification: function() {
          this.logMsgToCons("feedNotification", "start loading");
          // TODO: get notification reads and add as parameter
          let notificationsRead = this.$store.state.notificationsRead.toString();
          console.log(notificationsRead);
          axios.get(
              restapiVars.root+'blormapi/v1/feed/notification',
              {
                headers: {
                  'Content-Type': 'application/json',
                  'X-WP-Nonce': restapiVars.nonce,
                }
              }
          ).then(response => {
                var postData = {};

                if(!this.isResponseStatusOk(response)) {
                  this.logMsgToCons("feedNotification ResponseStatusIsNotOk", response);
                  return
                }

                if (response.data.length > 0) {
                  this.$store.commit('setNotifications', response.data);
                }

                /*if ( postData.length > 0) {
                  this.$store.commit('setFeed', postData);
                  this.$store.commit('setFeedOffset', postData.length + this.$store.state.feedOffset);
                  console.log("feeedOffset");
                  console.log(this.$store.state.feedOffset);
                  document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";
                }*/
              }
          ).catch(error => {

            this.logMsgToCons("feedNotification error", error);
            document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";

          });
        },
        /**
         * postCreate
         * @param createJSONObj
         */
        postCreate: function(createJSONObj) {
            this.logMsgToCons("postCreate", createJSONObj);
            document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";

            let $this = this;
            return new Promise (
                function(success, error) {
                    let bodyFormData = new FormData();
                    bodyFormData.append('uploadfile', file);
                    axios.post(
                        restapiVars.root+'blormapi/v1/blogpost/create',
                        createJSONObj,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'X-WP-Nonce': restapiVars.nonce,
                            }
                        }
                    ).then(function (response) {
                        if(!$this.isResponseStatusOk(response)) {
                          this.logMsgToCons("postCreate error", response);
                          return
                        }
                        // on success, call fullfill method, to resolve
                        success(response);
                    }).catch(function (response) {
                        error(response);
                        this.logMsgToCons("postCreate", response);
                        return false;
                    });
                }
            );
        },

        /**
         * postFileUpload
         * @param file
         * @returns {Promise<any>}
         */
        postFileUpload: function(file) {
           return new Promise (
                function(success, error) {
                    let bodyFormData = new FormData();
                    bodyFormData.append('uploadfile', file);
                    axios.post(
                        restapiVars.root+'blormapi/v1/file/upload',
                        bodyFormData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'X-WP-Nonce': restapiVars.nonce,
                            }
                        }
                    ).then(function (response) {
                        // on success, call fullfill method, to resolve
                        success(response);
                    }).catch(function (response) {
                        this.logMsgToCons("postFileUpload error", response);
                        error(response);
                    });
                }
           );
        },

        /**
         * postDelete
         * @param activityId
         */
        postDelete: function (activityId) {
            this.logMsgToCons("postDelete", activityId);
            document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";

            let promiseObj = new Promise (function(fullfill, reject) {
                axios.get(restapiVars.root+'blormapi/v1/blogpost/delete/'+activityId,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,}
                    }
                ).then(function (response) {
                    fullfill(response);
                }).catch(error => {
                    this.logMsgToCons("postDelete error", error);
                    reject(error);
                });
            });
            return promiseObj;
        },

        /**
         * postShare
         * @param verb
         * @param event
         * @param post
         */
        postShare: function(verb, event, post) {

            this.logMsgToCons("postShare", verb);
            document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";

            let promiseObj = new Promise (function(fullfill, reject) {
                var shareJSONObj = {
                    "@context": "https://www.w3.org/ns/activitystreams",
                    "verb": verb,
                    "type": jQuery(event.target).parent().data('objecttype'),
                    "origin_post": {
                        "object_iri": jQuery(event.target).parent().data('objectiri'),
                        "activity_id": jQuery(event.target).parent().data('originactivityid')
                    }
                };
                axios.post(
                    restapiVars.root+'blormapi/v1/blogpost/share',
                    shareJSONObj,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,
                        }
                    }
                ).then(response => {
                    fullfill(response);
                }).catch(error => {
                    this.logMsgToCons("postShare error", error);
                    reject(error);
                });
            });
            //Returns Promise object
            return promiseObj;
        },

        /**
         * postReblog
         * @param verb
         * @param event
         * @param post
         */
        postReblog: function(verb, event, post) {
            this.logMsgToCons("postReblog", verb);
            document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";

            let promiseObj = new Promise (function(fullfill, reject) {
                var shareJSONObj = {
                    "@context": "https://www.w3.org/ns/activitystreams",
                    "verb": verb,
                    "type": jQuery(event.target).parent().data('objecttype'),
                    "origin_post": {
                        "object_iri": jQuery(event.target).parent().data('objectiri'),
                        "activity_id": jQuery(event.target).parent().data('originactivityid')
                    },
                    "origin_post_data": {
                        "headline": post.object.headline,
                        "text": post.object.text,
                        "image": post.object.image,
                        "url": post.object.url,
                    }
                };
                axios.post(
                    restapiVars.root+'blormapi/v1/blogpost/reblog',
                    shareJSONObj,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,
                        }
                    }
                ).then(response => {
                    fullfill(response);
                }).catch(error => {
                    this.logMsgToCons("postReblog", error);
                    reject(error);
                });
            });
            jQuery("#selectblogpost").val(0).prop('selected', true);
            //Returns Promise object
            return promiseObj;
        },

        /**
         * reblogUndo
         * @param activityId
         */
        reblogUndo: function (activityId) {
            this.logMsgToCons("reblogUndo", activityId);
            document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";

            let promiseObj = new Promise (function(fullfill, reject) {
                axios.get(restapiVars.root+'blormapi/v1/blogpost/undo/reblog/'+activityId,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,}
                    }
                ).then(function (response) {
                    console.log(response);
                    fullfill(response);
                }).catch(error => {
                    this.logMsgToCons("reblogUndo error", error);
                    reject(error);
                });
            });
            return promiseObj;
        },

        /**
         * shareUndo
         * @param activityId
         */
        shareUndo: function (activityId) {
            this.logMsgToCons("shareUndo", activityId);
            document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";

            let promiseObj = new Promise (function(fullfill, reject) {
                axios.get(restapiVars.root+'blormapi/v1/blogpost/undo/share/'+activityId,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,}
                    }
                ).then(function (response) {
                    fullfill(response);
                }).catch(error => {
                  this.logMsgToCons("shareUndo error", error);
                    reject(error);
                });
            });
            return promiseObj;
        },

        /**
         * postComment
         * @param commentText
         * @param activityId
         */
        postComment: function (commentText, activityId) {
            // `this` inside methods points to the Vue instance
            this.logMsgToCons("postComment", activityId);
            if (commentText === "" ||activityId === "") {
                return;
            }

            var shareJSONObj = {
                "@context": "https://www.w3.org/ns/activitystreams",
                "parent_post": {
                    "activity_id": activityId,
                },
                "comment": {
                    "text": commentText,
                }
            };

            jQuery( ".BlormFeed_Action--comment" ).css("opacity","0.5");
            jQuery( ".BlormFeed_Action--comment button" ).prop('disabled', true);
            let $this = this;
            axios.post(
                restapiVars.root+'blormapi/v1/comment/create',
                shareJSONObj,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': restapiVars.nonce,}
                }
            ).then(function (response) {
                    jQuery( ".BlormFeed_Action--comment" ).css("opacity","1");
                    jQuery( ".BlormFeed_Action--comment button" ).prop('disabled', false);

                    // update the feed
                    $this.feedTimeline(0);
                }).catch(error => {
                     this.logMsgToCons("postComment error", error);
                    reject(error);
            });
        },
        getBlormApiData: function (url) {
            this.logMsgToCons("getBlormApiData", url);
            let promiseObj = new Promise( function( fullfill, reject) {
                axios.get( url,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,
                        },
                    }
                ).then(response => {
                    fullfill(response);
                }).catch(error => {
                    this.logMsgToCons("getBlormApiData error", error);
                    reject(error);
                });
            });
            return promiseObj;
        },
        /**
         * userFollowing
         * @param blormhandle
         * @returns {Promise<any>}
         */
        userFollowing: function (blormhandle) {
            this.logMsgToCons("userFollowing", blormhandlea);
            return this.getBlormApiData(restapiVars.root+'blormapi/v1/user/follow/blormhandle/'+blormhandle);
        },
        /**
         * userUnFollowing
         * @param blormhandle
         */
        userUnFollowing: function (blormhandle) {
            this.logMsgToCons("userUnFollowing", blormhandlea);
            return this.getBlormApiData(restapiVars.root+'blormapi/v1/user/unfollow/blormhandle/'+blormhandle);
        },
        /**
         * getFollowingUsers
         */
        getFollowingUsers: function(userId) {
            this.logMsgToCons("getFollowingUsers", userId);
            let responsePromise = this.getBlormApiData(restapiVars.root+'blormapi/v1/feed/following/timeline/'+userId);
            responsePromise.then(
                this.handleGetFollowingUsersSuccess,
                this.handleResponseError
            );
        },
        handleGetFollowingUsersSuccess: function (response) {
            this.logMsgToCons("handleGetFollowingUsersSuccess", response);
            this.$store.commit('setFollowingUsers', response.data);
        },
        /**
         * getFollowersOfUser
         */
        getFollowersOfUser: function(userId) {
            this.logMsgToCons("getFollowersOfUser", userId);
            let responsePromise = this.getBlormApiData(restapiVars.root+'blormapi/v1/feed/followers/user/'+userId);
            responsePromise.then(
                this.handleGetFollowersOfUserSuccess,
                this.handleResponseError
            );
        },
        handleGetFollowersOfUserSuccess: function (response) {
            this.logMsgToCons("handleGetFollowersOfUserSuccess", response);
            this.$store.commit('setFollowersOfUser', response.data);
        },
        handleResponseError: function (response) {
            this.logMsgToCons("handleResponseError", response);
        },
        isAccountDataOnDisplay: function(userId) {
          this.logMsgToCons("isAccountDataOnDisplay", (userId === this.$store.state.account.id));
          return (userId === this.$store.state.account.id);
        },
        isResponseStatusOk: function (response) {
          this.logMsgToCons("isResponseStatusOk", response);
          if (response.status === 200 ) {
            this.$store.commit('isAuthenticated', true);
            return true;
          }
          this.$store.commit('isAuthenticated', false);
          return false;
        },
        logMsgToCons: function (logmessage, e) {
          if (this.$store.state.logToConsole) {
            console.log("message: " + logmessage);
            console.log(e);
          }
        },
    },
}
</script>

<style lang="css">
@keyframes feedOutAnimation {
    0%   { opacity: 1;     }
    100% { opacity:0.25; }
}

@keyframes feedInAnimation {
    0%   { opacity: 0.25;     }
    100% { opacity: 1; }
}

</style>