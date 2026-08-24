"use strict";

(function ($) {
  $(document).ready(function () {
    $(document).on('click', '.masterstudy-image-upload__field-button', function () {
      $(this).closest('.masterstudy-image-upload__field').find('.masterstudy-image-upload__input').click();
    });
    $(document).on('dragenter', '.masterstudy-image-upload__field', function (e) {
      e.preventDefault();
      $(this).addClass('masterstudy-image-upload__field_highlight');
    });
    $(document).on('dragleave drop', '.masterstudy-image-upload__field', function (e) {
      e.preventDefault();
      $(this).removeClass('masterstudy-image-upload__field_highlight');
    });
    $(document).on('dragover', '.masterstudy-image-upload__field', function (e) {
      e.preventDefault();
    });
    $(document).on('drop', '.masterstudy-image-upload__field', function (e) {
      e.preventDefault();
      var file = e.originalEvent.dataTransfer.files[0];
      if (file) {
        handleImageSelect(file, $(this));
      }
    });
    $(document).on('change', '.masterstudy-image-upload__input', function () {
      var file = this.files[0];
      if (file) {
        handleImageSelect(file, $(this).closest('.masterstudy-image-upload__field'));
      }
      this.value = '';
    });
    $(document).on('click', '.masterstudy-image-upload__delete', function (e) {
      e.preventDefault();
      var wrapper = $(this).closest('.masterstudy-image-upload');
      wrapper.find('.masterstudy-image-upload__item-wrapper').empty();
      wrapper.find('.masterstudy-image-upload__field').removeClass('masterstudy-image-upload__field_hide');
      window.masterstudy_selected_image = null;
    });
    function handleImageSelect(file, dropArea) {
      var wrapper = dropArea.closest('.masterstudy-image-upload');
      var errorContainer = wrapper.find('.masterstudy-image-upload__field-error');
      var previewContainer = wrapper.find('.masterstudy-image-upload__item-wrapper');
      errorContainer.removeClass('masterstudy-image-upload__field-error_show').text('');
      previewContainer.empty();
      var ext = '.' + file.name.split('.').pop().toLowerCase();
      var allowedExtensions = masterstudy_image_upload_data.allowed_extensions.map(function (e) {
        return e.toLowerCase();
      });
      if (!allowedExtensions.includes(ext)) {
        errorContainer.addClass('masterstudy-image-upload__field-error_show').text(masterstudy_image_upload_data.type_not_allowed);
        return;
      }
      var maxSizeMb = parseFloat(masterstudy_image_upload_data.allowed_filesize || 0);
      var fileSizeMb = file.size / 1024 / 1024;
      if (fileSizeMb > maxSizeMb) {
        errorContainer.addClass('masterstudy-image-upload__field-error_show').text(masterstudy_image_upload_data.too_large);
        return;
      }
      dropArea.addClass('masterstudy-image-upload__field_hide');
      var reader = new FileReader();
      reader.onload = function (e) {
        var html = "\n                    <div class=\"masterstudy-image-upload__item\">\n                        <img src=\"".concat(e.target.result, "\" class=\"masterstudy-image-upload__image\">\n                        <a class=\"masterstudy-image-upload__delete\" href=\"#\" data-id=\"\"></a>\n                        <span class=\"masterstudy-image-upload__item-cover\"></span>\n                    </div>");
        previewContainer.html(html);
      };
      reader.readAsDataURL(file);
      window.masterstudy_selected_image = file;
    }
  });
})(jQuery);