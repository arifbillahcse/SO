export function initializeLeadForms(selector) {
    document.querySelectorAll(selector).forEach((root, index) => {
        const button = root.querySelector('.cta-submit, .sp-submit');
        if (!button || !root.dataset.endpoint || root.dataset.leadFormBound) return;
        root.dataset.leadFormBound = 'true';
        root.setAttribute('role', 'form');
        root.setAttribute('aria-label', root.dataset.endpoint.includes('affiliate') ? 'Affiliate referral form' : 'Project enquiry form');
        root.addEventListener('keydown', event => {
            if (event.key === 'Enter' && event.target instanceof HTMLInputElement && event.target.name !== 'company_website') {
                event.preventDefault(); button.click();
            }
        });
        const original = button.innerHTML;
        const fields = [...root.querySelectorAll('[name]')];
        const feedback = document.createElement('p');
        feedback.className = 'form-feedback';
        feedback.id = 'lead-form-feedback-' + index;
        feedback.setAttribute('role', 'status');
        feedback.tabIndex = -1;
        feedback.hidden = true;
        root.append(feedback);
        fields.forEach(field => {
            if (['name', 'email', 'message'].includes(field.name)) field.required = true;
            field.setAttribute('aria-describedby', [field.getAttribute('aria-describedby'), feedback.id].filter(Boolean).join(' '));
        });
        const show = (message, isError = false) => {
            feedback.textContent = message;
            feedback.hidden = false;
            feedback.classList.toggle('is-error', isError);
        };
        button.addEventListener('click', async event => {
            event.preventDefault();
            if (button.disabled) return;
            feedback.hidden = true;
            fields.forEach(field => field.removeAttribute('aria-invalid'));
            const invalid = fields.find(field => !field.checkValidity());
            if (invalid) {
                invalid.setAttribute('aria-invalid', 'true');
                show(invalid.validationMessage || 'Please check your details.', true);
                invalid.focus();
                return;
            }
            button.disabled = true;
            button.textContent = 'Sending?';
            try {
                const response = await fetch(root.dataset.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', 'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify(Object.fromEntries(fields.map(field => [field.name, field.value]))),
                });
                const data = await response.json().catch(() => ({}));
                if (response.ok) {
                    show(data.message || 'Thank you. Your message has been received.');
                    fields.forEach(field => {
                        if (field instanceof HTMLSelectElement) field.selectedIndex = 0;
                        else field.value = '';
                    });
                    feedback.focus();
                } else if (response.status === 422) {
                    Object.keys(data.errors || {}).forEach(name => fields.find(field => field.name === name)?.setAttribute('aria-invalid', 'true'));
                    show(Object.values(data.errors || {})[0]?.[0] || 'Please check your details and try again.', true);
                    const field = fields.find(field => field.getAttribute('aria-invalid') === 'true' && field.name !== 'company_website');
                    if (field) field.focus(); else feedback.focus();
                } else {
                    show(response.status === 429 ? 'Too many attempts. Please wait a minute and try again.'
                        : response.status === 419 ? 'Your session expired. Refresh the page and try again.'
                        : 'Your message could not be sent. Please try again or use our Contact Us page.', true);
                    feedback.focus();
                }
            } catch {
                show('The connection was interrupted. Please try again.', true);
                feedback.focus();
            } finally {
                button.innerHTML = original;
                button.disabled = false;
            }
        });
    });
}
