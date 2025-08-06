document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-supprimer').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;

            if (confirm("❗ Es-tu sûr de vouloir supprimer ce dossier et l'étudiant associé ?")) {
                fetch('supprimer_etudiant.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id_etudiant=' + encodeURIComponent(id)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("✅ Étudiant et dossier supprimés.");
                        location.reload();
                    } else {
                        alert("❌ Erreur : " + data.error);
                    }
                });
            }
        });
    });
});
