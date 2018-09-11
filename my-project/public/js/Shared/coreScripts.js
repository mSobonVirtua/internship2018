function deleteConfirm()
{
    return confirm("Are you sure?");
}

/**
 * @param {number} numberOfColumns
 * @return boolean - Always return false, to prevent onClick
 * */
function changeNumberOfColumns(numberOfColumns)
{
    const productTemplates = document.querySelectorAll(".product-template");
    for (let i = 0; i < productTemplates.length; i++) {
        for (let j = 1; j <= 12; j++) {
            productTemplates[i].classList.remove(`col-${j}`);
        }

        if (numberOfColumns === 1) {
            productTemplates[i].classList.toggle('col-12');
        } else if (numberOfColumns === 2) {
            productTemplates[i].classList.toggle('col-6');
        } else if (numberOfColumns === 3) {
            productTemplates[i].classList.toggle('col-4');
        } else if (numberOfColumns === 4) {
            productTemplates[i].classList.toggle('col-3');
        } else if (numberOfColumns === 6) {
            productTemplates[i].classList.toggle('col-2');
        } else if (numberOfColumns === 12) {
            productTemplates[i].classList.toggle('col-1');
        }
    }
    return false;
}