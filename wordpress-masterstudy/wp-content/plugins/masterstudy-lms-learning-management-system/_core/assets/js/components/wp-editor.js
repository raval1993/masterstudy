"use strict";

(function ($) {
  $(document).ready(function () {
    if (typeof tinyMCE !== 'undefined') {
      editorCustomStyles();
      if (!data.words_off) {
        updateEditorWordCount();
      }
    }
    function editorCustomStyles() {
      var body_dark_styles = data.theme_fonts ? "\n                body {\n                    line-height: normal;\n                    background-color: rgba(23,23,23,1);\n                    color: rgba(255,255,255,0.7); }\n                " : "\n                body {\n                    font-family: 'Albert Sans', sans-serif;\n                    line-height: normal;\n                    background-color: rgba(23,23,23,1);\n                    color: rgba(255,255,255,0.7); }\n                ";
      var body_light_styles = data.theme_fonts ? "\n                body {\n                    line-height: normal;\n                    background-color: rgba(255,255,255,1);\n                    color: rgba(0,25,49,1);\n                }\n                " : "\n                body {\n                    font-family: 'Albert Sans', sans-serif;\n                    line-height: normal;\n                    background-color: rgba(255,255,255,1);\n                    color: rgba(0,25,49,1);\n                }\n                ";
      var customStyles = data.dark_mode ? body_dark_styles : body_light_styles;
      var editors = Array.isArray(tinyMCE.editors) ? tinyMCE.editors : [];
      var shouldRetry = false;
      if (editors.length > 0) {
        editors.forEach(function (editor) {
          if (!editor || editor.iframeElement === undefined) {
            shouldRetry = true;
            return;
          }
          var iframeDocument = editor.iframeElement.contentDocument || editor.iframeElement.contentWindow.document;
          if (!iframeDocument || !iframeDocument.head) {
            shouldRetry = true;
            return;
          }
          var styleElement = iframeDocument.head.querySelector('style[data-masterstudy-wp-editor-content="1"]');
          if (!styleElement) {
            styleElement = iframeDocument.createElement('style');
            styleElement.setAttribute('data-masterstudy-wp-editor-content', '1');
            iframeDocument.head.appendChild(styleElement);
          }
          styleElement.textContent = customStyles;
        });
      } else {
        var fallbackEditor = tinyMCE.get(data.editor_id);
        if (fallbackEditor && fallbackEditor.iframeElement) {
          var iframeDocument = fallbackEditor.iframeElement.contentDocument || fallbackEditor.iframeElement.contentWindow.document;
          if (iframeDocument && iframeDocument.head) {
            var styleElement = iframeDocument.head.querySelector('style[data-masterstudy-wp-editor-content="1"]');
            if (!styleElement) {
              styleElement = iframeDocument.createElement('style');
              styleElement.setAttribute('data-masterstudy-wp-editor-content', '1');
              iframeDocument.head.appendChild(styleElement);
            }
            styleElement.textContent = customStyles;
          }
        } else {
          shouldRetry = true;
        }
      }
      if (shouldRetry) {
        setTimeout(function () {
          editorCustomStyles();
        }, 500);
      }
      var styles = data.dark_mode ? "\n            body .mce-container.mce-panel.mce-floatpanel {\n                background-color: rgba(30,30,30,1);\n                border: 1px solid rgba(255,255,255,.05);\n                border-radius: 4px;\n                color: rgba(255,255,255,1);\n                margin-top: 3px;\n            }\n            body .mce-container.mce-panel.mce-floatpanel .mce-menu-item:hover {\n                background-color: rgba(255,255,255,.05);\n            }\n            body .mce-container.mce-panel.mce-floatpanel .mce-menu-item.mce-active {\n                background-color: rgba(255,255,255,.05);\n            }\n            body .masterstudy-wp-editor [role=\"toolbar\"] {\n                display: flex;\n                justify-content: space-between;\n                align-items: center;\n                flex-wrap: wrap;\n                gap: 10px;\n            }\n            body .masterstudy-wp-editor__word-count {\n                padding: 9px 10px;\n                align-self: end;\n                margin-left: auto;\n                color: rgba(255,255,255,1);\n            }\n            body.rtl .masterstudy-wp-editor__word-count {\n                margin-left: 0;\n                margin-right: auto;\n                order: 1;\n            }\n            " : "\n            body .mce-container.mce-panel.mce-floatpanel {\n                background-color: rgba(255,255,255,1);\n                border: 1px solid rgba(238,241,247,1);\n                border-radius: 4px;\n                color: rgba(0,25,49,1);\n                margin-top: 3px;\n            }\n            body .mce-container.mce-panel.mce-floatpanel .mce-menu-item:hover {\n                background-color: rgba(34,122,255,1);\n                color: rgba(255,255,255,1);\n            }\n            body .mce-container.mce-panel.mce-floatpanel .mce-menu-item.mce-active {\n                background-color: rgba(34,122,255,1);\n                color: rgba(255,255,255,1);\n            }\n            body .masterstudy-wp-editor [role=\"toolbar\"] {\n                display: flex;\n                justify-content: space-between;\n                align-items: center;\n                flex-wrap: wrap;\n                gap: 10px;\n            }\n            body .masterstudy-wp-editor__word-count {\n                padding: 9px 10px;\n                align-self: end;\n                margin-left: auto;\n                color: rgba(0,25,49,1);\n            }\n            body.rtl .masterstudy-wp-editor__word-count {\n                margin-left: 0;\n                margin-right: auto;\n                order: 1;\n            }\n            ";
      var parentStyleElement = document.head.querySelector('style[data-masterstudy-wp-editor-ui="1"]');
      if (!parentStyleElement) {
        parentStyleElement = document.createElement('style');
        parentStyleElement.setAttribute('data-masterstudy-wp-editor-ui', '1');
        document.head.appendChild(parentStyleElement);
      }
      parentStyleElement.textContent = styles;
    }
    var wpEditor = null;
    function updateEditorWordCount() {
      if (!wpEditor) {
        setTimeout(function () {
          updateEditorWordCount();
        }, 500);
        wpEditor = tinyMCE.get(data.editor_id);
        return;
      }
      $.each($('.masterstudy-wp-editor'), function (i, container) {
        var toolbar = $(container).find('[role="toolbar"]').first();
        var countContainer = toolbar.find('.masterstudy-wp-editor__word-count');
        var editorContent = wpEditor.getContent();
        var cleanContent = editorContent.replace(/<(?:.|\n)*?>/gm, '').trim();
        var editorWords = cleanContent.split(/\s+/);
        editorWords = editorWords.filter(function (word) {
          return word.length > 0;
        });
        var countedWord = "".concat(editorWords.length, " ").concat(data.translate.word || 'words');
        if (countContainer.length > 0) {
          countContainer.remove();
        }
        $(container).find("textarea[name=\"".concat(data.editor_id, "\"]")).val(editorContent);
        toolbar.append("<div class=\"masterstudy-wp-editor__word-count\">".concat(countedWord, "</div>"));
      });
      wpEditor.off('input keydown');
      wpEditor.on('input keydown', function () {
        if (!data.words_off) {
          updateEditorWordCount();
        }
      });
    }
  });
})(jQuery);