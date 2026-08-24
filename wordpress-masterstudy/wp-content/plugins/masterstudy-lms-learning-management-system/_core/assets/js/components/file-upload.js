"use strict";

(function ($) {
  $(document).ready(function () {
    var all_classes = ['masterstudy-account-settings'];
    $(document).on('drop', '.masterstudy-file-upload__field', function (e) {
      e.preventDefault();
      $(this).removeClass('masterstudy-file-upload__field_highlight');
      var files = e.originalEvent.dataTransfer.files;
      var dropArea = $(this);
      var parentClass = findClosestParentClassName(dropArea, all_classes);
      handleFileInputChange(dropArea, files[0], parentClass, 'drag');
    });
    $(document).on('change', '.masterstudy-file-upload__input', function (e) {
      var dropArea = $(this).closest('.masterstudy-file-upload__field');
      var parentClass = findClosestParentClassName(dropArea, all_classes);
      handleFileInputChange(dropArea, this, parentClass, 'input');
    });
    $(document).on('click', '.masterstudy-file-upload__link', function (e) {
      e.preventDefault();
      var dropArea = $(this).closest('.masterstudy-file-upload').find('.masterstudy-file-upload__field');
      var parentClass = findClosestParentClassName(dropArea, all_classes);
      var formContainer = $(this).closest(".".concat(parentClass));
      deleteFile($(this).data('id'), dropArea, parentClass, formContainer);
    });
    $(document).on('dragenter', '.masterstudy-file-upload__field', function (e) {
      e.preventDefault();
      $(this).addClass('masterstudy-file-upload__field_highlight');
    });
    $(document).on('dragover', '.masterstudy-file-upload__field', function (e) {
      e.preventDefault();
    });
    $(document).on('dragleave', '.masterstudy-file-upload__field', function (e) {
      var rect = this.getBoundingClientRect();
      var x = e.clientX;
      var y = e.clientY;
      if (!(x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom)) {
        $(this).removeClass('masterstudy-file-upload__field_highlight');
      }
    });
    $(document).on('click', '.masterstudy-file-upload__field-button', function () {
      $(this).parent().find('.masterstudy-file-upload__input').click();
    });
    $(document).on('click', "[data-id='cancel'], .masterstudy-alert__header-close", function (e) {
      e.preventDefault();
      $(this).closest("[data-id='file_upload_file_alert']").removeClass('masterstudy-alert_open');
    });
    $('.masterstudy-form-builder__radio-group').each(function () {
      if ($(this).find('.masterstudy-form-builder__radio-wrapper_checked').length === 0) {
        $(this).find('.masterstudy-form-builder__radio-container').first().find('.masterstudy-form-builder__radio-wrapper').addClass('masterstudy-form-builder__radio-wrapper_checked');
      }
    });
    setTimeout(function () {
      $('.masterstudy-form-builder__select').each(function () {
        var $parent = $(this).parent();
        $(this).select2({
          dropdownParent: $parent,
          minimumResultsForSearch: Infinity
        });
      });
    }, 1500);
    var formats = {
      'img': ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml'],
      'excel': ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
      'word': ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
      'powerpoint': ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
      'pdf': ['application/pdf'],
      'video': ['video/mp4', 'video/avi', 'video/flv', 'video/webm', 'video/x-ms-wmv', 'video/quicktime'],
      'audio': ['audio/mp3', 'audio/x-ms-wma', 'audio/aac', 'audio/mpeg'],
      'archive': ['application/zip', 'application/gzip', 'application/x-rar-compressed', 'application/x-7z-compressed', 'application/x-zip-compressed']
    };
    function findClosestParentClassName(element, classList) {
      var foundClass = null;
      classList.some(function (className) {
        if (element.closest(".".concat(className)).length > 0) {
          foundClass = className;
          return true;
        }
        return false;
      });
      return foundClass;
    }
    function handleFileInputChange(dropArea, fileInput, parent_class, type) {
      if ($(dropArea).parent().find('.masterstudy-file-upload__item').length > 0) {
        $(dropArea).parent().find('.masterstudy-file-upload__field-error').addClass('masterstudy-file-upload__field-error_show').text(masterstudy_file_upload_data.only_one_file);
        return;
      } else {
        $(dropArea).parent().find('.masterstudy-file-upload__field-error').removeClass('masterstudy-file-upload__field-error_show');
      }
      var file;
      if ('drag' === type) {
        file = fileInput;
      } else {
        file = fileInput.files[0];
      }
      if (file) {
        handleFiles(file, dropArea, parent_class);
      }
      fileInput.value = null;
    }
    function getFileType(fileType) {
      return Object.keys(formats).filter(function (type) {
        return formats[type].includes(fileType);
      });
    }
    function handleFiles(file, dropArea, parent_class) {
      var loadingBar = $(dropArea).find('.masterstudy-file-upload__field-progress-bar-filled');
      var totalFiles = 1;
      var extensions = $(dropArea).find('.masterstudy-hint__text').text();
      var uploadedFiles = 0;
      var current_percent = 0;
      var total_percent = 0;
      var formData = new FormData();
      formData.append('file', file);
      formData.append('action', masterstudy_file_upload_data.file_upload_action);
      formData.append('nonce', masterstudy_file_upload_data.file_upload_nonce);
      if (extensions.length > 0) {
        formData.append('extensions', extensions.trim());
      }
      $.ajax({
        url: masterstudy_file_upload_data.ajax_url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function xhr() {
          var xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener('progress', function (event) {
            if (event.lengthComputable) {
              current_percent = current_percent === 100 ? 95 : event.loaded / event.total / totalFiles * 100;
              if (totalFiles === 1) {
                loadingBar.css('width', current_percent + '%');
              }
            }
          }, false);
          return xhr;
        },
        beforeSend: function beforeSend() {
          $(dropArea).parent().find('.masterstudy-file-upload__field').addClass('masterstudy-file-upload__field_loading');
        },
        success: function success(data) {
          uploadedFiles++;
          if (data.error === false) {
            file.id = data.id;
            generateFileHtml(file, data.url, dropArea);
          }
          if (totalFiles === 1 && data.error !== false) {
            $(dropArea).parent().find('.masterstudy-file-upload__field-error').text(data.message);
            $(dropArea).parent().find('.masterstudy-file-upload__field-error').addClass('masterstudy-file-upload__field-error_show');
          }
          if (totalFiles === uploadedFiles) {
            loadingBar.css('width', '100%');
            setTimeout(function () {
              $(dropArea).parent().find('.masterstudy-file-upload__field').removeClass('masterstudy-file-upload__field_loading');
              loadingBar.css('width', '0');
            }, 500);
            setTimeout(function () {
              $(dropArea).parent().find('.masterstudy-file-upload__field-error').removeClass('masterstudy-file-upload__field-error_show');
            }, 1500);
            $(dropArea).find('.masterstudy-file-upload__input').attr('data-url', data.url);
            $(dropArea).parent().find(".".concat(parent_class, "__form-field-error")).remove();
          } else {
            total_percent = total_percent + current_percent;
            loadingBar.css('width', total_percent + '%');
          }
        }
      });
    }
    function generateFileHtml(file, fileUrl, dropArea) {
      var filesize = Math.round(file.size / 1024),
        filesize_label = filesize > 1000 ? 'mb' : 'kb',
        icon = getFileType(file.type).length > 0 ? getFileType(file.type) : 'unknown',
        icon_url = masterstudy_file_upload_data.full_image_view ? fileUrl : masterstudy_file_upload_data.icon_url + icon + '.svg';
      filesize = filesize > 1000 ? Math.round(filesize / 1024) : filesize;
      var html = '';
      if (masterstudy_file_upload_data.full_image_view) {
        html = "\n                <div class=\"masterstudy-file-upload__item masterstudy-file-upload__item_full-image\">\n                    <img src=\"".concat(icon_url, "\" class=\"masterstudy-file-upload__image\">\n                    <a class=\"masterstudy-file-upload__link\" href=\"#\" data-id=\"").concat(file.id, "\"></a>\n                </div>");
      } else {
        html = "\n                <div class=\"masterstudy-file-upload__item\">\n                    <img src=\"".concat(icon_url, "\" class=\"masterstudy-file-upload__image\">\n                    <div class=\"masterstudy-file-upload__wrapper\">\n                        <span class=\"masterstudy-file-upload__title\">").concat(file.name, "</span>\n                        <span class=\"masterstudy-file-upload__size\">").concat(filesize, " ").concat(filesize_label, "</span>\n                        <a class=\"masterstudy-file-upload__link\" href=\"#\" data-id=\"").concat(file.id, "\"></a>\n                    </div>\n                </div>");
      }
      $(dropArea).parent().find('.masterstudy-file-upload__item-wrapper').append(html);
      if (masterstudy_file_upload_data.full_image_view) {
        $(dropArea).parent().find('.masterstudy-file-upload__field').addClass('masterstudy-file-upload__field_disabled');
      }
    }
    function deleteFile(id, dropArea, parent_class, formContainer) {
      formContainer.find("[data-id='file_upload_file_alert']").addClass('masterstudy-alert_open');
      formContainer.find("[data-id='file_upload_file_alert']").find("[data-id='submit']").one('click', function (e) {
        var formData = new FormData();
        formData.append('file_id', id);
        formData.append('action', masterstudy_file_upload_data.file_delete_action);
        formData.append('nonce', masterstudy_file_upload_data.file_delete_nonce);
        $.ajax({
          url: masterstudy_file_upload_data.ajax_url,
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          beforeSend: function beforeSend() {
            formContainer.find("[data-id='file_upload_file_alert']").removeClass('masterstudy-alert_open');
          },
          success: function success(data) {
            if (data === 'OK') {
              formContainer.find("[data-id='".concat(id, "']")).closest('.masterstudy-file-upload__item').remove();
              $(dropArea).parent().find('.masterstudy-file-upload__field-error').removeClass('masterstudy-file-upload__field-error_show');
              $(dropArea).find('.masterstudy-file-upload__input').attr('data-url', '');
              if (masterstudy_file_upload_data.full_image_view) {
                $(dropArea).parent().find('.masterstudy-file-upload__field').removeClass('masterstudy-file-upload__field_disabled');
              }
            }
          }
        });
      });
    }
  });
})(jQuery);