(function () {
  "use strict";
  // TIMELINE
  // define variables
  var items = document.querySelectorAll(".timeline li");

  // check if an element is in viewport
  // http://stackoverflow.com/questions/123999/how-to-tell-if-a-dom-element-is-visible-in-the-current-viewport
  function isElementInViewport(el) {
    var rect = el.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <=
        (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  }

  function callbackFunc() {
    for (var i = 0; i < items.length; i++) {
      if (isElementInViewport(items[i])) {
        items[i].classList.add("in-view");
      }
    }
  }

  // listen for events
  window.addEventListener("load", callbackFunc);
  window.addEventListener("resize", callbackFunc);
  window.addEventListener("scroll", callbackFunc);
})();

// ACCORDION

var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  });
	
}


// LIGHTBOX VIDEO

// Find all links on the page
var links = document.querySelectorAll("a");

// Loop through each link and check if it is a Vimeo or YouTube link
links.forEach(function(link) {
  var url = link.href;
  if (url.indexOf("vimeo.com") > -1 || url.indexOf("youtube.com") > -1 || url.indexOf("youtu.be") > -1) {
    // Add the popup-video class to the link
    link.classList.add("popup-video");
  }
});

function openLightbox(link) {
  var videoId, iframeSrc;
  if (link.indexOf("vimeo.com") > -1) { // Check if Vimeo link
    videoId = link.split("/").pop(); // Extract video id
    iframeSrc = "https://player.vimeo.com/video/" + videoId;
  } else if (link.indexOf("youtube.com") > -1 || link.indexOf("youtu.be") > -1) { // Check if Youtube link
    videoId = link.split("=")[1]; // Extract video id
    if (!videoId) { // Check if short link format
      videoId = link.split("/").pop(); // Extract video id from short link format
    }
    iframeSrc = "https://www.youtube.com/embed/" + videoId;
  } else { // Invalid link
    alert("Invalid video link.");
    return;
  }
}

// Find all video links on the page
var videoLinks = document.querySelectorAll(".popup-video");

// Loop through each video link and add a click event listener
videoLinks.forEach(function(link) {
  link.addEventListener("click", function(e) {
    e.preventDefault();
    var videoLink = this.href;
    openLightbox(videoLink);
  });
});

// Close lightbox when clicked outside of iframe
document.addEventListener("click", function(e) {
  if (e.target.id === "popup-video") {
    document.body.removeChild(e.target);
  }
});

