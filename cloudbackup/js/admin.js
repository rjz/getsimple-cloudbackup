;(function ($) {

  "use strict";

	$.fn.expander = $.fn.expander || function () {

		return $(this).each(function () {

			var $this = $(this),
				$target = $($this.attr('href'));

			$this.click(function (e) {
				e.preventDefault();
				$target.slideToggle('fast');
			});

			$target.hide();
	
		});
	};

	$(document).ready(function () {
		$('[role=expander]').expander();

    $('[role=remove]').click(function (e) {
      if (!confirm(this.title)) {
        e.preventDefault();
      }
    });

		$('[role=schedule_start_today]').click(function (e) {
      var date = new Date,
        month = ('0' + (date.getMonth() + 1)).substr(0, 2),
        year = date.getFullYear().toString().substring(2);

      e.preventDefault();

      $('[name="schedule_start"]').val(month + '/' + date.getDate() + '/' + year)
    });
	});
		
})(window.jQuery);
