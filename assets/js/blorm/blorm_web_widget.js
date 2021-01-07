var Xpost = {
    reblogs: ' 1 blog1.de, green.de, super.com, hello.org, special.net, usw.de, zbd.de, xxxx.com, web.de, spiegel.de',
    shares: 'special.net, usw.de, zbd.de, xxxx.com, web.de, spiegel.de',
    comments: 'green.de, super.com, hello.org, special.net'
}

var blormAssets = "assets/";
var blormplus = "<img src=\""+blormAssets+"icons/circle-add-plus-new-outline-stroke.png\" class='blormWidgetImagePlus'>";

var md = new MobileDetect(window.navigator.userAgent);



class blorm_menue_bar {

    constructor(blormPostData) {

        /* setup config */

        // path to the plugin assets
        this.blormAssets = blormapp.postConfig.blormAssets;

        // config option, float the widget to left or right
        this.widgetFloat = blormapp.postConfig.float;

        // a class to wrap the widget for better css styling
        this.classForWidgetPlacement = blormapp.postConfig.classForWidgetPlacement;

        // set the unit for possible position adjustment
        this.positionUnit = "px";
        if (blormapp.postConfig.positionUnit === "unit_px") {
            this.positionUnit = "px";
        }
        if (blormapp.postConfig.positionUnit === "unit_percent") {
            this.positionUnit = "%";
        }

        // blormapp.postConfig.positionTop
        this.positionTop = 0;
        if (blormapp.postConfig.positionTop !== "") {
            this.positionTop = blormapp.postConfig.positionTop;
        }

        this.positionRight = 0;
        if (blormapp.postConfig.positionRight !== "") {
            this.positionRight = blormapp.postConfig.positionRight;
        }

        this.positionBottom = 0;
        if (blormapp.postConfig.positionBottom !== "") {
            this.positionBottom = blormapp.postConfig.positionBottom;
        }

        this.positionLeft = 0;
        if (blormapp.postConfig.positionLeft !== "") {
            this.positionLeft = blormapp.postConfig.positionLeft;
        }

        // config for origin link on widget
        this.OriginWebsiteName = blormPostData.OriginWebsiteName;
        this.OriginWebsiteUrl = blormPostData.OriginWebsiteUrl;

        // get the activity_id for the post
        this.postId = blormPostData.postid;

        // init the post data
        //this.initBlormPostData();
        this.blormPost = blormPostData;

        // prepare social data
        this.setSocialDataCounters();

        // init status of the social data display
        this.SocialStatus = "invisible";

        // now render the widget
        this.RenderContainerMenu();

        // init the mouseover events for the template
        this.InitHandler();

    }

    setSocialDataCounters() {

        this.ReblogedCount = 0;
        if (typeof(this.blormPost.ReblogedCount) != "undefined") {
            this.ReblogedCount = this.blormPost.ReblogedCount;
        }

        this.SharedCount = 0;
        if (typeof(this.blormPost.SharedCount) != "undefined") {
            this.SharedCount = this.blormPost.SharedCount;
        }

        this.CommentsCount = 0;
        if (typeof(this.blormPost.CommentsCount) != "undefined") {
            this.CommentsCount = this.blormPost.CommentsCount;
        }
    }

    setPowerBarContent(ContentType) {

        this.SocialStatus = ContentType;

        let socialData = new Array();
        let ul = document.createElement("ul");
        let li = document.createElement("li");

        switch(ContentType) {
            case "rebloged":
                socialData = this.blormPost.Rebloged;
                li.innerHTML = "This post was not rebloged anywhere. Perhaps you should start spreading it?";
                break;
            case "shared":
                socialData = this.blormPost.Shared;
                li.innerHTML = "Nobody shared it. Its up to you now.";
                break;
            case "comments":
                socialData = this.blormPost.Comments;
                li.innerHTML = "No comments on this post.";
                break;
            case "info":
                li.classList.add("PowerbarContentText");
                li.innerHTML = "Blorm helps connecting publishers to promote each other and the content they love. Learn more about blorm at <a href=\"http://blorm.io\">blorm.io</a>\n";
                break;
        }

        ul.appendChild(li);
        // if there is interacton data build the list of links
        if (typeof(socialData) != "undefined" && socialData.length > 0) {

            this.listcontent = new Array();
            socialData.forEach(function (item, index, arr) {
                this.listcontent[index] = {name:item.user.data.data.website_name,link:item.user.data.data.website_url };
            }, this);
            let li = document.createElement("li");
            li.innerHTML = "Post is " + data +" on:";
            ul.appendChild(li);

            for (content of this.listcontent) {
                let li = document.createElement("li");
                let a = document.createElement('a');
                a.href = content.link;
                a.innerHTML = content.name;
                li.appendChild(a);
                ul.appendChild(li);
            }
        }

        let c = this.PowerbarContent.firstChild;
        if ( c != null) {
            this.PowerbarContent.removeChild(c);
        }

        this.PowerbarContent.appendChild(ul);
    }

