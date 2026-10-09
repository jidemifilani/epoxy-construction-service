/* Admin: ask before anything destructive. Forms carry data-confirm="..." when
   the action cannot be undone; the browser's own dialog is used so there is
   nothing extra to load. */
(function () {
  'use strict';

  document.addEventListener('submit', function (event) {
    var form = event.target;
    var message = form.getAttribute('data-confirm');
    if (message && !window.confirm(message)) {
      event.preventDefault();
    }
  });
})();
