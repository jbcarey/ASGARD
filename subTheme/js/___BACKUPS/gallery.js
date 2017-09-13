document.addEventListener('DOMContentLoaded', (function() {

  baguetteBox.run('.gallery', {
    captions: function(element) {
      return element.getElementsByTagName('img')[0].title;
    }
  });

}));
