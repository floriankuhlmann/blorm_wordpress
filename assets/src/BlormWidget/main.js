import * as $ from 'jquery';
import 'bootstrap';
import '../../node_modules/bootstrap/scss/bootstrap.scss';
import './blormwidget.scss';
import * as rendering from './lib/rendering.js';

export function GetBlormWidgetContainer(blormPost) {
    return rendering.GetBlormWidgetContainer(blormPost);
}

export function GetBlormWidgetContainerMenu(blormPost) {
    return rendering.GetBlormWidgetContainerMenu(blormPost);
}

export function AddMenueToImage(imgEl, blormWidgetContainer) {
    return rendering.AddMenueToImage(imgEl, blormWidgetContainer);
}