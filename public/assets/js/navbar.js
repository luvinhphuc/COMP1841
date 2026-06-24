(() => {
    const openButton = document.querySelector('[data-menu-open]');
    const closeButton = document.querySelector('[data-menu-close]');
    const overlay = document.querySelector('[data-menu-overlay]');
    const primaryItems = Array.from(document.querySelectorAll('[data-menu-primary-item]'));
    const triggers = Array.from(document.querySelectorAll('[data-menu-trigger]'));
    const panels = Array.from(document.querySelectorAll('[data-menu-panel]'));
    const panelContainer = document.querySelector('[data-menu-panel-container]');
    const gsap = window.gsap;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const initialMenuKey = primaryItems.find((item) => item.dataset.active === 'true')?.dataset.menuKey ?? 'home';
    let menuAnimation;

    if (!openButton || !closeButton || !overlay) {
        return;
    }

    if (gsap) {
        gsap.set(overlay, { autoAlpha: 0, yPercent: -100 });
    }

    const setActiveMenu = (menuKey, animate = true, showPanel = true) => {
        primaryItems.forEach((item) => {
            const isActive = item.dataset.menuKey === menuKey;

            item.dataset.active = String(isActive);

            if (item.matches('button')) {
                item.setAttribute('aria-expanded', String(isActive && showPanel));
            }
        });

        let activePanel = null;

        panels.forEach((panel) => {
            const isActive = showPanel && panel.dataset.menuPanel === menuKey;

            panel.dataset.active = String(isActive);
            panel.setAttribute('aria-hidden', String(!isActive));

            if (isActive) {
                activePanel = panel;
            }
        });

        if (panelContainer) {
            panelContainer.dataset.empty = String(!activePanel);
        }

        if (activePanel && animate && gsap && !reduceMotion) {
            gsap.fromTo(
                activePanel.children,
                { autoAlpha: 0, y: 12 },
                { autoAlpha: 1, y: 0, duration: 0.25, stagger: 0.03, ease: 'power2.out', overwrite: true }
            );
        }
    };

    const setMenuOpen = (isOpen) => {
        overlay.setAttribute('aria-hidden', String(!isOpen));
        openButton.setAttribute('aria-expanded', String(isOpen));
        document.body.classList.toggle('overflow-hidden', isOpen);
        menuAnimation?.kill();

        if (isOpen) {
            setActiveMenu(initialMenuKey, false, false);
            overlay.dataset.open = 'true';
            overlay.removeAttribute('inert');

            if (gsap && !reduceMotion) {
                menuAnimation = gsap.timeline();
                menuAnimation
                    .to(overlay, {
                        autoAlpha: 1,
                        yPercent: 0,
                        duration: 0.48,
                        ease: 'power3.out',
                    })
                    .fromTo(
                        primaryItems,
                        { autoAlpha: 0, y: 18 },
                        { autoAlpha: 1, y: 0, duration: 0.32, stagger: 0.035, ease: 'power2.out' },
                        0.08
                    );

                const activePanel = panels.find((panel) => panel.dataset.active === 'true');

                if (activePanel) {
                    menuAnimation.fromTo(
                        activePanel.children,
                        { autoAlpha: 0, y: 12 },
                        { autoAlpha: 1, y: 0, duration: 0.28, stagger: 0.035, ease: 'power2.out' },
                        0.12
                    );
                }
            } else if (gsap) {
                gsap.set(overlay, { autoAlpha: 1, yPercent: 0 });
            }

            closeButton.focus();
            return;
        }

        const finishClosing = () => {
            overlay.dataset.open = 'false';
            overlay.setAttribute('inert', '');
        };

        if (gsap && !reduceMotion) {
            menuAnimation = gsap.to(overlay, {
                autoAlpha: 0,
                yPercent: -100,
                duration: 0.34,
                ease: 'power2.out',
                onComplete: finishClosing,
            });
        } else {
            if (gsap) {
                gsap.set(overlay, { autoAlpha: 0, yPercent: -100 });
            }

            finishClosing();
        }

        openButton.focus();
    };

    openButton.addEventListener('click', () => setMenuOpen(true));
    closeButton.addEventListener('click', () => setMenuOpen(false));

    triggers.forEach((trigger) => {
        const activate = () => setActiveMenu(trigger.dataset.menuTrigger);

        trigger.addEventListener('click', activate);
    });

    overlay.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setMenuOpen(false);
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const focusableElements = Array.from(
            overlay.querySelectorAll('a[href], button:not([disabled])')
        ).filter((element) => !element.closest('[aria-hidden="true"]'));

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey && document.activeElement === firstElement) {
            event.preventDefault();
            lastElement?.focus();
        }

        if (!event.shiftKey && document.activeElement === lastElement) {
            event.preventDefault();
            firstElement?.focus();
        }
    });
})();
