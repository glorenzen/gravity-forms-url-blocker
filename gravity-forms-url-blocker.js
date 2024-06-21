document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('inputs_container').addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-input-group')) {
            e.target.parentNode.remove();
        }
    });

    document.getElementById('add_more').addEventListener('click', function() {
        const container = document.getElementById('inputs_container');
        const newInputGroup = document.createElement('div');
        newInputGroup.className = 'input-group';
        newInputGroup.innerHTML = `
            <input type="text" name="form_id[]" placeholder="Form ID" />
            <input type="text" name="textarea_id[]" placeholder="Textarea ID" />
            <button type="button" class="delete-input-group">Delete</button>
        `;
        container.appendChild(newInputGroup);
    });
});