class RolesAutocomplete {
    constructor(selector) {
        this.selector = selector;
        this.selector.style.display = 'none';
        this.createInputSelector();
        this.delay = null;

        this.inputField.addEventListener('input', () => {
            clearTimeout(this.delay);
            this.delay = setTimeout(() => {
                this.serveSuggestions(this.inputField.value.toLowerCase());
            }, 500);
        });
        this.inputField.addEventListener('focus', () => {
            this.serveSuggestions(this.inputField.value.toLowerCase());
        });
        document.addEventListener('click', (e) => {
            if (!this.inputSelector.contains(e.target))
                this.suggestions.classList.add('hidden');
        });
    }

    createInputSelector() {
        this.inputSelector = document.createElement('div');
        this.inputSelector.classList.add('selector-container');

        this.selectedRolesContainer = document.createElement('ul');
        this.selectedRolesContainer.classList.add('selector-roles-container');
        this.inputSelector.appendChild(this.selectedRolesContainer);

        this.inputField = document.createElement('input');
        this.inputField.classList.add('selector-input');
        this.inputField.type = 'text';
        this.inputSelector.appendChild(this.inputField);

        this.suggestions = document.createElement('ul');
        this.suggestions.classList.add('selector-suggestions');
        this.suggestions.classList.add('hidden');
        this.inputSelector.appendChild(this.suggestions);

        this.selector.parentNode.insertBefore(this.inputSelector, this.selector);

        this.roleOptions = Array.from(this.selector.options).map((role) => {
            return {
                originalElement: role,
                value: role.value,
                text: role.text,
            }
        });
    }

    serveSuggestions(input) {
        let foundMatch = false;
        this.suggestions.innerHTML = '';

        this.roleOptions.forEach((role) => {
            if (role.originalElement.selected)
                return;

            if (role.text.toLowerCase().includes(input)) {
                foundMatch = true;

                const suggestedRole = document.createElement('li');
                suggestedRole.classList.add('suggested-role');
                suggestedRole.innerHTML = role.text;
                suggestedRole.addEventListener('click', () => {
                    role.originalElement.selected = true;
                    this.suggestions.classList.add('hidden');
                    this.inputField.value = '';
                    this.showSelectedRoles();
                });

                this.suggestions.appendChild(suggestedRole);
                this.selector.dispatchEvent(new Event('change'));
            }
        });

        if (foundMatch)
            this.suggestions.classList.remove('hidden');
        else
            this.suggestions.classList.add('hidden');
    }

    showSelectedRoles() {
        this.selectedRolesContainer.innerHTML = '';

        this.roleOptions.forEach((role) => {
            if (role.originalElement.selected) {
                const selectedRole = document.createElement('li');
                selectedRole.classList.add('selected-role');
                selectedRole.innerHTML = role.text;

                const removeBtn = document.createElement('span');
                removeBtn.classList.add('remove-role-btn');

                removeBtn.addEventListener('click', () => {
                    role.originalElement.selected = false;
                    this.showSelectedRoles();
                })

                selectedRole.appendChild(removeBtn);
                this.selectedRolesContainer.appendChild(selectedRole);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const autocomplete =  new RolesAutocomplete(document.querySelector('.autocomplete'));
});
