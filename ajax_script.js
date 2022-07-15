jQuery(document).ready(function () {
  jQuery("#wcswa-update-btn").click(function (e) {
    e.preventDefault();
    nonce = jQuery(this).attr("data-nonce");
    var userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: wcswaAjax.ajaxurl,
      data: {
        action: "wcswa_update_products",
        nonce: nonce,
        userTimezone: userTimezone,
      },
      success: function (response) {
        if (response.type == "success") {
          jQuery("#wcswa-update-result").addClass(
            "notice notice-success is-dismissible"
          );
          jQuery("#wcswa-update-result-message").html("Update successfully  !");
          jQuery("#wcswa-update-date").html(response.date);
          jQuery("#wcswa-product-count").html(response.product_count);
        } else {
          jQuery("#wcswa-update-result").removeClass("notice notice-success");
          jQuery("#wcswa-update-result").addClass("notice notice-error");
          jQuery("#wcswa-update-result-message").html(
            "Your update could not be done ! try again later"
          );
        }
      },
    });
  });
});
