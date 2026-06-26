(() => {
    const gsap = window.gsap;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealItems = Array.from(document.querySelectorAll('[data-home-reveal]'));
    const cards = Array.from(document.querySelectorAll('[data-home-card]'));

    if (!gsap || reduceMotion) {
        revealItems.forEach((item) => {
            item.style.opacity = '1';
            item.style.transform = 'none';
        });
        return;
    }

    gsap.set(revealItems, { autoAlpha: 0, y: 18 });

    gsap.to(revealItems, {
        autoAlpha: 1,
        y: 0,
        duration: 0.58,
        ease: 'power3.out',
        stagger: 0.055,
        delay: 0.08,
    });

    cards.forEach((card) => {
        card.addEventListener('mouseenter', () => {
            gsap.to(card, {
                y: -4,
                borderColor: '#191c1f',
                boxShadow: '0 18px 38px rgba(25, 28, 31, 0.08)',
                duration: 0.24,
                ease: 'power2.out',
                overwrite: true,
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                y: 0,
                borderColor: '#c4c7c7',
                boxShadow: '0 0 0 rgba(25, 28, 31, 0)',
                duration: 0.24,
                ease: 'power2.out',
                overwrite: true,
            });
        });
    });
})();
