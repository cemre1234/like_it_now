jQuery(document).ready(function(){
    var changeLikeItAfterBefore = function() {
    var currentId = jQuery(this).attr('id');
    if(jQuery("#show_like_it_before").attr("checked") == "checked" && 
        jQuery("#show_like_it_after").attr("checked") == "checked") {
        if(currentId == "show_like_it_after") {
            jQuery("#show_like_it_before").removeAttr("checked");
        }else{
            jQuery("#show_like_it_after").removeAttr("checked");
        }
    }
};

jQuery("#show_like_it_before").click(changeLikeItAfterBefore);
jQuery("#show_like_it_after").click(changeLikeItAfterBefore);
});