<script>
// Navbar & hamburger logic (window.App sudah diset di <head>)

window.AppDialogs = {
    confirm(title, message, warning, confirmLabel = 'Lanjutkan') {
        return new Promise((resolve) => {
            const dialog = document.createElement('dialog');
            dialog.className = 'send-success-dialog app-dialog';
            dialog.setAttribute('aria-labelledby', 'app-dialog-title');

            const heading = document.createElement('h2');
            heading.id = 'app-dialog-title';
            heading.textContent = title;

            const copy = document.createElement('p');
            copy.className = 'send-success-dialog__copy';
            copy.textContent = message;

            const warningCopy = document.createElement('p');
            warningCopy.className = 'app-dialog-warning';
            warningCopy.textContent = warning;

            const actions = document.createElement('div');
            actions.className = 'app-dialog-actions';

            const cancelButton = document.createElement('button');
            cancelButton.className = 'btn-seal send-success-dialog__button app-dialog-cancel';
            cancelButton.type = 'button';
            cancelButton.textContent = 'Batal';

            const confirmButton = document.createElement('button');
            confirmButton.className = 'btn-seal send-success-dialog__button app-dialog-danger';
            confirmButton.type = 'button';
            confirmButton.textContent = confirmLabel;

            actions.append(cancelButton, confirmButton);
            dialog.append(heading, copy);
            if (warning) dialog.appendChild(warningCopy);
            dialog.appendChild(actions);
            document.body.appendChild(dialog);

            let confirmed = false;
            cancelButton.addEventListener('click', () => dialog.close());
            confirmButton.addEventListener('click', () => {
                confirmed = true;
                dialog.close();
            });
            dialog.addEventListener('close', () => {
                dialog.remove();
                resolve(confirmed);
            }, { once: true });

            dialog.showModal();
            confirmButton.focus();
        });
    },

    notice(title, message, type) {
        return new Promise((resolve) => {
            const dialog = document.createElement('dialog');
            dialog.className = 'send-success-dialog app-dialog';
            dialog.setAttribute('aria-labelledby', 'app-dialog-title');

            const mark = document.createElement('div');
            mark.className = 'send-success-dialog__mark';
            mark.setAttribute('aria-hidden', 'true');
            mark.textContent = type === 'success' ? '✓' : '!';

            const heading = document.createElement('h2');
            heading.id = 'app-dialog-title';
            heading.textContent = title;

            const copy = document.createElement('p');
            copy.className = 'send-success-dialog__copy';
            copy.textContent = message;

            const closeButton = document.createElement('button');
            closeButton.className = 'btn-seal send-success-dialog__button';
            closeButton.type = 'button';
            closeButton.textContent = 'Oke';

            dialog.classList.toggle('app-dialog-error', type === 'error');
            dialog.append(mark, heading, copy, closeButton);
            document.body.appendChild(dialog);

            closeButton.addEventListener('click', () => dialog.close());
            dialog.addEventListener('close', () => {
                dialog.remove();
                resolve();
            }, { once: true });
            dialog.showModal();
            closeButton.focus();
        });
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const messageList = document.getElementById('message-list');
    if (messageList) {
        const username = messageList.dataset.username;
        const baseUrl = (window.App && window.App.baseUrl !== undefined) ? window.App.baseUrl : '';

        messageList.querySelectorAll('.message-card').forEach((card) => {
            const messageId = card.dataset.messageId;
            const messageContent = card.querySelector('.message-content')?.textContent?.trim() || '';
            const shareUrl = new URL(
                baseUrl + '/u/' + encodeURIComponent(username) + '?m=' + encodeURIComponent(messageId) + '#message-' + encodeURIComponent(messageId),
                window.location.origin
            ).toString();
            const shareText = `"${messageContent.substring(0, 100)}${messageContent.length > 100 ? '...' : ''}" - @${username}`;

            card.querySelectorAll('a[data-action="share"]').forEach((link) => {
                const shareType = link.dataset.type;
                if (shareType === 'twitter') {
                    link.href = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`;
                } else if (shareType === 'whatsapp') {
                    link.href = `https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`;
                } else if (shareType === 'facebook') {
                    link.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}&quote=${encodeURIComponent(shareText)}`;
                }
            });
        });
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!prefersReducedMotion && 'IntersectionObserver' in window) {
        const revealSelector = [
            '.page-wrap section',
            '.page-wrap .bento-card',
            '.page-wrap .home-feature-card',
            '.page-wrap .guideline-card',
            '.page-wrap .tip-item',
            '.page-wrap .contact-info-sidebar .info-card'
        ].join(', ');
        const revealItems = document.querySelectorAll(revealSelector);

        if (revealItems.length) {
            document.documentElement.classList.add('has-scroll-reveal');

            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px 0px -32px 0px'
            });

            revealItems.forEach((item) => {
                item.classList.add('scroll-reveal');
                revealObserver.observe(item);
            });
        }
    }

    // Navbar Scroll Effect (Passive for optimal scrolling performance)
    window.addEventListener('scroll', function () {
        const navbar = document.getElementById('navbar');
        const marquee = document.getElementById('marquee');

        if (window.scrollY > 50) {
            if (navbar) navbar.classList.add('scrolled');
            if (marquee) marquee.classList.add('hide');
        } else {
            if (navbar) navbar.classList.remove('scrolled');
            if (marquee) marquee.classList.remove('hide');
        }
    }, { passive: true });

    // Hamburger Mobile Menu
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (hamburgerBtn && mobileMenu) {
        hamburgerBtn.addEventListener('click', function () {
            const isOpen = mobileMenu.classList.toggle('open');
            hamburgerBtn.classList.toggle('open', isOpen);
            hamburgerBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        mobileMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                mobileMenu.classList.remove('open');
                hamburgerBtn.classList.remove('open');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
            });
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 780) {
                mobileMenu.classList.remove('open');
                hamburgerBtn.classList.remove('open');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>
</body>
</html>