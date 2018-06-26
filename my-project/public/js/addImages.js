var counter=0;
jQuery(document).ready(function () {

    var $diver=$('#product_images');
    jQuery('.add-another-collection-widget').click(function (e) {
        e.preventDefault();


       /* var $holder=$('#product_images');
        var prototype=$holder.data('prototype');
        var newForm=prototype.replace(/__name__/g, counter);
        var newLi=$('<li style="list-style-type: none"></li>').append(newForm);
        $('button.add-another-collection-widget').after(newLi);
        counter++;*/
    });
});