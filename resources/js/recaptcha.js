let apiPromise;

function loadApi() {
    if (!apiPromise) {
        apiPromise = new Promise((resolve, reject) => {
            const timeout = setTimeout(() => reject(new Error('Security check could not load. Refresh the page or check your connection.')), 12000);
            window.softorioRecaptchaReady = () => {
                clearTimeout(timeout);
                resolve(window.grecaptcha);
            };
            const script = document.createElement('script');
            script.src = 'https://www.google.com/recaptcha/api.js?onload=softorioRecaptchaReady&render=explicit';
            script.async = true;
            script.defer = true;
            script.onerror = () => {
                clearTimeout(timeout);
                reject(new Error('Security check could not load. Refresh the page or check your connection.'));
            };
            document.head.append(script);
        });
    }
    return apiPromise;
}

export function initializeRecaptcha(root, button) {
    const setting = document.querySelector('meta[name="recaptcha-site-key"]');
    if (!setting) return { token: async () => null, reset: () => {} };

    const container = document.createElement('div');
    container.className = 'recaptcha-container';
    const widget = document.createElement('div');
    const status = document.createElement('p');
    status.className = 'form-feedback';
    status.setAttribute('role', 'status');
    status.textContent = 'Loading security check...';
    container.append(widget, status);
    const anchor = root.querySelector('.contact-form-actions') || button;
    anchor.before(container);
    let api;
    let widgetId;
    let failure;
    const ready = loadApi().then(loaded => {
        api = loaded;
        widgetId = api.render(widget, {
            sitekey: setting.content,
            size: container.clientWidth < 304 ? 'compact' : 'normal',
            theme: document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light',
            callback: () => { status.hidden = true; },
            'expired-callback': () => { status.hidden = false; status.textContent = 'Security check expired. Please complete it again.'; },
            'error-callback': () => { status.hidden = false; status.textContent = 'Security check interrupted. Please retry or refresh the page.'; },
        });
        status.hidden = true;
    }).catch(error => {
        failure = error;
        status.textContent = error.message;
    });

    return {
        async token() {
            await ready;
            if (failure) throw failure;
            const token = api.getResponse(widgetId);
            if (!token) throw new Error('Please complete the security check before sending.');
            return token;
        },
        reset() {
            if (api && widgetId !== undefined) api.reset(widgetId);
        },
    };
}
