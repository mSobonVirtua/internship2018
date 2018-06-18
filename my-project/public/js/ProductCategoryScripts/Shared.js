
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