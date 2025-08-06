
document.addEventListener('DOMContentLoaded', function() {
   
    document.querySelectorAll('.editable').forEach(td => {
        td.addEventListener('click', function() {
            if (td.querySelector('input')) return; 
            const currentValue = td.textContent.trim();
            const input = document.createElement('input');
            input.type = 'date';
            input.value = currentValue;
            input.style.width = '130px';
            td.textContent = '';
            td.appendChild(input);
            input.focus();

        
            input.addEventListener('blur', function() {
                const newValue = input.value;
                if (newValue && newValue !== currentValue) {
                    const id_delai = td.parentElement.getAttribute('data-id');
                    const field = td.getAttribute('data-field');
                    updateDate(id_delai, field, newValue, td);
                } else {
                    td.textContent = currentValue; 
                }
            });

          
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    input.blur();
                }
                if (e.key === 'Escape') {
                    td.textContent = currentValue;
                }
            });
        });
    });

   
    function updateDate(id_delai, field, value, cell) {
        fetch('delai.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'update',
                id_delai: id_delai,
                field: field,
                value: value
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                cell.textContent = value;
                showMessage(data.message, 'green');
            } else {
                showMessage(data.message, 'red');
                cell.textContent = cell.querySelector('input') ? cell.querySelector('input').value : value;
            }
        })
        .catch(() => {
            showMessage('Erreur réseau', 'red');
            cell.textContent = cell.querySelector('input') ? cell.querySelector('input').value : value;
        });
    }

    
    const addForm = document.getElementById('add-form');
    addForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(addForm);
        fetch('delai.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'green');
                addForm.reset();
                
                setTimeout(() => location.reload(), 1200);
            } else {
                showMessage(data.message, 'red');
            }
        })
        .catch(() => {
            showMessage('Erreur réseau', 'red');
        });
    });

    function showMessage(msg, color) {
        const div = document.getElementById('message');
        div.textContent = msg;
        div.style.color = color;
        setTimeout(() => { div.textContent = ''; }, 4000);
    }
});
