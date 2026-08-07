class DropdownContacts {
    constructor() {
        this.expandButton = document.getElementById('dropdown-expand-btn');
        this.expandButton.addEventListener('click', (e) => {
            e.preventDefault();

            const dropdownContent = document.getElementById('dropdown-contacts-content');
            const isExpanded = this.expandButton.getAttribute('aria-expanded');

            if (isExpanded === 'false')
                this.open(dropdownContent);
            else
                this.close(dropdownContent);
        });
    }

    open(dropdownContent) {
        dropdownContent.classList.remove('hidden');
        this.expandButton.setAttribute('aria-expanded', 'true');
    }

    close(dropdownContent) {
        dropdownContent.classList.add('hidden');
        this.expandButton.setAttribute('aria-expanded', 'false');
    }
}

document.addEventListener('DOMContentLoaded', ()=> {
    const dropdownContacts = new DropdownContacts();
})
