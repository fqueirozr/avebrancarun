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

const maskCep = (value) => onlyDigits(value)
    .slice(0, 8)
    .replace(/(\d{5})(\d{1,3})$/, '$1-$2');

const masks = {
    cep: maskCep,
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

document.querySelectorAll('[data-registration-form]').forEach((form) => {
    const steps = Array.from(form.querySelectorAll('[data-registration-step]'));
    const guardianStep = form.querySelector('[data-guardian-step]');
    const birthDateInput = form.querySelector('input[name="birth_date"]');
    const modalityOptions = Array.from(form.querySelectorAll('[data-modality-option]'));
    const noCompatibleModality = form.querySelector('[data-no-compatible-modality]');
    const legalRepresentativeCheckbox = form.querySelector('[data-legal-representative-checkbox]');
    const previousButton = form.querySelector('[data-registration-prev]');
    const nextButton = form.querySelector('[data-registration-next]');
    const submitButton = form.querySelector('[data-registration-submit]');
    const progressLabel = form.querySelector('[data-registration-progress-label]');
    const progressTitle = form.querySelector('[data-registration-progress-title]');
    const progressBar = form.querySelector('[data-registration-progress-bar]');
    const review = form.querySelector('[data-registration-review]');
    const specialKitOptions = Array.from(form.querySelectorAll('[data-special-kit]'));
    const specialKitModal = document.getElementById('special-kit-rules-modal');
    const specialKitAcknowledgement = document.querySelector('[data-special-kit-acknowledgement]');
    const specialKitConfirm = document.querySelector('[data-special-kit-confirm]');
    const specialKitRulesContent = document.querySelector('[data-special-kit-rules-content]');
    const specialKitRulesTitle = document.querySelector('[data-special-kit-rules-title]');
    let currentStepIndex = 0;

    const fieldLabels = {
        athlete_name: 'Nome do atleta',
        shirt_size: 'Tamanho da camisa',
        participant_cpf: 'CPF do atleta',
        birth_date: 'Data de nascimento',
        sex: 'Sexo',
        phone: 'Telefone',
        email: 'E-mail',
        guardian_name: 'Nome do responsavel legal',
        guardian_cpf: 'CPF do responsavel legal',
        filled_by_legal_representative: 'Preenchida pelo representante legal',
        billing_name: 'Nome do pagador',
        billing_document: 'CPF/CNPJ do pagador',
        billing_address: 'Endereco',
        billing_province: 'Bairro',
        billing_address_number: 'Numero',
        billing_postal_code: 'CEP',
        race_modality_id: 'Prova',
        kit_id: 'Kit',
        emergency_contact_name: 'Contato de emergencia',
        emergency_contact_phone: 'Telefone de emergencia',
    };

    const visibleSteps = () => steps.filter((step) => !step.dataset.skipStep);

    const isMinorBirthDate = () => {
        if (!birthDateInput?.value) {
            return false;
        }

        const birthDate = new Date(`${birthDateInput.value}T00:00:00`);

        if (Number.isNaN(birthDate.getTime())) {
            return false;
        }

        const adultDate = new Date();
        adultDate.setFullYear(adultDate.getFullYear() - 18);
        adultDate.setHours(0, 0, 0, 0);

        return birthDate > adultDate;
    };

    const syncGuardianStep = () => {
        if (!guardianStep) {
            return;
        }

        const isMinor = isMinorBirthDate();

        if (isMinor && legalRepresentativeCheckbox) {
            legalRepresentativeCheckbox.checked = true;
        }

        const shouldShow = isMinor || legalRepresentativeCheckbox?.checked;
        guardianStep.dataset.skipStep = shouldShow ? '' : 'true';
        guardianStep.hidden = !shouldShow;

        guardianStep.querySelectorAll('input, textarea, select').forEach((field) => {
            field.required = shouldShow;

            if (!shouldShow) {
                field.value = '';
            }
        });
    };

    const ageOnDate = (birthDate, referenceDate) => {
        let age = referenceDate.getFullYear() - birthDate.getFullYear();
        const hasNotHadBirthday = referenceDate.getMonth() < birthDate.getMonth()
            || (referenceDate.getMonth() === birthDate.getMonth() && referenceDate.getDate() < birthDate.getDate());

        if (hasNotHadBirthday) {
            age--;
        }

        return age;
    };

    const syncModalityOptions = () => {
        const birthDate = birthDateInput?.value ? new Date(`${birthDateInput.value}T00:00:00`) : null;
        let compatibleOptions = 0;

        modalityOptions.forEach((option) => {
            const input = option.querySelector('input[name="race_modality_id"]');
            const raceDate = new Date(`${option.dataset.raceDate}T00:00:00`);
            const age = birthDate && !Number.isNaN(birthDate.getTime()) ? ageOnDate(birthDate, raceDate) : null;
            const ageStart = option.dataset.ageStart === '' ? null : Number(option.dataset.ageStart);
            const ageEnd = option.dataset.ageEnd === '' ? null : Number(option.dataset.ageEnd);
            const isCompatible = age !== null
                && (ageStart === null || age >= ageStart)
                && (ageEnd === null || age <= ageEnd);

            option.hidden = !isCompatible;
            input.disabled = !isCompatible || input.dataset.unavailable === 'true';

            if (!isCompatible) {
                input.checked = false;
            } else {
                compatibleOptions++;
            }
        });

        if (noCompatibleModality) {
            noCompatibleModality.hidden = compatibleOptions > 0;
            noCompatibleModality.textContent = birthDate
                ? 'Nenhuma prova está disponível para a idade do atleta.'
                : 'Informe a data de nascimento do atleta para visualizar as provas disponíveis para a idade dele.';
        }
    };

    const selectedLabel = (field) => {
        if (field.type === 'radio') {
            const checked = form.querySelector(`input[name="${field.name}"]:checked`);

            return checked?.closest('label')?.innerText.trim() ?? '';
        }

        if (field.type === 'checkbox') {
            return field.checked ? 'Sim' : 'Nao';
        }

        return field.value.trim();
    };

    const updateReview = () => {
        if (!review) {
            return;
        }

        review.innerHTML = '';

        const fields = Array.from(form.querySelectorAll('input[name], textarea[name], select[name]'))
            .filter((field) => !field.closest('[data-registration-step]')?.dataset.skipStep)
            .filter((field) => field.name in fieldLabels)
            .filter((field) => field.type !== 'hidden')
            .filter((field, index, fieldsList) => field.type !== 'radio' || fieldsList.findIndex((item) => item.name === field.name) === index);

        fields.forEach((field) => {
            const value = selectedLabel(field) || 'Nao informado';
            const item = document.createElement('div');
            const term = document.createElement('dt');
            const description = document.createElement('dd');

            item.className = 'rounded-md border border-zinc-200 bg-white p-3';
            term.className = 'font-bold text-zinc-700';
            term.textContent = fieldLabels[field.name];
            description.className = 'mt-1 break-words text-zinc-950';
            description.textContent = value;
            item.append(term, description);
            review.appendChild(item);
        });
    };

    const validateCurrentStep = () => {
        const currentStep = visibleSteps()[currentStepIndex];
        const controls = Array.from(currentStep.querySelectorAll('input, textarea, select'));

        if (currentStep.querySelector('input[name="kit_id"]')
            && form.querySelector('[data-special-kit]:checked')
            && !specialKitAcknowledgement?.checked) {
            specialKitModal?.showModal();

            return false;
        }

        for (const control of controls) {
            if (!control.checkValidity()) {
                control.reportValidity();

                return false;
            }
        }

        return true;
    };

    const renderStep = () => {
        syncGuardianStep();

        const currentVisibleSteps = visibleSteps();
        currentStepIndex = Math.min(currentStepIndex, currentVisibleSteps.length - 1);

        steps.forEach((step) => {
            step.hidden = step !== currentVisibleSteps[currentStepIndex] || Boolean(step.dataset.skipStep);
        });

        const isFirstStep = currentStepIndex === 0;
        const isLastStep = currentStepIndex === currentVisibleSteps.length - 1;
        const progress = ((currentStepIndex + 1) / currentVisibleSteps.length) * 100;

        previousButton.hidden = isFirstStep;
        nextButton.hidden = isLastStep;
        submitButton.hidden = !isLastStep;
        progressLabel.textContent = `Etapa ${currentStepIndex + 1} de ${currentVisibleSteps.length}`;
        progressTitle.textContent = currentVisibleSteps[currentStepIndex].dataset.stepTitle;
        progressBar.style.width = `${progress}%`;

        if (isLastStep) {
            updateReview();
        }
    };

    birthDateInput?.addEventListener('change', () => {
        syncGuardianStep();
        syncModalityOptions();
        renderStep();
    });
    legalRepresentativeCheckbox?.addEventListener('change', syncGuardianStep);

    specialKitOptions.forEach((option) => {
        option.addEventListener('change', () => {
            if (option.checked && specialKitModal instanceof HTMLDialogElement) {
                const rules = option.closest('label')?.querySelector('[data-kit-rules-template]');

                if (rules && specialKitRulesContent) {
                    specialKitRulesContent.innerHTML = rules.innerHTML;
                }

                if (specialKitRulesTitle) {
                    specialKitRulesTitle.textContent = `Regras do ${option.dataset.kitName}`;
                }

                if (specialKitAcknowledgement) {
                    specialKitAcknowledgement.checked = false;
                    specialKitConfirm.disabled = true;
                }

                specialKitModal.showModal();
            }
        });
    });

    form.querySelectorAll('input[name="kit_id"]').forEach((option) => {
        option.addEventListener('change', () => {
            if (!option.matches('[data-special-kit]') && specialKitAcknowledgement) {
                specialKitAcknowledgement.checked = false;
                specialKitConfirm.disabled = true;
            }

            const shirtSizeField = form.querySelector('[data-shirt-size-field]');

            if (shirtSizeField) {
                const shirtSizeSelect = shirtSizeField.querySelector('select');

                shirtSizeField.hidden = option.dataset.hasShirt !== 'true';
                shirtSizeSelect.disabled = shirtSizeField.hidden;
                shirtSizeSelect.required = !shirtSizeField.hidden;

                if (shirtSizeField.hidden) {
                    shirtSizeSelect.value = '';
                }
            }
        });
    });

    form.querySelector('input[name="kit_id"]:checked')?.dispatchEvent(new Event('change'));

    specialKitAcknowledgement?.addEventListener('change', () => {
        specialKitConfirm.disabled = !specialKitAcknowledgement.checked;
    });

    specialKitConfirm?.addEventListener('click', () => {
        if (specialKitAcknowledgement?.checked && specialKitModal instanceof HTMLDialogElement) {
            specialKitModal.close();
        }
    });

    if (specialKitConfirm) {
        specialKitConfirm.disabled = !specialKitAcknowledgement?.checked;
    }

    previousButton?.addEventListener('click', () => {
        currentStepIndex = Math.max(0, currentStepIndex - 1);
        renderStep();
    });

    nextButton?.addEventListener('click', () => {
        if (!validateCurrentStep()) {
            return;
        }

        currentStepIndex = Math.min(visibleSteps().length - 1, currentStepIndex + 1);
        renderStep();
    });

    form.addEventListener('submit', updateReview);

    syncModalityOptions();
    renderStep();
});

document.querySelectorAll('[data-course-tabs]').forEach((tabsRoot) => {
    const tabs = Array.from(tabsRoot.querySelectorAll('[data-course-tab]'));
    const panels = Array.from(tabsRoot.querySelectorAll('[data-course-panel]'));

    const activateTab = (tab) => {
        tabs.forEach((currentTab) => {
            currentTab.setAttribute('aria-selected', currentTab === tab ? 'true' : 'false');
        });

        panels.forEach((panel) => {
            panel.hidden = panel.id !== tab.getAttribute('aria-controls');
        });
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => activateTab(tab));
    });

    const initialTab = tabs.find((tab) => `#${tab.getAttribute('aria-controls')}` === window.location.hash);

    if (initialTab) {
        activateTab(initialTab);
    }
});

