document.addEventListener('DOMContentLoaded', () => {
  const btnViewContent = document.getElementById('btnViewContent');
  const modal = document.getElementById('fileModal');
  const modalCloseBtn = document.getElementById('modalCloseBtn');
  // const fileTextContent = document.getElementById('fileTextContent'); // Non utilisé dans ce code

  if (!btnViewContent || !modal || !modalCloseBtn) {
    console.warn("Certains éléments nécessaires sont manquants dans le DOM");
    return;
  }

  btnViewContent.addEventListener('click', () => {
    // Ouvre la modale, contenu déjà injecté côté PHP
    modal.style.display = 'block';
  });

  modalCloseBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  window.addEventListener('click', (event) => {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });

});
