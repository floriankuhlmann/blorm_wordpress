document.addEventListener("DOMContentLoaded", function() {

    function insertAfter(newNode, existingNode) {
        existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    }

    console.log("web-app init: mode content");

    let BlormPosts = blormapp.getAllBlormPosts();

    Array.from(BlormPosts).forEach(function(BlormPost){

        // the 'blorm-post-data'-container holds the relevant postdata we need to connect with the remote data
        let BlormWidget = BlormPost.getElementsByClassName("blormWidget")[0];
        if (typeof BlormWidget !== "undefined") {
            let postId = BlormWidget.dataset.postid;
            let postData = blormapp.getPostById(postId);

            if (Object.keys(postData).length !== 0) {
                blormMenuBar = new blorm_menue_bar(postData);
                if (BlormWidget.parentNode.getAttribute('href') !== null) {
                    insertAfter(blormMenuBar.GetWidget(), BlormWidget.parentNode);
                } else {
                    BlormWidget.appendChild(blormMenuBar.GetMenue());
                    console.log("blorm | initialized post id:" + postData.PostId + " for website: "+postData.OriginWebsiteName);
                }
            }
        }
    });

});


