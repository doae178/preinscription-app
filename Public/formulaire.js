document.addEventListener('DOMContentLoaded', function () {
    // --- Variables du formulaire ---
    const formationSelect = document.getElementById('formation');
    const filiereSelect = document.getElementById('filiere');
    const modeSelect = document.getElementById('mode'); 
    const btnRemplir = document.getElementById('btn-remplir');
    const blocDossier = document.getElementById('dossier-fields');
    const fonctionnaireContainer = document.getElementById('fonctionnaireContainer');

    // Conteneur message délai
    let msgContainer = document.createElement('div');
    msgContainer.id = 'message-verif-delai';
    msgContainer.style.marginTop = '15px';
    msgContainer.style.fontWeight = 'bold';
    document.querySelector('form').insertBefore(msgContainer, blocDossier);

    blocDossier.style.display = 'none';
    modeSelect.disabled = true; 

    // --- Fonction pour afficher ou cacher la section fonctionnaire ---
    function toggleFonctionnaireSection() {
        const selectedOption = formationSelect.options[formationSelect.selectedIndex];
        if (!selectedOption) return;
        const nomFormation = selectedOption.getAttribute('data-nom') || '';

        if (nomFormation === 'Master' || nomFormation === 'Doctorat') {
            fonctionnaireContainer.style.display = 'block';
        } else {
            fonctionnaireContainer.style.display = 'none';
            // Reset des valeurs
            document.getElementById('fonctionnaire').value = '';
            document.getElementById('typeFonctionnaireContainer').style.display = 'none';
            document.getElementById('type_fonctionnaire').value = '';
        }
    }

    // --- Événement changement formation ---
    formationSelect.addEventListener('change', function () {
        const formationId = this.value;

        toggleFonctionnaireSection();

        if (formationId) {
            // Chargement des filières
            fetch('get_filieres.php?formation_id=' + encodeURIComponent(formationId))
                .then(response => response.json())
                .then(data => {
                    filiereSelect.innerHTML = '<option value="">-- Sélectionnez une filière --</option>';
                    data.forEach(filiere => {
                        filiereSelect.innerHTML += `<option value="${filiere.id_filiere}" data-mode="${filiere.nom_mode}">${filiere.nom_filiere}</option>`;
                    });
                    filiereSelect.disabled = false;
                })
                .catch(err => {
                    console.error('Erreur chargement filières:', err);
                    filiereSelect.innerHTML = '<option value="">-- Erreur chargement --</option>';
                    filiereSelect.disabled = true;
                });

            // Chargement des modes
            fetch('get_modes.php?formation_id=' + encodeURIComponent(formationId))
                .then(response => response.json())
                .then(data => {
                    modeSelect.innerHTML = '<option value="">-- Sélectionnez un mode --</option>';
                    data.forEach(mode => {
                        modeSelect.innerHTML += `<option value="${mode.id_mode}">${mode.nom_mode}</option>`;
                    });
                    modeSelect.disabled = false;
                })
                .catch(err => {
                    console.error('Erreur chargement modes:', err);
                    modeSelect.innerHTML = '<option value="">-- Erreur chargement --</option>';
                    modeSelect.disabled = true;
                });
        } else {
            filiereSelect.innerHTML = '<option value="">-- Sélectionnez une filière --</option>';
            filiereSelect.disabled = true;
            modeSelect.innerHTML = '<option value="">-- Sélectionnez un mode --</option>';
            modeSelect.disabled = true;
        }

        blocDossier.style.display = 'none';
        msgContainer.textContent = '';
    });

    // --- Bouton remplir le formulaire ---
    btnRemplir.addEventListener('click', function () {
        const formationId = formationSelect.value;

        if (!formationId) {
            msgContainer.style.color = 'red';
            msgContainer.textContent = "Veuillez sélectionner une formation.";
            blocDossier.style.display = 'none';
            return;
        }

        msgContainer.style.color = 'black';
        msgContainer.textContent = "Vérification du délai en cours...";

        fetch('verif_delai.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ formation_id: formationId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.delai_en_cours === true || data.delai_en_cours == 1) {
                    msgContainer.style.color = 'green';
                    msgContainer.textContent = data.message || "Vous pouvez remplir le formulaire.";
                    blocDossier.style.display = 'block';
                } else {
                    msgContainer.style.color = 'red';
                    msgContainer.textContent = data.message || "Le délai est terminé.";
                    blocDossier.style.display = 'none';
                }
            })
            .catch(err => {
                console.error('Erreur lors de la vérification du délai:', err);
                msgContainer.style.color = 'red';
                msgContainer.textContent = "Erreur serveur, veuillez réessayer plus tard.";
                blocDossier.style.display = 'none';
            });
    });

    // --- Gestion handicap ---
    window.toggleTypeHandicap = function () {
        const select = document.getElementById('handicape');
        const container = document.getElementById('typeHandicapContainer');
        if (select.value === 'oui') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            document.getElementById('type_handicap').value = '';
        }
    };

    // --- Gestion fonctionnaire ---
    window.toggleTypeFonctionnaire = function () {
        const select = document.getElementById('fonctionnaire');
        const container = document.getElementById('typeFonctionnaireContainer');
        if (select.value === 'oui') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            document.getElementById('type_fonctionnaire').value = '';
        }
    };

    toggleFonctionnaireSection();

  

    const nomArabeInput = document.getElementById('nom_arabe');
    const keyboardContainer = document.getElementById('keyboard-container-nom_arabe');

    if (nomArabeInput && keyboardContainer) {
        const keyboard = new SimpleKeyboard.default({
            onChange: input => nomArabeInput.value = input,
            onKeyPress: button => {
                if (button === "{shift}" || button === "{lock}") handleShift();
            },
            layout: {
                default: [
                    "ض ص ث ق ف غ ع ه خ ح ج د",
                    "ش س ي ب ل ا ت ن م ك ط",
                    "ئ ء ؤ ر لا ى ة و ز ظ",
                    "{space}"
                ]
            },
            theme: "hg-theme-default hg-layout-default",
            display: {
                '{space}': 'Espace',
                '{shift}': 'Shift',
                '{lock}': 'Caps',
            },
            physicalKeyboardHighlight: true,
            physicalKeyboardHighlightPress: true,
            syncInstanceInputs: true,
            inputName: "nom_arabe",
            rootElement: keyboardContainer
        });

        function handleShift() {
            let currentLayout = keyboard.options.layoutName;
            let shiftToggle = currentLayout === "default" ? "shift" : "default";
            keyboard.setOptions({ layoutName: shiftToggle });
        }

        nomArabeInput.addEventListener('input', event => {
            keyboard.setInput(event.target.value);
        });
    } else {
        console.error("Clavier arabe : champ ou conteneur clavier non trouvé.");
    }
});
