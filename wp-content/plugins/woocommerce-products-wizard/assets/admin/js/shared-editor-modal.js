function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard Shared Editor Modal
 * Original author: Alex Troll
 * Further changes, comments: mail@troll-winner.com
 */

(function (root, factory) {
  'use strict';

  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  } else if ((typeof exports === "undefined" ? "undefined" : _typeof(exports)) === 'object' && typeof module !== 'undefined' && typeof require === 'function') {
    module.exports = factory(require('jquery'));
  } else {
    factory(root.jQuery);
  }
})(this, function ($) {
  'use strict';

  var $document = $(document);

  // open shared wp-editor modal
  $document.on('click', '[data-component~="wcpw-shared-editor-open"]', function (event) {
    event.preventDefault();
    var $element = $(this);
    var $target = $element.next('[data-component~="wcpw-shared-editor-target"]');
    var $sharedEditorModal = $('#wcpw-shared-editor-modal');

    // for a modal in a modal
    if (window.location.hash && window.location.hash !== 'close') {
      $sharedEditorModal.find('[href]').each(function () {
        return $(this).attr('href', window.location.hash);
      });
    }
    $sharedEditorModal.addClass('is-opened').data('target', $target);

    // set editor content
    if ($('#wp-wcpw-shared-editor-wrap').hasClass('tmce-active') && window.tinyMCE.get('wcpw-shared-editor')) {
      window.tinyMCE.get('wcpw-shared-editor').setContent($target.val());
    } else {
      $('#wcpw-shared-editor').val($target.val());
    }
  });

  // modal save click
  $document.on('click', '#wcpw-shared-editor-save', function () {
    var $sharedEditorModal = $('#wcpw-shared-editor-modal');
    var content = $('#wcpw-shared-editor').val();

    // get editor content
    if ($('#wp-wcpw-shared-editor-wrap').hasClass('tmce-active') && window.tinyMCE.get('wcpw-shared-editor')) {
      content = window.tinyMCE.get('wcpw-shared-editor').getContent();
    }
    $sharedEditorModal.data('target').val(content);
  });

  // modal close click
  $document.on('click', '[data-component~="wcpw-modal-close"]', function () {
    return $(this).closest('[data-component~="wcpw-modal"]').removeClass('is-opened');
  });
});