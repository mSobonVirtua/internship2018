
function onSubmitEditForm(path)
{
    const addImageForm = document.forms.namedItem('image');
    const dataForm = new FormData(addImageForm);
    const response = fetch(`${path}`, {
        method: 'POST',
        body: dataForm
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
            BootstrapAlertSuccess(response.message);
        })
        .catch((err)=>{
            console.log(err)
            err.then((e)=>{
                BootstrapAlertDanger(e.error);
            });
        });
    return false;
}
