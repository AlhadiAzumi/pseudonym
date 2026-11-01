<script>
// Navbar & hamburger logic (window.App sudah diset di <head>)

document.addEventListener('DOMContentLoaded', function () {
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