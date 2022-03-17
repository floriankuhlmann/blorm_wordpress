
<script>
import * as blormApi from './api/BlormApi';
import axios from 'axios';
import { useStore } from 'vuex'

export default {

    setup () {
        const store = useStore();
    },
    mounted: function () {
      // on mounting the user is the account
      this.$store.commit('setUserData', this.$store.state.account);
      this.logMsgToCons("blorm | feedTimeline user id:", this.$store.state.user.id);

      // render the display page for the
      this.renderPage();
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
      renderPage: function() {
        this.changePageDesign();
        this.feedNotification();

        this.getFollowersOfUser(this.$store.state.user.id);
        this.getFollowingUsers(this.$store.state.user.id);
        //this.setUserData(userId);
      },
      changePageDesign: function () {
        let color = "";
        if (this.isAccountDataOnDisplay()) {
          color = "FFFF94";
          jQuery("#BlormDashboardWidgetNewPost").css("display", "block");
          jQuery("#BlormDashboardWidgetSearchUser").css("display", "block");
        } else {
          color = "FFFFFF";
          jQuery("#BlormDashboardWidgetNewPost").css("display", "none");
          jQuery("#BlormDashboardWidgetSearchUser").css("display", "none");
        }
        jQuery( "#wpcontent").css("background-color","#000");
        jQuery( "#wpcontent .wrap h1").css("color", "#"+color);
        jQuery( "#BlormDashboardWidgetFeed" ).css("background-color", "#"+color);
        jQuery( "#BlormDashboardWidgetUserProfile" ).css("background-color", "#"+color);
        jQuery( "#BlormDashboardWidgetFollowers" ).css("background-color", "#"+color);
        jQuery( "#BlormDashboardWidgetFollowing").css("background-color", "#"+color);
        jQuery( "#BlormDashboardWidgetFeed .blormImage").attr('src', blormPluginUrl+"/assets/images/blorm_logo_world_"+color+".png");

      },
      /* the page when the account data ist displayed */
      loadAccountPage: function() {
        // reset the account data
        this.$store.commit('setUserData', this.$store.state.account);
        this.getFeedDataTimeline(0);
        this.renderPage();
      },
      /* the page when the user (followers) data ist displayed */
      loadUserPage: function(user) {
        this.$store.commit('setUserData', user);
        this.getFeedDataUser(user.id);
        this.renderPage();
      },
      /* a single post can be user or account */
      loadSinglePost: function(activityId, user) {
        this.$store.commit('setUserData', user);

        this.renderPage();
        this.getSinglePostData(activityId);
      },
      loadUserData: function (userId) {

        let _this = this;
        return new Promise (
            function(successHandle, errorHandle) {

              //console.log(userId);
              let responsePromise = blormApi.getBlormApiData(restapiVars.root+'blormapi/v1/user/data/userid/'+userId);
              // work the promise
              responsePromise.then(
                  function (response) {
                    if(!_this.isResponseStatusOk(response)) {
                      _this.logMsgToCons("ResponseStatusIsNotOk", response);
                    }
                    return response;
                  }
              ).then(
                  function (response) {

                    if (response.data.id === undefined) {
                      throw new Error("Error loading userdata: user not available");
                    }

                    //    constructor(id, name, blormHandle, photoUrl, websiteName, websiteUrl, websiteId, category, websiteType) {
                    let user = new blormApi.User(
                        response.data.id,
                        response.data.name,
                        response.data.blormhandle,
                        response.data.photo_url,
                        response.data.website_name,
                        response.data.website_href,
                        response.data.website_id,
                        response.data.website_category,
                        response.data.website_type,
                    );

                    _this.$store.commit('setUserData', user);
                    _this.logMsgToCons("handleGetUserData Set:", _this.$store.state.user);
                    return response;
                  }
              ).then(
                  function (response) {
                    successHandle(response);
                  }
              ).
              catch(
                  function (error) {
                    _this.$store.commit('setUserData', _this.$store.state.account);
                    _this.logMsgToCons("handleGetUserDataResponseError", "loadUserData error");
                    errorHandle(error);
                  }
              );
            } //function(successHandle, errorHandle)
        ); //return new Promise
      },
      getSinglePostData: function(id) {

        this.timelineFadeOut();

        let responsePromise = blormApi.getBlormApiData(restapiVars.root+'blormapi/v1/feed/byactivity/'+id);

        // work the promise
        responsePromise.then(
            response => {this.checkStatus(response); return response;}
        ).then(
            response => {

              this.processFeedData(response.data);
              // TODO: make renderpage independet from single api-calls.
              this.renderPage(response.data[0].actor.id);
              return response;

            },
            function(response) {}
        ).finally(
            response => {this.timelineFadeIn();}
        );

      },
      /**
       * * get the timeline
       */
      checkStatus: function(response) {
          if(!this.isResponseStatusOk(response)) {
            this.logMsgToCons("ResponseStatusIsNotOk", response);
          }
          return response;
      },
      timelineFadeOut: function() {
        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedOutAnimation 1s ease 0s 1 normal forwards";
      },
      timelineFadeIn: function() {
        document.getElementsByClassName("Blormfeed")[0].style.animation = "feedInAnimation 1s ease 0s 1 normal forwards";
      },
      getFeedDataUser: function(userId) {

        this.timelineFadeOut();
        let responsePromise = blormApi.getBlormApiData(restapiVars.root+'blormapi/v1/feed/user/'+userId);

        // work the promise
        responsePromise.then(
            response => {this.checkStatus(response); return response;}
        ).then(
            response => {
              this.processFeedData(response.data);
              return response;
            }
        ).finally(
            response => {this.timelineFadeIn();}
        );
      },
      /**
       * get the timeline
       */
      getFeedDataTimeline: function(offSet) {

        this.timelineFadeOut();

        // reload after undoReblog, undoShare, reblog, share: the offset is ste to 0 to load the updated feed
        if (typeof offSet !== "undefined") {
          this.logMsgToCons("feedTimeline feedOffset:", offSet);
          this.$store.commit('setFeedOffset', offSet);
        }

        // if undefined we want the offset
        if (typeof offSet === "undefined") {
          offSet = this.$store.state.feedOffset;
        }

        let responsePromise = blormApi.getBlormApiData(restapiVars.root+'blormapi/v1/feed/timeline?limit='+this.$store.state.feedLimit+'&offset='+offSet);

        // work the promise
        responsePromise.then(
            response => {this.checkStatus(response);return response}
        ).then(
            response => {
              this.logMsgToCons("feedTimeline response", response);
              this.processFeedData(response.data);
              return response;
            }
        ).finally(
            response => {this.timelineFadeIn();}
        );
      },
      feedNotification: function() {

        let isRead = "read";
        if (encodeURI(this.$store.state.notificationsRead.toString()) !== "") {
          isRead = encodeURI(this.$store.state.notificationsRead.toString());
        }

        let isSeen = "seen";
        if (encodeURI(this.$store.state.notificationsSeen.toString()) !== "") {
          isSeen = encodeURI(this.$store.state.notificationsSeen.toString());
        }

        let responsePromise = blormApi.getBlormApiData(restapiVars.root+'blormapi/v1/feed/notification/'+isRead+"/"+isSeen);

        responsePromise.then(
            response => {this.checkStatus(response);return response;}
        ).then(
            response => {
              var postData = {};
              if (response.data.length > 0) {
                postData = this.processNotificationFeedData(response.data);
                this.$store.commit('setNotifications', postData.slice(0,10));
              }
              return response;
            }
        ).then(
            response => {this.timelineFadeIn();}
        ).catch(
            response => {this.timelineFadeIn();}
        );
      },
      processFeedData: function(posts) {

        if (posts.length === 0) {
          this.$store.commit('setFeed', []);
          this.$store.commit('setFeedOffset', 0);
          return;
        }

        this.logMsgToCons("processFeedData", posts);
        let postData = [];
        Array.from(posts).forEach(function(value){

          // TODO: define data object and import
          var data = {};

          if (typeof(value.object) === "undefined") {
            data.error = true;
            data.activityId = "0"
            data.errortype = "data_undefined";
            return;
          }

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

        if ( postData.length > 0) {
          this.$store.commit('setFeed', postData);
          this.$store.commit('setFeedOffset', postData.length + this.$store.state.feedOffset);
        }
        return postData;
      },

      processNotificationFeedData: function(notifications) {

          if (!Array.isArray(notifications)) {
            this.logMsgToCons("feedNotification ResponseStatusIsNotOk: data undefined", response);
            return
          }

          let notificationsRead = encodeURI(this.$store.state.notificationsRead.toString());
          let notificationsData = [];

          // notifications loop start
          Array.from(notifications).forEach(function(notification){

            // activities loop start
            let activities = [];
            Array.from(notification.activities).forEach(function(activity){
              if (activity.object.error !== "ReferenceNotFound") {
                let tempnot = {};
                tempnot.activityId = activity.ActivityId;
                tempnot.notificationGroupId = encodeURIComponent(notification.id);
                tempnot.verb = notification.verb;
                tempnot.is_read = notification.is_read;
                tempnot.is_seen = notification.is_seen;
                tempnot.actor = activity.actor;
                tempnot.title = activity.object.data.headline;
                activities.push(tempnot);
              }
            },this); // activities loop end

            if (notification.error === true ) {
              this.$root.logMsgToCons("feed-error: notification is not vaild:", notification);
            }

            // we only want to show the latest 10 notifications
            if (activities.length > 0) {
              notificationsData.push(...activities);
            }

          },this); // notifications loop start

          this.$root.logMsgToCons("processNotificationFeedData output:", notificationsData);
          return notificationsData;
        },

      /*** postCreate
       ** @param createJSONObj
       */
      postCreate: function(createJSONObj) {

        this.timelineFadeOut();
        let _this = this;

        // TODO: REFACTORING THIS PROMIS AND THE CALLING METHODS IN BlormNewPost.vue
        return new Promise (
            function(success, error) {
              axios.post(
                  restapiVars.root+'blormapi/v1/blogpost/create',
                  createJSONObj,
                  {
                    headers: {
                      'Content-Type': 'multipart/form-data',
                      'X-WP-Nonce': restapiVars.nonce,
                    }
                  }
                  ).then(
                    function (response) {_this.checkStatus(response);return response;}
                  )
                  .then(
                      function (response) {
                      // on success, call fullfill method, to resolve
                      success(response);
                  }).finally(
                  function (response) {
                    _this.timelineFadeIn();
                  }).catch(function (response) {
                    error(response);
                    _this.logMsgToCons("postCreate", response);
                    return false;
                });
            }
        );
      }, // postCreate

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

        this.timelineFadeOut();

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

        this.timelineFadeOut();

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

          this.timelineFadeOut();
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
          this.timelineFadeOut();
            let promiseObj = new Promise (function(fullfill, reject) {
                axios.get(restapiVars.root+'blormapi/v1/blogpost/undo/reblog/'+activityId,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restapiVars.nonce,}
                    }
                ).then(function (response) {
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
          this.timelineFadeOut();

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
            ).then(response => {
              jQuery( ".BlormFeed_Action--comment" ).css("opacity","1");
              jQuery( ".BlormFeed_Action--comment button" ).prop('disabled', false);
              // update the feed
              this.loadAccountPage();
            }).catch(error => {
              this.logMsgToCons("postComment error", error);
            });

        },
        getBlormApiData: function (url) {
            this.logMsgToCons("getBlormApiData", url);
            let promiseObj = new Promise( function( fullfill, reject) {
                let _this = this;
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
                    _this.logMsgToCons("getBlormApiData error", error);
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
            return this.getBlormApiData(restapiVars.root+'blormapi/v1/user/follow/blormhandle/'+blormhandle);
        },
        /**
         * userUnFollowing
         * @param blormhandle
         */
        userUnFollowing: function (blormhandle) {
            return this.getBlormApiData(restapiVars.root+'blormapi/v1/user/unfollow/blormhandle/'+blormhandle);
        },
        /**
         * getFollowingUsers
         */
        getFollowingUsers: function(userId) {
            let responsePromise = this.getBlormApiData(restapiVars.root+'blormapi/v1/feed/following/timeline/'+userId);
            responsePromise.then(
                this.handleGetFollowingUsersSuccess,
                this.handleResponseError
            );
        },
        handleGetFollowingUsersSuccess: function (response) {
          this.processUserData(response.data);
          this.$store.commit('setFollowingUsers', this.processUserData(response.data));
        },
        /**
         * getFollowersOfUser
         */
        getFollowersOfUser: function(userId) {
           let responsePromise = this.getBlormApiData(restapiVars.root+'blormapi/v1/feed/followers/user/'+userId);
            responsePromise.then(
                this.handleGetFollowersOfUserSuccess,
                this.handleResponseError
            );
        },
        handleGetFollowersOfUserSuccess: function (response) {
          this.$store.commit('setFollowersOfUser', this.processUserData(response.data));
        },
        processUserData: function(userDatas) {

          let users = [];
          Object.entries(userDatas).forEach( function(userData) {

            // constructor(id, name, blormHandle, photoUrl, websiteName, websiteUrl, websiteId, category)
            // check the input, we only want to have users with full data
            if (userData[0] !== undefined &&
                userData[1].Person.name !== undefined &&
                userData[1].Person.blormhandle !== undefined &&
                userData[1].Person.photo_url !== undefined &&
                userData[1].Organizations[0].name !== undefined &&
                userData[1].Organizations[0].url.Href !== undefined &&
                userData[1].Organizations[0].id !== undefined &&
                userData[1].Organizations[0].category !== undefined &&
                userData[1].Organizations[0].type !== undefined
            ) {
              let user = new blormApi.User(
                  userData[0],
                  userData[1].Person.name,
                  userData[1].Person.blormhandle,
                  userData[1].Person.photo_url,
                  userData[1].Organizations[0].name,
                  userData[1].Organizations[0].url.Href,
                  userData[1].Organizations[0].id,
                  userData[1].Organizations[0].category,
                  userData[1].Organizations[0].type,
              );
              users.push(user);
            }
          }); // usersData loop start

        return users;
      },

      handleResponseError: function (response) {
            this.logMsgToCons("handleResponseError", response);
        },
        isAccountDataOnDisplay: function() {
          //this.logMsgToCons("isAccountDataOnDisplay", (userId === this.$store.state.account.id));
          return (this.$store.state.user.id === this.$store.state.account.id);
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