/** beautify-article.js
 *  Beautifies parts of articles (e.g. subjeadings) for use in BINUS Today.
 *  #BetterThanKnowledge
 */
var i, j;

var content = document.getElementById("articlecontent");

// Get all child nodes
    content.innerHTML = content.innerHTML.replaceAll(/width="[^"]+" height="[^"]+"/gi, "width=\"100%\" height=\"auto\"");
var children0 = content.childNodes;
for (i = 0; i < children0.length; i++){
    var child0 = children0[i];
    switch (child0.tagName){
        case "P":
            var children1 = child0.childNodes;
            console.log(children1[0].outerText);

            // Replace <p> tags with "1. ABC" content to become <h3> subheadings
            if (children1.length > 0 && children1[0].outerText.match(/^[1-9][0-9]*\s*[.:-][\w\W]+$/gi)){
                var newElement = document.createElement("h3");
                while (child0.firstChild) {
                    // Remove font-weight: 400;
                    child0.firstChild.style.fontWeight = "600";
                    newElement.appendChild(child0.firstChild);
                }
                for (j = child0.attributes.length - 1; j >= 0; j--) {
                    newElement.attributes.setNamedItem(child0.attributes[j].cloneNode());
                }
                content.replaceChild(newElement, child0);
            }
            break;
        
            case "FIGURE":
                if (child0.getAttribute("itemtype") == "http://schema.org/ImageObject"){
                    child0.style.width = "100%";
                    child0.style.margin = "0"
                    if (child0.childNodes[0].tagName == "A"){
                        child0.childNodes[0].childNodes[0].setAttribute("width", "100%");
                        child0.childNodes[0].childNodes[0].removeAttribute("height");
                    }
                    if (child0.childNodes[1].tagName == "FIGCAPTION"){
                        child0.childNodes[1].style.fontStyle = "italic";
                    }
                }
                break;
    }
}

// Convert "1. things" into 
