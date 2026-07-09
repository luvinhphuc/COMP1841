(() => {
    const menuButton = document.querySelector('#menu-btn');
    const logo = document.querySelector('#site-logo');
    const overlay = document.querySelector('#mega-menu');
    const header = menuButton?.closest('header');
    const iconLines = {
        top: menuButton?.querySelector('[data-menu-icon-line="top"]'),
        middle: menuButton?.querySelector('[data-menu-icon-line="middle"]'),
        bottom: menuButton?.querySelector('[data-menu-icon-line="bottom"]'),
    };
    const primaryItems = Array.from(document.querySelectorAll('[data-menu-primary-item]'));
    const triggers = Array.from(document.querySelectorAll('[data-menu-trigger]'));
    const panels = Array.from(document.querySelectorAll('[data-menu-panel]'));
    const panelContainer = document.querySelector('[data-menu-panel-container]');
    const userMenu = document.querySelector('[data-user-menu]');
    const userMenuButton = document.querySelector('[data-user-menu-button]');
    const userMenuDropdown = document.querySelector('[data-user-menu-dropdown]');
    const gsap = window.gsap;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const initialMenuKey = primaryItems.find((item) => item.dataset.active === 'true')?.dataset.menuKey ?? 'dashboard';
    let menuAnimation;
    let isTransitioning = false;

    if (!menuButton || !logo || !overlay || !header) {
        return;
    }

    if (gsap) {
        gsap.set(overlay, { autoAlpha: 0, yPercent: -100 });
        gsap.set([iconLines.top, iconLines.middle, iconLines.bottom], {
            transformOrigin: '50% 50%',
            svgOrigin: '16.5 12.5',
        });
    }

    const setMenuIconOpen = (isOpen) => {
        if (!iconLines.top || !iconLines.middle || !iconLines.bottom) {
            return;
        }

        if (!gsap || reduceMotion) {
            iconLines.top.style.transform = isOpen ? 'translateY(7px) rotate(45deg)' : '';
            iconLines.middle.style.opacity = isOpen ? '0' : '';
            iconLines.bottom.style.transform = isOpen ? 'translateY(-7px) rotate(-45deg)' : '';
            return;
        }

        gsap.to(iconLines.top, {
            y: isOpen ? 7 : 0,
            rotation: isOpen ? 45 : 0,
            duration: 0.28,
            ease: 'power3.out',
            overwrite: true,
        });
        gsap.to(iconLines.middle, {
            autoAlpha: isOpen ? 0 : 1,
            scaleX: isOpen ? 0.35 : 1,
            duration: 0.2,
            ease: 'power2.out',
            overwrite: true,
        });
        gsap.to(iconLines.bottom, {
            y: isOpen ? -7 : 0,
            rotation: isOpen ? -45 : 0,
            duration: 0.28,
            ease: 'power3.out',
            overwrite: true,
        });
    };

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
        setMenuIconOpen(isOpen);
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

    const setUserMenuOpen = (isOpen) => {
        if (!userMenuButton || !userMenuDropdown) {
            return;
        }

        userMenuButton.setAttribute('aria-expanded', String(isOpen));
        userMenuDropdown.dataset.open = String(isOpen);
    };

    userMenuButton?.addEventListener('click', (event) => {
        event.stopPropagation();
        setUserMenuOpen(userMenuButton.getAttribute('aria-expanded') !== 'true');
    });

    document.addEventListener('click', (event) => {
        if (userMenu && !userMenu.contains(event.target)) {
            setUserMenuOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setUserMenuOpen(false);

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
