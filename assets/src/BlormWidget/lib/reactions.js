import * as $ from 'jquery';
import { createPopper } from '@popperjs/core';
import * as logo from './logo.js';

export function CreateBlormWidgetReactionBar(blormPost) {

    // the container for all reactions + logo
    let BlormWidgetReactionBar = document.createElement("div");
    BlormWidgetReactionBar.classList.add("blormWidgetSocialReactions");

    // setup reblog reactions
    BlormWidgetReactionBar.append(createReactionbarNode(
        blormPost.ReblogedCount,
        'filter_none',
        blormPost,
        "rebloged",
        "Post was also published here:"
    ));

    // setup shared reactions
    BlormWidgetReactionBar.append(createReactionbarNode(
        blormPost.SharedCount,
        'sync',
        blormPost,
        "shared",
        "Post was shared on the network:",
    ));

    // setup comments
    BlormWidgetReactionBar.append(createReactionbarNode(
        blormPost.CommentsCount,
        'chat',
        blormPost,
        "comments",
        "Discussed by blormusers:",
    ));
    BlormWidgetReactionBar.append(logo.getLogo());
    return BlormWidgetReactionBar;
}

function createReactionbarNode(count, icon, blormPost, ReactionsType, headLineText) {

    // a single container for a div with reactions + reactions count and a div with the tooltip
    let blormWidgetReactionContainer = document.createElement("div");
    blormWidgetReactionContainer.classList.add("blormWidgetSocialReactionsContainer");

    // build the element for social reactions
    let blormWidgetSocialReactionsElement = document.createElement("div");
    blormWidgetSocialReactionsElement.classList.add("blormWidgetSocialReactionsContainerElement");

    let reactionsCount = 0;
    if (typeof(count) != "undefined") {
        reactionsCount = count;
    }

    const markupReactionsElement = `
        <span class="material-icons">${icon}</span>
        <span>${reactionsCount}</span>
    `;

    blormWidgetSocialReactionsElement.innerHTML = markupReactionsElement;
    // add the element for social reactions to the container
    blormWidgetReactionContainer.appendChild(blormWidgetSocialReactionsElement);

    // generate the tooltip element (init with display = none)
    let blormWidgetToolTipElement = generateReactionContent( blormPost, ReactionsType, headLineText );
    blormWidgetReactionContainer.appendChild(blormWidgetToolTipElement);

    // create the Popper wit the tooltip
    let popperInstance = createPopper(
        blormWidgetSocialReactionsElement,
        blormWidgetToolTipElement,
        {
            placement: 'top',
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 5],
                    },
                },
            ],
        });

    const showEvents = ['click'];

    showEvents.forEach((event) => {
        blormWidgetSocialReactionsElement.addEventListener(event,function () {
            blormWidgetElementToggleFunction(blormWidgetToolTipElement, popperInstance, blormWidgetSocialReactionsElement);
        });
    });

    return blormWidgetReactionContainer;
}

function blormWidgetElementToggleFunction(blormWidgetToolTipElement, popperInstance, blormWidgetSocialReactionsElement) {

    if (blormWidgetToolTipElement.getAttribute('data-show') === "true" ) {
        blormWidgetToolTipElement.removeAttribute('data-show');
        blormWidgetSocialReactionsElement.classList.remove("blormWidgetSocialReactionsElementActive");
        return;
    }

    blormWidgetToolTipElement.setAttribute('data-show', 'true');
    blormWidgetSocialReactionsElement.classList.add("blormWidgetSocialReactionsElementActive");
    // We need to tell Popper to update the tooltip position
    // after we show the tooltip, otherwise it will be incorrect
    popperInstance.update();
}



function generateReactionContent(blormPost, ReactionsType, headLineText) {

    let socialData = [];

    // create the tooltip
    let toolTip = document.createElement("div");
    toolTip.classList.add("blormWidgetTooltip");
    toolTip.setAttribute("role", "tooltip");

    // create the tooltip headline
    let toolTipHeadline = document.createElement("div");
    toolTipHeadline.classList.add("blormWidgetTooltipHeadline");
    toolTipHeadline.innerHTML = headLineText;

    // create the tooltip content
    let toolTipContent = document.createElement("div");
    toolTipContent.classList.add("blormWidgetTooltipContent");
    let contentElement = document.createElement("span");

    switch(ReactionsType) {
        case "rebloged":
            socialData = blormPost.Rebloged;
            contentElement.innerHTML = "This post was not rebloged anywhere. Perhaps you should start spreading it?";
            break;
        case "shared":
            socialData = blormPost.Shared;
            contentElement.innerHTML = "Nobody shared it. Its up to you now.";
            break;
        case "comments":
            socialData = blormPost.Comments;
            contentElement.innerHTML = "No comments on this post.";
            break;
        case "info":
            contentElement.classList.add("PowerbarContentText");
            contentElement.innerHTML = "Blorm helps connecting publishers to promote each other and the content they love. Learn more about blorm at <a href='http://blorm.io'>blorm.io</a>\n";
            break;
    }

    // if there is interacton data build the list of links
    if (typeof(socialData) != "undefined" && socialData.length > 0) {

        // prepare an arary with url + name
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

        // build a spans with url and names inside
        let i = 0;
        for (let content of listcontent) {
            i++;

            // create the link in a span
            let el = document.createElement("span");
            let a = document.createElement('a');
            a.classList.add("blormWidgetContentLink");
            a.href = content.link;
            a.innerHTML = content.name;
            el.appendChild(a);
            toolTipContent.appendChild(el);

            // generate a '|' separater between the links
            if (i < listcontent.length) {
                let elb = document.createElement("span");
                elb.classList.add("blormWidgetContentLinkBreak");
                elb.innerHTML = "|";
                toolTipContent.appendChild(elb);
            }
        }
    } else {
        toolTipContent.appendChild(contentElement);
    }

    // put the tooltip together
    toolTip.appendChild(toolTipHeadline);
    toolTip.appendChild(toolTipContent);
    return toolTip;
}





