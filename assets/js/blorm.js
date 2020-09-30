
var shared = "<svg class=\"svg-icon\" width=\"16\" height=\"16\" aria-hidden=\"true\" role=\"img\" focusable=\"false\" viewBox=\"0 0 24 24\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><path d=\"M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\"></path><path d=\"M0 0h24v24H0z\" fill=\"none\"></path></svg>";
var comment = "<svg class=\"svg-icon\" width=\"16\" height=\"16\" aria-hidden=\"true\" role=\"img\" focusable=\"false\" viewBox=\"0 0 24 24\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><path d=\"M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z\"></path><path d=\"M0 0h24v24H0z\" fill=\"none\"></path></svg>";
var reblog = "<span class=\"dashicons dashicons-controls-repeat\"></span>";
var blormplus = "<img src=\""+blormAssets+"icons/circle-add-plus-new-outline-stroke.png\" class='blormWidgetImagePlus'>";


function renderBlormWidget() {


    var widgetcode = '<div class="blormWidget">' +
        '<div class="blormWidgetPlus">'+blormplus+'<div class="blormWidgetPlusText">&nbsp;BLORM</div></div>' +
        '</div>';


    return widgetcode;

}




document.addEventListener("DOMContentLoaded", function(){
    // Handler when the DOM is fully loaded



    function insertAfter(referenceNode, newNode) {
        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    }

    var para = document.createElement("p");
    var node = document.createTextNode("This is BLorm.");
    para.appendChild(node);



    //var typeBlormpost = document.getElementsByClassName("type-blormpost");
    var typeBlormpost = document.getElementsByClassName("type-blormpost");

    console.log(typeBlormpost);

    l = typeBlormpost.length;
    console.log(typeBlormpost.length);
    for (var i = 0; i < typeBlormpost.length; i++) {

        var blormMenuBar = document.createElement("div");
        blormMenuBar.style.width = "100%";
        blormMenuBar.style.height = "1rem";
        blormMenuBar.style.marginBottom = "1rem";
        blormMenuBar.innerHTML = "Blorm";
        blormMenuBar.className = "blormMenuBar";
        blormMenuBar.addEventListener('mouseover', function() {
            //this.setAttribute("style", "cursor: ;");
        });
        blormMenuBar.addEventListener('mouseout', function() {
            //this.removeAttribute("style", "background-color: grey;");
        });

        //document.write(x[i].tagName + "<br>");
        console.log("typeBlormpost");
        console.log(typeBlormpost[i]);
        //element.parentNode.insertBefore(newElement, element.nextSibling);
        insertAfter(typeBlormpost[i],blormMenuBar);
        //typeBlormpost[i].appendChild(blormMenuBar);

    }

    //var typeBlormpost = document.getElementsByClassName("type-blormpost");
    var blormShared = document.getElementsByClassName("blorm-shared");

    console.log(blormShared);

    l = blormShared.length;
    console.log(blormShared.length);
    for (var i = 0; i < blormShared.length; i++) {

        var blormMenuBar = document.createElement("div");
        //blormMenuBar.style.width = "100%";
        //blormMenuBar.style.height = "1rem";
        //blormMenuBar.style.marginBottom = "1rem";
        //blormMenuBar.style.border = "1px solid #000000";
        //blormMenuBar.innerHTML = "<span>"+blormplus+ "BLORM " + shared + " 1," + comment + " 4, " + reblog + " 4 </span>";
        blormMenuBar.innerHTML = renderBlormWidget();
        blormMenuBar.className = "blormMenuBar entry-footer";
        blormMenuBar.addEventListener('mouseover', function() {
            //this.setAttribute("style", "background-color: grey;");
        });
        blormMenuBar.addEventListener('mouseout', function() {
            //this.removeAttribute("style", "background-color: grey;");
        });

        var blormSharedChilds = blormShared[i].childNodes;
        console.log(blormShared); // Return collection of child node

        //document.write(x[i].tagName + "<br>");
        console.log("blormShared");
        console.log(blormShared[i].lastElementChild.nextSibling);
        console.log(blormShared[i].getElementsByClassName("entry-content"));
        //element.parentNode.insertBefore(newElement, element.nextSibling);
        insertAfter(blormShared[i].getElementsByClassName("entry-content")[0],blormMenuBar);
        //typeBlormpost[i].appendChild(blormMenuBar);

    }



});




/*typeBlormpost.forEach(function(element){

    element.appendChild(para);

});*/
