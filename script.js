function showInlineMessage(targetId, message, type) {
    const target = document.getElementById(targetId);
    if (!target) {
        return;
    }

    target.textContent = message;
    target.className = `message ${type}`.trim();
}

function clearInlineMessage(targetId) {
    const target = document.getElementById(targetId);
    if (!target) {
        return;
    }

    target.textContent = '';
    target.className = 'message';
}

function validateEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value || '').trim());
}

function validateLoginForm(event) {
    const form = event.target;
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;

    clearInlineMessage('loginMessage');

    if (!email || !password) {
        event.preventDefault();
        showInlineMessage('loginMessage', 'Please enter both your email and password.', 'error');
        return;
    }

    if (!validateEmail(email)) {
        event.preventDefault();
        showInlineMessage('loginMessage', 'Please enter a valid email address.', 'error');
    }
}

function validateSignupForm(event) {
    const form = event.target;
    const fullName = document.getElementById('signupFullName').value.trim();
    const email = document.getElementById('signupEmail').value.trim();
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    clearInlineMessage('signupMessage');

    if (!fullName || !email || !password || !confirmPassword) {
        event.preventDefault();
        showInlineMessage('signupMessage', 'Please complete your name, email, password, and confirmation.', 'error');
        return;
    }

    if (!validateEmail(email)) {
        event.preventDefault();
        showInlineMessage('signupMessage', 'Please enter a valid email address.', 'error');
        return;
    }

    if (password.length < 8) {
        event.preventDefault();
        showInlineMessage('signupMessage', 'Password must contain at least 8 characters.', 'error');
        return;
    }

    if (password !== confirmPassword) {
        event.preventDefault();
        showInlineMessage('signupMessage', 'Passwords do not match.', 'error');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const landingPage = document.body.classList.contains('landing-page');

    clearInlineMessage('loginMessage');
    clearInlineMessage('signupMessage');

    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);
    }

    if (signupForm) {
        signupForm.addEventListener('submit', validateSignupForm);
    }

    if (landingPage) {
        const siteHeader = document.querySelector('.site-header');
        const navToggle = document.querySelector('.nav-toggle');
        const navPanel = document.querySelector('.nav-panel');
        const navLinks = document.querySelectorAll('.nav-links a');
        const revealTargets = document.querySelectorAll('.section-reveal');

        const updateHeaderState = () => {
            if (!siteHeader) {
                return;
            }

            siteHeader.classList.toggle('is-scrolled', window.scrollY > 8);
        };

        updateHeaderState();
        window.addEventListener('scroll', updateHeaderState, { passive: true });

        if (navToggle && navPanel) {
            const setNavState = (isOpen) => {
                navPanel.classList.toggle('is-open', isOpen);
                navToggle.setAttribute('aria-expanded', String(isOpen));
            };

            navToggle.addEventListener('click', () => {
                setNavState(!navPanel.classList.contains('is-open'));
            });

            navLinks.forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 860) {
                        setNavState(false);
                    }
                });
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 860) {
                    setNavState(false);
                }
            });
        }

        if ('IntersectionObserver' in window && revealTargets.length > 0) {
            const observer = new IntersectionObserver(
                (entries, revealObserver) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            revealObserver.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.16 }
            );

            revealTargets.forEach((target) => observer.observe(target));
        } else {
            revealTargets.forEach((target) => target.classList.add('is-visible'));
        }
    }
});

window.addEventListener('pageshow', () => {
    clearInlineMessage('loginMessage');
    clearInlineMessage('signupMessage');
});