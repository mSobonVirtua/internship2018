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
    if(!confirm("Do you really want delete this?")) return;
    const response = fetch(path ,{
        method: "DELETE"
    });
    response
        .then(response=>{
            if(!response.ok){
                return Promise.reject(response.json());
            }else{
                return response.json();
            }
        })
        .then((response)=>{
            img.remove();
            alert(response.message);
        })
        .catch((err)=>{
            err.then((e)=>{
                alert(e.message);
            });
        });
}