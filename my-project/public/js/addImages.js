var counter=0;
jQuery(document).ready(function () {
    jQuery('.add_new_image').click(function (e) {
        e.preventDefault();
        var $holder=$('#product_images');
        var prototype=$holder.data('prototype');
        var newForm=prototype.replace(/__name__/g, counter);
        $("#imagesBox").append("<div class='row'>" + "<div class='col-md-10'>" + newForm + "</div></div>");
        counter++;
    });
});
