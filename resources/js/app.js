const onlyDigits = (value) => value.replace(/\D+/g, '');

const maskCpf = (value) => onlyDigits(value)
    .slice(0, 11)
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d{1,2})$/, '$1-$2');

const maskCpfCnpj = (value) => {
    const digits = onlyDigits(value).slice(0, 14);

    if (digits.length > 11) {
        return digits
            .replace(/(\d{2})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1/$2')
            .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }

    return maskCpf(digits);
};

const maskPhone = (value) => {
    const digits = onlyDigits(value).slice(0, 11);

    if (digits.length <= 10) {
        return digits
            .replace(/(\d{2})(\d)/, '($1) $2')
            .replace(/(\d{4})(\d{1,4})$/, '$1-$2');
    }

    return digits
        .replace(/(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{5})(\d{1,4})$/, '$1-$2');
};

const masks = {
    cpf: maskCpf,
    cpfCnpj: maskCpfCnpj,
    phone: maskPhone,
};

document.querySelectorAll('[data-mask]').forEach((input) => {
    const applyMask = () => {
        input.value = masks[input.dataset.mask](input.value);
    };

    input.addEventListener('input', applyMask);
    applyMask();
});

document.querySelectorAll('[data-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = document.getElementById(button.dataset.modalOpen);

        if (modal instanceof HTMLDialogElement) {
            modal.showModal();
        }
    });
});

document.querySelectorAll('dialog').forEach((modal) => {
    modal.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => modal.close());
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.close();
        }
    });
});
