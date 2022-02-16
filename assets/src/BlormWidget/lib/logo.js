import * as $ from 'jquery';
import { createPopper } from '@popperjs/core';

export function getLogo() {

    let blormWidgetReactionContainer = document.createElement("div");
    blormWidgetReactionContainer.classList.add("blormWidgetSocialReactionsContainer");

    // display has two parts the LogoIcon and the SocialIcons list
    let BlormWidgetPlusLogoIcon = document.createElement("div");
    BlormWidgetPlusLogoIcon.classList.add("blormWidgetPlusLogoIcon");

    // add the element for social reactions to the container
    blormWidgetReactionContainer.appendChild(BlormWidgetPlusLogoIcon);

    // generate the tooltip element (init with display = none)
    let blormWidgetToolTipElement = generateLogoContent();
    blormWidgetReactionContainer.appendChild(blormWidgetToolTipElement);

    // create the Popper wit the tooltip
    let popperInstance = createPopper(
        BlormWidgetPlusLogoIcon,
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

    //return BlormWidgetPlusLogoIcon;
    showEvents.forEach((event) => {
        BlormWidgetPlusLogoIcon.addEventListener(event,function () {
            blormWidgetElementToggleFunction(blormWidgetToolTipElement, popperInstance, BlormWidgetPlusLogoIcon);
        });
    });

    return blormWidgetReactionContainer;

}

function blormWidgetElementToggleFunction(blormWidgetToolTipElement, popperInstance, blormWidgetPlusLogoIcon) {

    console.log(blormWidgetToolTipElement.getAttribute('data-show'));

    if (blormWidgetToolTipElement.getAttribute('data-show') === "true" ) {
        blormWidgetToolTipElement.removeAttribute('data-show');
        blormWidgetPlusLogoIcon.classList.remove("active");
        return;
    }

    blormWidgetToolTipElement.setAttribute('data-show', 'true');
    blormWidgetPlusLogoIcon.classList.add("active");
    // We need to tell Popper to update the tooltip position
    // after we show the tooltip, otherwise it will be incorrect
    popperInstance.update();
}



function generateLogoContent() {

    // create the tooltip
    let toolTip = document.createElement("div");

    toolTip.classList.add("blormWidgetTooltip");

    // create the headline
    let toolTipHeadline = document.createElement("div");
    toolTipHeadline.classList.add("blormWidgetTooltipHeadline");
    toolTipHeadline.innerHTML = "<b>BLORM (BLOG SWARM)</b>";

    // create the content
    let toolTipContent = document.createElement("div");
    toolTipContent.classList.add("blormWidgetTooltipContent");
    let toolTipContentElement = document.createElement("span");
    toolTipContentElement.innerHTML = "a social network hidden in the backend<br> " +
        "of your wordpress blog." +
        "<br>visit <a href='https://blorm.io'>blorm.io</a>.";
    toolTipContent.appendChild(toolTipContentElement);

    // put the tooltip together
    toolTip.appendChild(toolTipHeadline);
    toolTip.appendChild(toolTipContent);

    return toolTip;
}
