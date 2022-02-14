import * as $ from 'jquery';
import 'bootstrap';

export function getLogo() {

    // display has two parts the LogoIcon and the SocialIcons list
    let BlormWidgetPlusLogoIcon = document.createElement("div");
    BlormWidgetPlusLogoIcon.classList.add("blormWidgetPlusLogoIcon");

    $(BlormWidgetPlusLogoIcon).popover(
        {
            title: "<b>BLORM (BLOG SWARM)</b>",
            content: "a social network hidden in the backend of your wordpress blog, more info <a href='https://blorm.io'>blorm.io</a>.",
            html: true,
            placement: "top",
            animation: true,
            trigger: 'click'
        }
    );

    $(BlormWidgetPlusLogoIcon).on('shown.bs.popover', function(){
        BlormWidgetPlusLogoIcon.classList.add("active");
    });
    $(BlormWidgetPlusLogoIcon).on('hide.bs.popover', function(){
        BlormWidgetPlusLogoIcon.classList.remove("active");
    });

    return BlormWidgetPlusLogoIcon;
}