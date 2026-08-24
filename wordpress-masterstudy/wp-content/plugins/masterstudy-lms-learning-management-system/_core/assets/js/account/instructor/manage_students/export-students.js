"use strict";

(function ($) {
  $(document).ready(function () {
    $('[data-id="export-students-to-csv"]').on('click', function (e) {
      e.preventDefault();
      var urlParams = new URLSearchParams(window.location.search);
      var course_id = urlParams.get('course_id');
      if (course_id) {
        var apiUrl = "".concat(ms_lms_resturl, "/students/export/").concat(course_id);
        fetch(apiUrl, {
          method: 'GET',
          headers: {
            'X-WP-Nonce': ms_lms_nonce,
            'Content-Type': 'application/json'
          }
        }).then(function (response) {
          if (response.ok) {
            return response.json();
          }
        }).then(function (response) {
          downloadCSV(course_id, response);
        })["catch"](function (error) {
          throw error;
        });
      }
    });
    function downloadCSV(course_id, data) {
      var href,
        filename,
        link,
        csvUtf = '';
      var csv = convertArrayofObjectsToCSV({
        data: data
      });
      if (csv == null) return;
      filename = "course_".concat(course_id, "_students.csv");
      if (!csv.match(/^data:text\/csv/i)) {
        csvUtf = 'data:text/csv;charset=utf-8,';
      }
      href = encodeURI(csvUtf) + "\uFEFF" + encodeURI(csv);
      link = document.createElement('a');
      link.setAttribute('href', href);
      link.setAttribute('download', filename);
      link.click();
    }
    function convertArrayofObjectsToCSV(args) {
      var result, keys, columnDelimeter, lineDelimeter, data;
      data = args.data || null;
      if (data == null || !data.length) {
        return null;
      }
      columnDelimeter = args.columnDelimeter || ',';
      lineDelimeter = args.lineDelimeter || '\r\n';
      keys = Object.keys(data[0]);
      result = '';
      result += keys.join(columnDelimeter);
      result += lineDelimeter;
      data.forEach(function (item) {
        keys.forEach(function (key, index) {
          if (index > 0) result += columnDelimeter + ' ';
          result += item[key] || '';
        });
        result += lineDelimeter;
      });
      return result;
    }
  });
})(jQuery);