/**
 * VI-37 ProductCategoryStyles
 *
 * @category   Scripts
 * @package    Virtua_ProductCategoryScripts
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */


function RemoveImageFromCategory(path, img)
{
    console.log(path)
    const response = fetch(path ,{
        method: "DELETE"
    });
    response
        .then((response)=>{
            img.remove();
        });
}