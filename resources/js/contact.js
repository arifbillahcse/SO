const form = document.getElementById('contactPageForm');

if (form) {
    const button = form.querySelector('[type="submit"]');
    const status = form.querySelector('[data-contact-status]');
    const originalButtonContent = button.innerHTML;
    let submitting = false;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (submitting || !form.reportValidity()) return;
        submitting = true;
        button.disabled = true;
        button.textContent = 'Sending?';
        status.hidden = true;
        form.querySelectorAll('[data-error-for]').forEach(node => node.textContent = '');
        form.querySelectorAll('[aria-invalid]').forEach(node => node.removeAttribute('aria-invalid'));

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));
            if (response.ok) {
                status.textContent = data.message || 'Thank you. Your message has been received.';
                status.hidden = false;
                form.querySelectorAll('input[name], textarea[name]').forEach(input => {
                    if (input.name !== '_token') input.value = '';
                });
                form.querySelector('select').selectedIndex = 0;
                status.focus();
            } else if (response.status === 422) {
                let firstInvalid = null;
                Object.entries(data.errors || {}).forEach(([name, errors]) => {
                    const field = form.elements.namedItem(name);
                    const errorNode = [...form.querySelectorAll('[data-error-for]')].find(node => node.dataset.errorFor === name);
                    if (errorNode) errorNode.textContent = errors[0];
                    if (field && name !== 'company_website') {
                        field.setAttribute('aria-invalid', 'true');
                        firstInvalid ||= field;
                    }
                });
                if (firstInvalid) firstInvalid.focus();
                else throw new Error(data.message || 'Please check your details and try again.');
            } else {
                throw new Error(response.status === 429
                    ? 'Too many attempts. Please wait a minute and try again.'
                    : response.status === 419
                        ? 'Your session expired. Refresh the page and try again.'
                        : 'We could not send your message. Please try again or use the contact details on this page.');
            }
        } catch (error) {
            status.textContent = error instanceof TypeError
                ? 'The connection was interrupted. Please try again.'
                : error.message;
            status.hidden = false;
            status.focus();
        } finally {
            submitting = false;
            button.disabled = false;
            button.innerHTML = originalButtonContent;
        }
    });
}
