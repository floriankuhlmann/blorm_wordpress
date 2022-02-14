import * as reactions from './reactions.js';
import * as origin from './origin.js';


export function GetBlormWidgetContainer(blormPost) {

    let blormWidgetContainer = document.createElement("div");
    blormWidgetContainer.classList.add("blormWidgetContainer");
    blormWidgetContainer.appendChild(GetBlormWidgetContainerMenu(blormPost));

    return blormWidgetContainer;

}

/*
**
 */
export function GetBlormWidgetContainerMenu(blormPost) {

    console.log("render post: "+blormPost.PostId);

    let blormWidgetContainerMenu = document.createElement("div");
    blormWidgetContainerMenu.classList.add("blormWidgetContainerMenu");

    if (blormapp.postConfig.float === "float_right" || blormapp.postConfig.float === "") {
        blormWidgetContainerMenu.classList.add("alignRight");
    }

    if (blormapp.postConfig.float === "float_left") {
        blormWidgetContainerMenu.classList.add("alignLeft");
    }

    blormWidgetContainerMenu.appendChild(reactions.CreateBlormWidgetReactionBar(blormPost));
    if ( typeof blormPost.OriginWebsiteName !== 'undefined' && typeof blormPost.OriginWebsiteUrl !== 'undefined') {
        blormWidgetContainerMenu.appendChild(origin.GetOriginUrl(blormPost));
    }

    return SetPosition(blormWidgetContainerMenu,blormapp.postConfig);
}


export function AddMenueToImage(imgEl, blormWidgetContainer) {

    // we want to put the thumbnail link on the image INSIDE our div. so we save it here for later usage (end of function)
    // this is little bit annyoing but needed to mak the blorm widget work for mobile click events
    let imgElOrigLink = imgEl.parentNode;
    let imgElOrigLinkHref = imgElOrigLink.getAttribute('href');

    // new image wrapper div
    let divWrapper = document.createElement('div');
    divWrapper.classList.add("blormWidgetImageWrapper");

    // insert the wrapper before the image + put image in the wrapper
    imgEl.parentNode.insertBefore(divWrapper, imgEl);
    divWrapper.appendChild(imgEl);

    // the div layer for the blormwidget with the menue
    let divLayerWidget = document.createElement('div');
    divLayerWidget.classList.add("blormWidgetImagelayerWidget");

    if (blormapp.postConfig.float === "float_right" || blormapp.postConfig.float === "") {
        divLayerWidget.classList.add("alignRight");
    }

    if (blormapp.postConfig.float === "float_left") {
        divLayerWidget.classList.add("alignLeft");
    }

    // TODO: CHECK SIZE OF  blormWidgetImageWrapper if smaller 300 add small class 100% width
    if (divWrapper.offsetWidth < 400) {
        divLayerWidget.classList.add("size10050");
    }
    if (divWrapper.offsetWidth >= 400) {
        divLayerWidget.classList.add("size5050");
    }
    console.log("divWrapper.offsetWidth");
    console.log(divWrapper.offsetWidth);

    /* get the menue widget */
    divLayerWidget.append(blormWidgetContainer);

    let divLayerBlormIconImg = document.createElement('img');
    divLayerBlormIconImg.src = blormapp.postConfig.blormAssets + "/images/blorm_icon_network.png";
    divLayerBlormIconImg.classList.add("blormWidgetImagelayerBlormIconImg");

    // blorm icon on the top corner of the image
    let divLayerBlormIcon = document.createElement('div');
    divLayerBlormIcon.classList.add("blormWidgetImagelayerBlormIcon");
    divLayerBlormIcon.classList.add("topleft");
    divLayerBlormIcon.append(divLayerBlormIconImg);

    // check if there is a link on the image. if not everything ist fine ans easy
    if (imgElOrigLinkHref == null) {
        imgEl.parentNode.insertBefore(divLayerWidget, imgEl.nextSibling);
        imgEl.parentNode.insertBefore(divLayerBlormIcon, imgEl.nextSibling);
        // if there is a link on the image we have to modify a little bit so the link is not laying over the widget
    } else {
        // we rebuild the links on the images and layers to prevent the link from laying over the blorm widget what would cause problems on mobile click events
        let imgLink = document.createElement('a');
        imgElOrigLink.removeAttribute('href');
        imgLink.href = imgElOrigLinkHref;

        // insert the link before the image + image in the link
        imgEl.parentNode.insertBefore(imgLink, imgEl);
        imgLink.appendChild(imgEl);

        // TODO: PERHAPS
        // add a link to the widget layer
        //let divLayerWidgetLink = document.createElement('a');
        //divLayerWidgetLink.href = imgElOrigLinkHref;
        //divLayerWidgetLink.append(divLayerWidget);
        //imgLink.parentNode.insertBefore(divLayerWidgetLink, imgEl.nextSibling);
        imgLink.parentNode.insertBefore(divLayerWidget, imgEl.nextSibling);
        // put a link on the div layer blorm icon
        let divLayerBlormIconLink = document.createElement('a');
        divLayerBlormIconLink.href = imgElOrigLinkHref;
        divLayerBlormIconLink.append(divLayerBlormIcon);

        imgLink.parentNode.insertBefore(divLayerBlormIconLink, imgEl.nextSibling);
        imgLink.appendChild(imgEl);
    }
}

function SetPosition(element, config) {

     element.style.marginTop = config.positionTop + config.positionUnit;

     element.style.marginRight = config.positionRight + config.positionUnit;

     element.style.marginBottom = config.positionBottom + config.positionUnit;

     element.style.marginLeft = config.positionLeft + config.positionUnit;

     return element;
}