document.querySelectorAll('a[href^="#percurso-"]').forEach((link) => {
    link.addEventListener('click', () => {
        const tab = document.querySelector(`[data-course-tab][aria-controls="${link.hash.slice(1)}"]`);

        if (tab instanceof HTMLButtonElement) {
            tab.click();
        }
    });
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

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (!prefersReducedMotion) {
    const parallaxLayers = Array.from(document.querySelectorAll('[data-parallax-speed]'));
    let parallaxFrame = null;

    const syncParallax = () => {
        parallaxFrame = null;

        parallaxLayers.forEach((layer) => {
            const speed = Number.parseFloat(layer.dataset.parallaxSpeed || '0.12');
            const bounds = layer.getBoundingClientRect();
            const viewportCenter = window.innerHeight / 2;
            const layerCenter = bounds.top + bounds.height / 2;
            const offset = (viewportCenter - layerCenter) * speed;

            layer.style.setProperty('--parallax-y', `${offset.toFixed(2)}px`);
        });
    };

    const requestParallaxSync = () => {
        if (parallaxFrame !== null) {
            return;
        }

        parallaxFrame = window.requestAnimationFrame(syncParallax);
    };

    if (parallaxLayers.length > 0) {
        window.addEventListener('scroll', requestParallaxSync, { passive: true });
        window.addEventListener('resize', requestParallaxSync);
        syncParallax();
    }
}

const revealItems = Array.from(document.querySelectorAll('[data-reveal]'));

if (revealItems.length > 0 && 'IntersectionObserver' in window && !prefersReducedMotion) {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            revealObserver.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px -12% 0px',
        threshold: 0.12,
    });

    revealItems.forEach((item) => revealObserver.observe(item));
} else {
    revealItems.forEach((item) => item.classList.add('is-visible'));
}
