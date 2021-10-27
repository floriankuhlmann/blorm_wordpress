
document.addEventListener("DOMContentLoaded", function() {

    function insertAfter(newNode, existingNode) {
        existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    }

    console.log("web-app init");

    // if blorm_display_widget()
    var allBlormWidgetPosts = document.getElementsByClassName("blormWidget-template-tag");
    /*Array.from(allBlormWidgetPosts).forEach(function(BlormWidget) {
        let id = BlormWidget.dataset.postid;
        //let postData = getPostById(id);
        //if (Object.keys(postData).length !== 0) {
            blormMenuBar = new blorm_menue_bar(id);
            if (BlormWidget.parentNode.getAttribute('href') !== null) {
                insertAfter(blormMenuBar.GetWidget(),BlormWidget.parentNode);
            } else {
                BlormWidget.appendChild(blormMenuBar.GetMenue());
            }
        //}
    });*/


    // reblogged posts are custom post type 'blormpost' and can be dicovered by the css class type-blormpost
    var allBlormPosts = document.getElementsByClassName("type-blormpost");
    Array.from(allBlormPosts).forEach(function(BlormPost){

        // the 'blorm-post-data'-container holds the relevant postdata we need to connect with the remote data
        let BlormPostContainer = BlormPost.getElementsByClassName("blorm-post-data")[0];
        let postId = BlormPostContainer.dataset.postid;
        //let postData = getPostById(id);

        console.log("__ start init post type-blormpost id:" + postData.PostId + " for website: "+postData.OriginWebsiteName);


        // exchange all links in the blorm posts
        /*if (Object.keys(postData).length !== 0) {
            // if the post is a reblog and not a shared post we want to change the urls to the origin post
            if (BlormPost.classList.contains("blorm-rebloged")) {
                let BlormPostLinks = BlormPost.getElementsByTagName('a');
                if (BlormPostLinks.length > 0) {
                    Array.from(BlormPostLinks).forEach(function (BlormPostLink) {
                        if (!BlormPostContainer.classList.contains("post-thumbnail")) {
                            BlormPostLink.href = postData.TeaserUrl;
                        }
                    });
                }
            }
        }*/

        // integrate the widget in the posts. first way put the widget on the image
        if (BlormPost.classList.contains("blormwidget-on-image-post")) {
            //if (Object.keys(postData).length !== 0) {
            // this is the menue bar inside the image container
            blormMenuBar = new blorm_menue_bar(postId)

            if( BlormPost.getElementsByTagName('img').length > 0) {
                // there is an image
                // img element that will be wrapped
                var imgEl = BlormPost.getElementsByTagName('img')[0];
                blormMenuBar.AddMenueToImage(imgEl);
                //return;
            }
            //}
        };

        // second possibility add the widget to the content
        if (BlormPost.classList.contains("blormwidget-add-to-content")) {

            var allBlormWidgets = BlormPost.getElementsByClassName("blormWidget");
            Array.from(allBlormWidgets).forEach(function(BlormWidget){
                if (Object.keys(postData).length !== 0) {
                    blormMenuBar = new blorm_menue_bar(postData);
                    if (BlormWidget.parentNode.getAttribute('href') !== null) {
                        insertAfter(blormMenuBar.GetWidget(),BlormWidget.parentNode);
                    } else {
                        BlormWidget.appendChild(blormMenuBar.GetMenue());
                    }
                }
            }, postData);
        };

        console.log("_ finished init post type-blormpost id:" + postData.PostId + " for website: "+postData.OriginWebsiteName);

    });

    // reblogged posts are custom post type 'blormpost' and can be dicovered by the css class type-blormpost
    var allBlormPosts = document.getElementsByClassName("blorm-shared");
    Array.from(allBlormPosts).forEach(function(BlormPost){

        if ( BlormPost.getElementsByClassName("blorm-post-data")[0] !== null ) {
            let BlormPostContainer = BlormPost.getElementsByClassName("blorm-post-data")[0];
            let id = BlormPostContainer.dataset.postid;
            let postData = getPostById(id);

            console.log("__ start init post blorm-shared id:" + postData.PostId + " for website: "+postData.OriginWebsiteName);

            // integrate the widget in the posts. first way put the widget on the image
            if (BlormPost.classList.contains("blormwidget-on-image-post")) {
                // the container holds the data
                let BlormPostContainer = BlormPost.getElementsByClassName("blorm-post-data")[0];
                let id = BlormPostContainer.dataset.postid;
                post = getPostById(id);

                if (Object.keys(post).length !== 0) {

                    // this is the menue bar inside the image container
                    blormMenuBar = new blorm_menue_bar(post)

                    if( BlormPost.getElementsByTagName('img').length > 0) {
                        // there is an image

                        // img element that will be wrapped
                        var imgEl = BlormPost.getElementsByTagName('img')[0];
                        blormMenuBar.AddMenueToImage(imgEl);
                    }
                }
            };

            // second possibility add the widget to the content
            if (BlormPost.classList.contains("blormwidget-add-to-content")) {
                var allBlormWidgets = BlormPost.getElementsByClassName("blormWidget");
                Array.from(allBlormWidgets).forEach(function(BlormWidget){
                    if (Object.keys(postData).length !== 0) {
                        blormMenuBar = new blorm_menue_bar(postData);
                        if (BlormWidget.parentNode.getAttribute('href') !== null) {
                            insertAfter(blormMenuBar.GetWidget(),BlormWidget.parentNode);
                        } else {
                            BlormWidget.appendChild(blormMenuBar.GetMenue());
                        }

                    }
                }, postData);
            };

            console.log("_ finished init post blorm-shared id:" + postData.PostId + " for website: "+postData.OriginWebsiteName);

        }
    });
});


