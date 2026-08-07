class AccountDeleteWarning {
    constructor() {
        this.modal = document.getElementById('delete-warning');
        this.form = this.modal.querySelector('form');
        this.cancelButton = this.modal.querySelector('.cancel-delete-btn');

        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal && this.modal)
                this.hide();
        })

        this.cancelButton.addEventListener('click', (e) => {
            if (this.modal)
                this.hide();
        });
    }

    show(deleteUrl) {
        this.modal.classList.remove('hidden');
        this.form.action = deleteUrl;
    }

    hide() {
        this.modal.classList.add('hidden');
        this.form.action = '';
    }
}

document.addEventListener('DOMContentLoaded', () =>  {
    const deleteWarning = new AccountDeleteWarning();

    const deleteButtons = document.querySelectorAll('.delete-account-btn');
    deleteButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            deleteWarning.show(btn.getAttribute('href'));
        });
    });
});
