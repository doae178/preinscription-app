

document.addEventListener('DOMContentLoaded', function () {
    const openModalBtn = document.getElementById('open-modal');
    const modal = document.getElementById('modal-cin');
    const closeModalBtn = document.getElementById('modal-close');
    const inputCin = document.getElementById('cin');

    if (openModalBtn && modal) {
        openModalBtn.addEventListener('click', function (e) {
            e.preventDefault(); 
            modal.style.display = 'flex'; 
            if (inputCin) inputCin.focus(); 
        });
    }

    if (closeModalBtn && modal) {
        closeModalBtn.addEventListener('click', function () {
            modal.style.display = 'none';
        });
    }

    if (modal) {
        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});
