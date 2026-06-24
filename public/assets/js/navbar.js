(() => {
    const menuButton = document.querySelector('#menu-btn');
    const logo = document.querySelector('#site-logo');
    const overlay = document.querySelector('#mega-menu');
    const header = menuButton?.closest('header');
    const openIcon = menuButton?.querySelector('[data-menu-icon-open]');
    const closeIcon = menuButton?.querySelector('[data-menu-icon-close]');
    const primaryItems = Array.from(document.querySelectorAll('[data-menu-primary-item]'));
    const triggers = Array.from(document.querySelectorAll('[data-menu-trigger]'));
    const panels = Array.from(document.querySelectorAll('[data-menu-panel]'));
    const panelContainer = document.querySelector('[data-menu-panel-container]');
    const gsap = window.gsap;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const initialMenuKey = primaryItems.find((item) => item.dataset.active === 'true')?.dataset.menuKey ?? 'home';
    let menuAnimation;
    let isTransitioning = false;

    if (!menuButton || !logo || !overlay || !header) {
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
        if (isTransitioning) {
            return;
        }

        isTransitioning = true;
        menuButton.setAttribute('aria-disabled', 'true');

        const finishTransition = () => {
            isTransitioning = false;
            menuButton.removeAttribute('aria-disabled');
        };

        overlay.setAttribute('aria-hidden', String(!isOpen));
        menuButton.setAttribute('aria-expanded', String(isOpen));
        menuButton.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
        if (isOpen) {
            header.dataset.menuOpen = 'true';
        }
        openIcon?.classList.toggle('hidden', isOpen);
        closeIcon?.classList.toggle('hidden', !isOpen);
        document.body.classList.toggle('overflow-hidden', isOpen);
        menuAnimation?.kill();

        if (isOpen) {
            setActiveMenu(initialMenuKey, false, false);
            overlay.dataset.open = 'true';
            overlay.removeAttribute('inert');

            if (gsap && !reduceMotion) {
                const logoBounds = logo.getBoundingClientRect();
                const edgePadding = window.innerWidth >= 1024 ? 128 : window.innerWidth >= 640 ? 32 : 20;
                const logoTargetX = window.innerWidth - logoBounds.right - edgePadding;
                menuAnimation = gsap.timeline({ onComplete: finishTransition });
                menuAnimation
                    .to(logo, {
                        x: logoTargetX,
                        duration: 0.32,
                        ease: 'power3.out',
                    }, 0)
                    .to(overlay, {
                        autoAlpha: 1,
                        yPercent: 0,
                        duration: 0.36,
                        ease: 'power3.out',
                    }, 0)
                    .fromTo(
                        primaryItems,
                        { autoAlpha: 0, y: 14 },
                        { autoAlpha: 1, y: 0, duration: 0.22, stagger: 0.02, ease: 'power2.out' },
                        0.05
                    );

                const activePanel = panels.find((panel) => panel.dataset.active === 'true');

                if (activePanel) {
                    menuAnimation.fromTo(
                        activePanel.children,
                        { autoAlpha: 0, y: 12 },
                        { autoAlpha: 1, y: 0, duration: 0.18, stagger: 0.035, ease: 'power2.out' },
                        0.12
                    );
                }
            } else if (gsap) {
                gsap.set(overlay, { autoAlpha: 1, yPercent: 0 });
                finishTransition();
            } else {
                finishTransition();
            }

            menuButton.focus();
            return;
        }

        const finishClosing = () => {
            overlay.dataset.open = 'false';
            overlay.setAttribute('inert', '');
            header.dataset.menuOpen = 'false';
        };

        if (gsap && !reduceMotion) {
            menuAnimation = gsap.timeline({
                onComplete: () => {
                    finishClosing();
                    finishTransition();
                },
            })
                .to(overlay, { autoAlpha: 0, yPercent: -100, duration: 0.34, ease: 'power2.inOut' }, 0)
                .to(logo, { x: 0, duration: 0.34, ease: 'power2.inOut' }, 0);
        } else {
            if (gsap) {
                gsap.set(overlay, { autoAlpha: 0, yPercent: -100 });
                gsap.set(logo, { x: 0 });
            }

            finishClosing();
            finishTransition();
        }

        menuButton.focus();
    };

    menuButton.addEventListener('click', () => {
        setMenuOpen(menuButton.getAttribute('aria-expanded') !== 'true');
    });

    triggers.forEach((trigger) => {
        const activate = () => setActiveMenu(trigger.dataset.menuTrigger);

        trigger.addEventListener('click', activate);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (menuButton.getAttribute('aria-expanded') === 'true') {
                setMenuOpen(false);
            }
            return;
        }

        if (menuButton.getAttribute('aria-expanded') !== 'true') {
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const focusableElements = [menuButton, ...Array.from(
            overlay.querySelectorAll('a[href], button:not([disabled])')
        ).filter((element) => !element.closest('[aria-hidden="true"]'))];

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
