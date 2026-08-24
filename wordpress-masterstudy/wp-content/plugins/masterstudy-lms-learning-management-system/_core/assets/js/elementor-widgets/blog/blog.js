"use strict";

jQuery(document).ready(function ($) {
  $(document).on("click", ".masterstudy-post-template__pagination_list_item a", function (e) {
    e.preventDefault();
    var currentPage = $(this).attr("href").split("page=")[1];
    loadPosts(currentPage);
  });
  var urlParams = new URLSearchParams(window.location.search);
  var currentPageFromUrl = urlParams.get('current-page');
  if (currentPageFromUrl) {
    loadPosts(currentPageFromUrl);
  }
  function loadPosts(currentPage) {
    var $paginationList = $(".masterstudy-post-template__pagination_list");
    $.ajax({
      url: ms_lms_blog.ajax_url,
      type: "POST",
      data: {
        action: "ms_lms_blog_pagination",
        nonce: ms_lms_blog.nonce,
        current_page: currentPage,
        posts_per_page: $paginationList.data("per-page"),
        blog_style: $paginationList.data("blog-style") || "cards"
      },
      success: function success(response) {
        if (response.success) {
          $(".masterstudy-post-template__wrap").html(response.data.posts);
          $(".masterstudy-post-template__pagination").html(response.data.pagination);
          var newUrl = new URL(window.location);
          newUrl.searchParams.set('current-page', currentPage);
          window.history.pushState({}, '', newUrl);
        } else {
          error.log(response.data.message);
        }
      }
    });
  }
});