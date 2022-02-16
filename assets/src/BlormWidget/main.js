//import 'bootstrap';
//import '../../node_modules/bootstrap/scss/_popover.scss';
import './blormwidget.scss';
import * as rendering from './lib/rendering.js';

export function GetBlormWidgetContainer(blormPost) {

    let blormWidgetContainer = rendering.GetBlormWidgetContainer(blormPost);
    return checkForClassForWidgetPlacement(blormWidgetContainer);
}

export function GetBlormWidgetContainerMenu(blormPost) {

    let blormWidgetContainerMenu = rendering.GetBlormWidgetContainerMenu(blormPost);
    return checkForClassForWidgetPlacement(blormWidgetContainerMenu);
}

export function AddMenueToImage(imgEl, blormWidgetContainer) {
    return rendering.AddMenueToImage(imgEl, blormWidgetContainer);
}

/*
** check for the plugin setting classForWidgetPlacement
 */
function checkForClassForWidgetPlacement (blormWidget) {

    // if classForWidgetPlacement has a class then wrap it around
    if (blormapp.postConfig.classForWidgetPlacement !== "") {

        let classForWidgetPlacement = document.createElement("span");
        classForWidgetPlacement.classList.add(blormapp.postConfig.classForWidgetPlacement);
        classForWidgetPlacement.append(blormWidget);

        return classForWidgetPlacement;
    }

    // no class no wrapper
    return blormWidget;

}