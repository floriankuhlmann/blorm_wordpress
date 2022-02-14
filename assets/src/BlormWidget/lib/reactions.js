import * as $ from 'jquery';
import 'bootstrap';
import * as logo from './logo.js';

export function CreateBlormWidgetReactionBar(blormPost) {

    let BlormWidgetReactionBar = document.createElement("div");
    BlormWidgetReactionBar.classList.add("blormWidgetSocialReactions");

    // setup reblog reactions
    BlormWidgetReactionBar.append(createReactionbarNode(
        blormPost.ReblogedCount,
        'filter_none',
        generateReactionContent( blormPost, "rebloged"),
        "Post was also published here:"
    ));

    // setup shared reactions
    BlormWidgetReactionBar.append(createReactionbarNode(
        blormPost.SharedCount,
        'sync',
        generateReactionContent( blormPost, "shared"),
        "Post was shared on the network:"
    ));

    // setup comments
    BlormWidgetReactionBar.append(createReactionbarNode(
        blormPost.CommentsCount,
        'chat',
        generateReactionContent( blormPost, "comments"),
        "Discussed by blormusers:"
    ));
    BlormWidgetReactionBar.appendChild(logo.getLogo());
    return BlormWidgetReactionBar;
}

function createReactionbarNode(count, icon, content, headline) {

    let nodeDiv = document.createElement("div");
    nodeDiv.classList.add("blormWidgetSocialReactionsElement");
    let reactionsCount = 0;

    if (typeof(count) != "undefined") {
        reactionsCount = count;
    }

    const markupReactionbarNode = `
        <span class="material-icons">${icon}</span>
        <span>${reactionsCount}</span>
    `;

    nodeDiv.innerHTML = markupReactionbarNode;

    $(nodeDiv).popover(
        {
            title: "<b>"+headline+"</b>",
            content: content,
            html: true,
            placement: "top",
            animation: true,
            trigger: 'click'
        }
    );

    $(nodeDiv).on('shown.bs.popover', function(){
        nodeDiv.classList.add("blormWidgetSocialReactionsElementActive");
    });
    $(nodeDiv).on('hide.bs.popover', function(){
        nodeDiv.classList.remove("blormWidgetSocialReactionsElementActive");
    });

    return nodeDiv;
}



function generateReactionContent(blormPost, ReactionsType) {

    let socialData = [];
    let div = document.createElement("div");
    let span = document.createElement("span");

    switch(ReactionsType) {
        case "rebloged":
            socialData = blormPost.Rebloged;
            span.innerHTML = "This post was not rebloged anywhere. Perhaps you should start spreading it?";
            break;
        case "shared":
            socialData = blormPost.Shared;
            span.innerHTML = "Nobody shared it. Its up to you now.";
            break;
        case "comments":
            socialData = blormPost.Comments;
            span.innerHTML = "No comments on this post.";
            break;
        case "info":
            span.classList.add("PowerbarContentText");
            span.innerHTML = "Blorm helps connecting publishers to promote each other and the content they love. Learn more about blorm at <a href=\"http://blorm.io\">blorm.io</a>\n";
            break;
    }

    // if there is interacton data build the list of links
    if (typeof(socialData) != "undefined" && socialData.length > 0) {

        let listcontent = new Array();
        socialData.forEach(function (item, index, arr) {
            if (item.kind === "reblog") {
                listcontent[index] = {name:item.data.publisher.Name, link:item.data.publisher.Url };
            }
            if (item.kind === "share") {
                listcontent[index] = {name:item.data.publisher.Name, link:item.data.publisher.Url };
            }
            if (item.kind === "comment") {
                listcontent[index] = {name:item.user.data.data.blormhandle, link: "https://blorm.io/" };
            }
        });
        let i = 0;
        for (let content of listcontent) {
            i++;
            let el = document.createElement("span");
            let a = document.createElement('a');
            a.classList.add("blormWidgetContentLink");
            a.href = content.link;
            a.innerHTML = content.name;
            el.appendChild(a);
            div.appendChild(el);
            console.log(listcontent.length);
            if (i < listcontent.length) {
                let elb = document.createElement("span");
                elb.classList.add("blormWidgetContentLinkBreak");
                elb.innerHTML = "|";
                div.appendChild(elb);
            }

        }
    } else {
        div.appendChild(span);
    }

    return div;
}





