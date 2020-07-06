
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
            this.setAttribute("style", "background-color: grey;");
        });
        blormMenuBar.addEventListener('mouseout', function() {
            this.removeAttribute("style", "background-color: grey;");
        });

        //document.write(x[i].tagName + "<br>");
        console.log("typeBlormpost");
        console.log(typeBlormpost[i]);
        //element.parentNode.insertBefore(newElement, element.nextSibling);
        insertAfter(typeBlormpost[i],blormMenuBar);
        //typeBlormpost[i].appendChild(blormMenuBar);

    }

});




/*typeBlormpost.forEach(function(element){

    element.appendChild(para);

});*/
