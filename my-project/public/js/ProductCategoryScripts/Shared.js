/**
 * VI-37 ProductCategoryStyles
 *
 * @category   Scripts
 * @package    Virtua_ProductCategoryScripts
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */


/**
 * @param {string} path
 * @param {Element} img
 * @returns void
 * */
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
            BootstrapAlertSuccess(response.message);
        })
        .catch((err)=>{
            err.then((e)=>{
                BootstrapAlertDanger(e.error);
            });
        });
}

/**
 * @param {string} message
 * @returns void
 * */
function BootstrapAlertSuccess(message){
    const flashMessageContainer = document.querySelector('#flashMessage-container');
    const flashMessageContent = document.createElement('div');
    flashMessageContent.innerHTML = `
        <div class="alert alert-dismissible alert-success fade show">
            <div>${message}</div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    flashMessageContainer.appendChild(flashMessageContent);
}

/**
 * @param {string} message
 * @returns void
 * */
function BootstrapAlertDanger(message){
    console.log("test")
    const flashMessageContainer = document.querySelector('#flashMessage-container');
    const flashMessageContent = document.createElement('div');
    flashMessageContent.innerHTML = `
        <div class="alert alert-dismissible alert-danger fade show">
            <div>${message}</div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    flashMessageContainer.appendChild(flashMessageContent);
}

