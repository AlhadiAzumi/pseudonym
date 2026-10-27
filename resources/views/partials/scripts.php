<script>
// Navbar & hamburger logic (window.App sudah diset di <head>)

document.addEventListener('DOMContentLoaded', function () {
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