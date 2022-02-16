

export function GetOriginUrl(blormPost) {

    // display has two parts the LogoIcon and the SocialIcons list
    let BlormWidgetOrigin = document.createElement("div");
    BlormWidgetOrigin.classList.add("blormWidgetOrigin");

    if ( typeof blormPost.OriginWebsiteName !== 'undefined' && typeof blormPost.OriginWebsiteUrl !== 'undefined') {
        let a = document.createElement('a');
        a.classList.add("blormWidgetOriginLink");
        a.href = blormPost.OriginWebsiteUrl;
        a.innerHTML = "»&nbsp;" + prepareOriginWebsiteUrl(blormPost.OriginWebsiteName);
        BlormWidgetOrigin.appendChild(a);
    }

    return BlormWidgetOrigin;
}

function prepareOriginWebsiteUrl(name) {

    if ( typeof name === 'undefined' ) {
        return "";
    }

    let nameElements = name.split("//");
    console.log(nameElements);
    if ( nameElements.length > 1) {
        return nameElements[1].substring(0,24);
    }
    return nameElements[0].substring(0,24);

}