    setPowerBarPosition() {

        this.Powerbar.style.display = "inline";
        let h = this.PowerbarContent.scrollHeight;

        this.Powerbar.style.height = h + 1 + "px";
        this.Powerbar.style.top =  "-" + h + "px";
        this.PowerbarContent.style.backgroundColor = "#000";
        this.PowerbarContent.style.color = "#fff";

    }

    InitHandler() {
        console.log("init handler");
        // mouse event for the powerbar when mouse on icons
        this.handlePlusSocialBars = this.blormWidget.getElementsByClassName("blormWidgetPlusSocialBarEventHandler");
        let SocialBar;
        let _this = this;

        this.handleLayerRebloged = this.ContainerDisplay.getElementsByClassName("blormWidgetPlusSocialBarRebloged")[0];
        this.handleLayerShared = this.ContainerDisplay.getElementsByClassName("blormWidgetPlusSocialBarShared")[0];
        this.handleLayerComments = this.ContainerDisplay.getElementsByClassName("blormWidgetPlusSocialBarComments")[0];
        this.handleLayerLogo = this.ContainerDisplay.getElementsByClassName("blormWidgetPlusLogoIcon")[0];

        if (md.mobile() == null) {

            for (SocialBar of this.handlePlusSocialBars) {
                SocialBar.addEventListener(
                    "mouseout",
                    function () {
                        _this.Powerbar.style.display = "none";
                    },
                    false
                );
            }

            // keep the powerbar visible as long we use it
            this.Powerbar.addEventListener(
                "mouseover",
                function () {
                    _this.Powerbar.style.display = "inline";
                    _this.setPowerBarPosition();
                },
                false
            );

            this.Powerbar.addEventListener(
                "mouseout",
                function () {
                    _this.Powerbar.style.display = "none";
                },
                false
            );

            this.handleLayerRebloged.addEventListener(
                "mouseover",
                function () {
                    _this.setPowerBarContent("rebloged");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerShared.addEventListener(
                "mouseover",
                function () {
                    _this.setPowerBarContent("shared");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerComments.addEventListener(
                "mouseover",
                function () {
                    _this.setPowerBarContent("comments");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerLogo.addEventListener(
                "mouseover",
                function () {
                    _this.setPowerBarContent("info");
                    _this.setPowerBarPosition();
                    _this.BlormWidgetPlusLogoIconImg.src = _this.blormAssets+"/images/blorm_icon_black_3.png";
                },
                true
            );

            this.handleLayerLogo.addEventListener(
                "mouseout",
                function () {
                    _this.BlormWidgetPlusLogoIconImg.src = _this.blormAssets+"/images/blorm_icon_black_1.png";
                },
                true
            );

            console.log("init handler finished");
        }

        if (md.mobile() !== null) {

            this.handleLayerLogo.addEventListener(
                "click",
                function () {
                    if ( _this.BlormWidgetPlusSocial.getAttribute("style") === "display:none" ) {
                        _this.BlormWidgetPlusSocial.setAttribute("style","display:inline");
                        _this.BlormWidgetPowerText.setAttribute("style","display:inline");
                        _this.BlormWidgetPlusBlormInfo.setAttribute("style","display:inline");
                        _this.BlormWidgetPlus.classList.add("BorderBottom");
                        _this.BlormWidgetPlusLogoIconImg.src = _this.blormAssets+"/images/blorm_icon_black_3.png";
                    } else {
                        _this.BlormWidgetPlusSocial.setAttribute("style","display:none");
                        _this.BlormWidgetPowerText.setAttribute("style","display:none");
                        _this.BlormWidgetPlusBlormInfo.setAttribute("style","display:none");
                        _this.BlormWidgetPlus.classList.remove("BorderBottom");
                        _this.BlormWidgetPlusLogoIconImg.src = _this.blormAssets+"/images/blorm_icon_black_1.png";

                        if ( _this.SocialStatus !== "invisible") {
                            _this.Powerbar.style.display = "none";
                        }
                    }
                },
                true
            );

            this.handleLayerRebloged.addEventListener(
                "click",
                function () {
                    if ( _this.SocialStatus === "rebloged") {
                        _this.Powerbar.style.display = "none";
                        _this.SocialStatus = "invisible";
                        return;
                    }
                    _this.setPowerBarContent("rebloged");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerShared.addEventListener(
                "click",
                function () {
                    if ( _this.SocialStatus === "shared") {
                        _this.Powerbar.style.display = "none";
                        _this.SocialStatus = "invisible";
                        return;
                    }
                    _this.setPowerBarContent("shared");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerComments.addEventListener(
                "click",
                function () {
                    if ( _this.SocialStatus === "comments") {
                        _this.Powerbar.style.display = "none";
                        _this.SocialStatus = "invisible";
                        return;
                    }
                    _this.setPowerBarContent("comments");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.BlormWidgetPlusBlormInfo.addEventListener(
                "click",
                function () {
                    if ( _this.SocialStatus === "info") {
                        _this.Powerbar.style.display = "none";
                        _this.SocialStatus = "invisible";
                        return;
                    }
                    _this.setPowerBarContent("info");
                    _this.setPowerBarPosition();

                },
                true
            );

        }
    }

    RenderContainerMenu() {

        // create the html widget
        this.blormWidget = document.createElement("div");
        this.blormWidget.className = "blormWidget";

        // the menue is very all the magic comes together
        this.ContainerMenu = document.createElement("div");
        this.ContainerMenu.className = "blormWidgetContainerMenu";

        /*
        the ContainerMenu depends on two elements:
        1. the powerbar is capable for showing the social interactions:
        blogs reblogged the posts, userhandle commenting it, or users who shared the post on blorm
        the powerbar only appears on mouseover

        2. the display part:
        this is the user-menue showing the origin source of the post, presenting the numbers and the icons
        the powerbar ist visible for users
         */

        /* powerbar plus content */
        // we need a container for the content(list of sharing publishers and comments)
        this.PowerbarContent = document.createElement("div");
        this.PowerbarContent.classList.add("blormWidgetPowerbarContent");

        // we need a powerbar and append the content to
        this.Powerbar = document.createElement("div");
        this.Powerbar.classList.add("blormWidgetPowerbar");
        this.Powerbar.appendChild(this.PowerbarContent);

        // add the powerbar to the menue
        this.ContainerMenu.appendChild(this.Powerbar);


        this.ContainerDisplay = document.createElement("div");
        this.ContainerDisplay.classList.add("blormWidgetContainerDisplay");

        // display has two parts the LogoIcon and the SocialIcons list
        this.BlormWidgetPlusLogoIcon = document.createElement("div");
        this.BlormWidgetPlusLogoIcon.classList.add("blormWidgetPlusLogoIcon");
        this.BlormWidgetPlusLogoIcon.classList.add("blormWidgetPlusSocialBarEventHandler");

        this.BlormWidgetPlusLogoIconImg = document.createElement("img");
        this.BlormWidgetPlusLogoIconImg.classList.add("blormWidgetPlusLogoIconImg");
        this.BlormWidgetPlusLogoIconImg.src = this.blormAssets + "/images/blorm_icon_black_1.png";
        //const markupContainerDisplayPlusLogoIcon = ;
        //this.BlormWidgetPlusLogoIcon.innerHTML = `<img src="${this.blormAssets}/images/blorm_icon_black_1.png" class="blormWidgetPlusLogoIconImg">`;
        this.BlormWidgetPlusLogoIcon.append(this.BlormWidgetPlusLogoIconImg);

        // display has two parts the LogoIcon and the SocialIcons list
        this.BlormWidgetPlusBlormInfo = document.createElement("div");
        this.BlormWidgetPlusBlormInfo.classList.add("blormWidgetPlusBlormInfo");
        this.BlormWidgetPlusBlormInfo.classList.add("blormWidgetPlusSocialBarEventHandler");
        let infoUl = document.createElement("ul");
        let infoUlLi = document.createElement("li");
        infoUlLi.innerHTML = `<img src="${this.blormAssets}/icons/circle-info-more-information-detail-glyph.png" class="blormWidgetPlusLogoIconImg">`;
        infoUl.append(infoUlLi);
        this.BlormWidgetPlusBlormInfo.append(infoUl);

        const markupContainerDisplayPlusSocial = `
                                <ul class="blormWidgetPlusSocialBar">
                                    <li class="blormWidgetPlusSocialBarIcon blormWidgetPlusSocialBarEventHandler blormWidgetPlusSocialBarRebloged">
                                        <img src="${this.blormAssets}/icons/editor-copy-2-duplicate-glyph.png" alt="reblogged" >
                                    </li>
                                    <li class="blormWidgetPlusSocialBarText">
                                        <span class="blormWidgetPlusSocialBarRebloggedCount">${this.ReblogedCount}</span>
                                    </li>
                                    <li class="blormWidgetPlusSocialBarIcon blormWidgetPlusSocialBarEventHandler blormWidgetPlusSocialBarShared">
                                        <img src="${this.blormAssets}/icons/circle-sync-backup-2-glyph.png" alt="shared" >
                                    </li>
                                    <li class="blormWidgetPlusSocialBarText">
                                        <span class="blormWidgetPlusSocialBarSharedCount">${this.SharedCount}</span>
                                    </li>
                                    <li class="blormWidgetPlusSocialBarIcon blormWidgetPlusSocialBarEventHandler blormWidgetPlusSocialBarComments">
                                        <img src="${this.blormAssets}/icons/other-review-comment-glyph.png" alt="comments">
                                    </li>
                                    <li class="blormWidgetPlusSocialBarText">
                                        <span class="blormWidgetPlusSocialBarCommentsCount">${this.CommentsCount}</span>
                                    </li>
                                </ul>
                            `;

        this.BlormWidgetPlusSocial = document.createElement("div");
        this.BlormWidgetPlusSocial.classList.add("blormWidgetPlusSocial");
        this.BlormWidgetPlusSocial.innerHTML = markupContainerDisplayPlusSocial;


        this.BlormWidgetPlus = document.createElement("div");
        this.BlormWidgetPlus.classList.add("blormWidgetPlus");

        this.BlormWidgetPlus.appendChild(this.BlormWidgetPlusLogoIcon);

        if (md.mobile() !== null) {
            this.BlormWidgetPlus.appendChild(this.BlormWidgetPlusBlormInfo);
        }
        this.BlormWidgetPlus.appendChild(this.BlormWidgetPlusSocial);

        let clearDiv = document.createElement("div");
        clearDiv.setAttribute("style", "clear:both");

        this.BlormWidgetPlus.appendChild(clearDiv);

        this.ContainerDisplay.append(this.BlormWidgetPlus)


        this.BlormWidgetPowerText = document.createElement("div");
        this.BlormWidgetPowerText.classList.add("blormWidgetPowerText");
        let originWebsiteLink = document.createElement("a");
        if (typeof(this.OriginWebsiteName) !== "undefined") {

        console.log(this.OriginWebsiteName);
        originWebsiteLink.href = this.OriginWebsiteUrl;
        originWebsiteLink.innerText = this.OriginWebsiteName;
        this.BlormWidgetPowerText.append(originWebsiteLink);
        }
        this.ContainerDisplay.appendChild(this.BlormWidgetPowerText);


        /* put it all together */
        this.ContainerMenu.appendChild(this.ContainerDisplay);

        /* a wraper box to float the menue left or right */
        this.ContainerMenuBox = document.createElement("div");
        if (this.widgetFloat === "float_left") {
            this.ContainerMenuBox.classList.add("FloatLeft");
            this.BlormWidgetPlus.classList.add("FloatLeft");
            this.BlormWidgetPlusLogoIcon.classList.add("FloatLeft");
            this.BlormWidgetPowerText.classList.add("FloatLeft");
            this.BlormWidgetPowerText.classList.add("AlignLeft");
            this.Powerbar.classList.add("PositionLeft");
        }

        if (this.widgetFloat === "float_right") {
            this.ContainerMenuBox.classList.add("FloatRight");
            this.BlormWidgetPlus.classList.add("FloatRight");
            this.BlormWidgetPlusLogoIcon.classList.add("FloatRight");
            this.BlormWidgetPowerText.classList.add("FloatRight");
            this.BlormWidgetPowerText.classList.add("AlignRight");
            this.Powerbar.classList.add("PositionRight");
        }

        console.log( "md.mobile():" );
        if ( md.mobile() !== null) {
            console.log( md.mobile() );
            this.BlormWidgetPlusSocial.setAttribute("style","display:none");
            this.BlormWidgetPowerText.setAttribute("style","display:none");
            this.BlormWidgetPlusBlormInfo.setAttribute("style","display:none");
        } else {
            this.BlormWidgetPlus.classList.add("BorderBottom");
        }

        // append the menu to the wrapper
        this.ContainerMenuBox.appendChild(this.ContainerMenu);

        // prepare the widget
        this.blormWidget.appendChild(this.ContainerMenuBox);
        console.log(this.blormWidget);
    }

    GetWidget() {
        this.setPosition(this.ContainerMenu);
        if (this.classForWidgetPlacement !== "") {
            let blormWidgetClassBox = document.createElement("div");
            blormWidgetClassBox.className = this.classForWidgetPlacement;
            blormWidgetClassBox.append(this.ContainerMenuBox);
            return blormWidgetClassBox;
        }
        return this.blormWidget;
    }

    GetWidgetClassBoxed(ClassName) {

        this.setPosition(this.ContainerMenu);
        let ClassBox = document.createElement("div");
        ClassBox.className = ClassName;
        ClassBox.innerHTML = this.blormWidget.outerHTML;
        return ClassBox;
    }

    GetMenue() {
        this.setPosition(this.ContainerMenu);
        if (this.classForWidgetPlacement !== "") {
            let ContainerMenuClassBox = document.createElement("div");
            ContainerMenuClassBox.className = this.classForWidgetPlacement;
            ContainerMenuClassBox.append(this.ContainerMenuBox);
            return ContainerMenuClassBox;
        }
        return this.ContainerMenuBox;
    }

    GetMenueClassBoxed(ClassName) {
        this.setPosition(this.ContainerMenu);
        let ClassBox = document.createElement("div");
        ClassBox.className = ClassName;
        ClassBox.append(this.ContainerMenuBox);
        return ClassBox;
    }

    setPosition(element) {
        if (this.positionTop !== 0) {
            let x = 0 - this.positionTop;
            element.style.marginTop = x + this.positionUnit;
        }
        if (this.positionRight !== 0) {
            let x = 0 - this.positionRight;
            element.style.marginRight = x + this.positionUnit;
        }
        if (this.positionBottom !== 0) {
            let x = 0 - this.positionBottom;
            element.style.marginBottom = x + this.positionUnit;
        }
        if (this.positionLeft !== 0) {
            let x = 0 - this.positionLeft;
            element.style.marginLeft = x + this.positionUnit;
        }
    }

}; // end blorm class


function getPostById(id) {

    let post = {};

    if (typeof blormapp.reblogedPosts[id] !== 'undefined') {
        post = blormapp.reblogedPosts[id];
    }

    if (typeof blormapp.blormPosts[id] !== 'undefined') {
        post = blormapp.blormPosts[id];
    }

    return post;
}

var reblogged = 32;
var shared = 3;
var comments = 7;
document.addEventListener("DOMContentLoaded", function() {

    console.log("web-app init");

    // get all rebloged posts on the page
    /*var allReblogedPosts = document.getElementsByClassName("blorm-rebloged");

    Array.from(allReblogedPosts).forEach(function(ReblogedPost){
        console.log(ReblogedPost);
        let id = ReblogedPost.id.split("-")[1];
        blormMenuBar = new blorm_menue_bar(blormapp.reblogedPosts[id])


        // ReblogedPost.appendChild(blormMenuBar.GetWidgetClassBoxed("entry-content"));


        /* standard content elements "entry-content" */
    /*contentWraper = ReblogedPost.getElementsByClassName("blorm-reblog-post-data");
    contentWraper[0].parentNode.insertBefore(blormMenuBar.GetWidget(), contentWraper[0].nextSibling);

    headerBlock = ReblogedPost.getElementsByClassName("entry-header");
    headerBlock[0].parentNode.insertBefore(blormMenuBar.GetWidgetClassBoxed("entry-content"), headerBlock[0].nextSibling);

    /*contentBlock = ReblogedPost.getElementsByClassName("entry-content");
    console.log(contentBlock[0].innerHTML);
    temp = contentBlock[0].innerHTML;
    console.log(blormMenuBar.GetWidget().outerHTML);

    contentBlock[0].innerHTML = temp + blormMenuBar.GetWidget().outerHTML;
    //contentBlock[0].parentNode.insertBefore(blormMenuBar.GetWidget(), contentBlock[0].nextSibling);

    // footer block
    footerBlock = ReblogedPost.getElementsByClassName("entry-footer");
    footerBlock[0].parentNode.insertBefore(blormMenuBar.GetWidget(), footerBlock[0].nextSibling);

});*/

    var allBlormWidgets = document.getElementsByClassName("blormWidget");

    Array.from(allBlormWidgets).forEach(function(BlormWidget){
        console.log(BlormWidget);

        let id = BlormWidget.dataset.postid;
        post = getPostById(id);
        console.log(post);
        if (Object.keys(post).length !== 0) {
            blormMenuBar = new blorm_menue_bar(post)
            //console.log(blormMenuBar);
            BlormWidget.appendChild(blormMenuBar.GetMenue());
        }
    });
});