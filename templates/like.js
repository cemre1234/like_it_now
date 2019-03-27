
function myFunction(x) {
  x.classList.toggle("fa-thumbs-down");
}

(function ($) {

    $(function() {

        if ($('.like').length > 0) {
            $('i').click(function () {
                $this = $(this);
                $this.attr("disabled", "disabled");

                data = {
                    'action': 'like_it',
                    'nonce': like_it.nonce,
                    'post': $this.attr('id')
                };

                $.ajax({
                    type: "post",
                    data: data,
                    url: like_it.url,
                    dataType: "json",
                    success: function (results) {
                        $this.removeAttr("disabled");
                        $this.parent().toggleClass('liked');
                        $this.parent().find('.LoveCount').text(results.likes);
                        $this.parent().find('.intitule').text(results.text);

                    },
                    error: function () {
                    }
                });
            });
        }
    });
    
})(jQuery);
