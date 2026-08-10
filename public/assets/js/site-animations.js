/** Coordinates opt-in GSAP motion while respecting responsive and reduced-motion preferences. */
(() => {
    const root = document.querySelector('#main-content');
    const gsap = window.gsap;

    if (!root || !gsap) {
        return;
    }

    const motionElements = () =>
        Array.from(
            root.querySelectorAll(
                '[data-motion-intro], [data-motion-reveal], [data-motion-item], [data-motion-lift]'
            )
        );

    const resetMotionStyles = (targets) => {
        if (targets.length === 0) {
            return;
        }

        gsap.set(targets, {
            clearProps: 'transform,opacity,visibility,willChange',
        });
    };

    const media = gsap.matchMedia();

    media.add(
        {
            isDesktop: '(min-width: 768px)',
            reduceMotion: '(prefers-reduced-motion: reduce)',
            canHover: '(hover: hover) and (pointer: fine)',
        },
        (context) => {
            const {isDesktop, reduceMotion, canHover} = context.conditions;

            const allMotionElements = motionElements();

            const introGroups = Array.from(
                root.querySelectorAll('[data-motion-intro]')
            );

            const revealElements = Array.from(
                root.querySelectorAll('[data-motion-reveal]')
            );

            const listGroups = Array.from(
                root.querySelectorAll('[data-motion-list]')
            );

            const typingText = root.querySelector('#typing-text');

            const hoverCleanups = [];
            const entranceTweens = [];
            const observedAnimations = new Map();

            const distanceScale = isDesktop ? 1 : 0.72;

            const completeTargets = (targets) => {
                gsap.set(targets, {
                    clearProps: 'transform,opacity,visibility,willChange',
                });
            };

            const playEntrance = (targets, options = {}) => {
                const validTargets = targets.filter(
                    (target) => target instanceof HTMLElement
                );

                if (validTargets.length === 0) {
                    return null;
                }

                const tween = gsap.to(validTargets, {
                    autoAlpha: 1,
                    y: 0,
                    duration: options.duration ?? 0.5,
                    stagger: options.stagger ?? 0,
                    ease: options.ease ?? 'power2.out',
                    overwrite: 'auto',
                    onComplete: () => completeTargets(validTargets),
                });

                entranceTweens.push(tween);

                return tween;
            };

            /*
            |--------------------------------------------------------------------------
            | Reduced motion
            |--------------------------------------------------------------------------
            */

            if (reduceMotion) {
                resetMotionStyles(allMotionElements);

                if (typingText) {
                    gsap.set(typingText, {
                        clearProps: 'all',
                    });
                }

                return undefined;
            }

            /*
            |--------------------------------------------------------------------------
            | Typing animation
            |--------------------------------------------------------------------------
            */

            let typingTimeline = null;

            if (typingText) {
                const originalText = typingText.textContent.trim();

                typingText.textContent = '';

                const chars = [...originalText].map((char) => {
                    const span = document.createElement('span');

                    span.textContent =
                        char === ' '
                            ? '\u00A0'
                            : char;

                    span.style.display = 'inline-block';

                    typingText.appendChild(span);

                    return span;
                });

                gsap.set(chars, {
                    autoAlpha: 0,
                });

                gsap.set(typingText, {
                    autoAlpha: 1,
                });

                // Build one reversible sequence so typing and deletion share the same character nodes.
                typingTimeline = gsap.timeline();

                // Reveal characters in reading order.
                typingTimeline.to(chars, {
                    autoAlpha: 1,
                    duration: 0,
                    stagger: 0.08,
                });

                // Hold the complete phrase long enough to be read.
                typingTimeline.to({}, {
                    duration: 1.5,
                });

                // Remove characters in reverse to mimic backspacing.
                typingTimeline.to([...chars].reverse(), {
                    autoAlpha: 0,
                    duration: 0,
                    stagger: 0.04,
                });

                // Separate deletion from the final replay.
                typingTimeline.to({}, {
                    duration: 0.4,
                });

                typingTimeline.to(chars, {
                    autoAlpha: 1,
                    duration: 0,
                    stagger: 0.08,
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Intro animation
            |--------------------------------------------------------------------------
            */

            introGroups.forEach((group) => {
                const targets = Array.from(group.children).filter((child) => {
                    return (
                        child instanceof HTMLElement &&
                        !child.hidden &&
                        !child.matches(
                            '.sr-only, dialog, [aria-hidden="true"]'
                        )
                    );
                });

                if (targets.length === 0) {
                    return;
                }

                gsap.set(targets, {
                    autoAlpha: 0,
                    y: 18 * distanceScale,
                    willChange: 'transform, opacity',
                });

                playEntrance(targets, {
                    duration: isDesktop ? 0.52 : 0.44,
                    stagger: 0.055,
                    ease: 'power3.out',
                });
            });

            /*
            |--------------------------------------------------------------------------
            | Reveal animation
            |--------------------------------------------------------------------------
            */

            revealElements.forEach((element) => {
                gsap.set(element, {
                    autoAlpha: 0,
                    y: 22 * distanceScale,
                    willChange: 'transform, opacity',
                });

                observedAnimations.set(element, {
                    targets: [element],
                    duration: isDesktop ? 0.5 : 0.42,
                    stagger: 0,
                });
            });

            /*
            |--------------------------------------------------------------------------
            | List animation
            |--------------------------------------------------------------------------
            */

            listGroups.forEach((group) => {
                const items = Array.from(group.children).filter((child) => {
                    return (
                        child instanceof HTMLElement &&
                        child.hasAttribute('data-motion-item')
                    );
                });

                if (items.length === 0) {
                    return;
                }

                gsap.set(items, {
                    autoAlpha: 0,
                    y: 16 * distanceScale,
                    willChange: 'transform, opacity',
                });

                observedAnimations.set(group, {
                    targets: items,
                    duration: isDesktop ? 0.46 : 0.4,
                    stagger: 0.055,
                });
            });

            /*
            |--------------------------------------------------------------------------
            | Intersection Observer
            |--------------------------------------------------------------------------
            */

            let observer = null;

            if ('IntersectionObserver' in window) {
                observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (!entry.isIntersecting) {
                                return;
                            }

                            const animation =
                                observedAnimations.get(entry.target);

                            if (animation) {
                                playEntrance(
                                    animation.targets,
                                    animation
                                );
                            }

                            observer.unobserve(entry.target);

                            observedAnimations.delete(entry.target);
                        });
                    },
                    {
                        threshold: 0.14,
                        rootMargin: '0px 0px -8% 0px',
                    }
                );

                observedAnimations.forEach(
                    (animation, trigger) => {
                        observer.observe(trigger);
                    }
                );
            } else {
                observedAnimations.forEach((animation) => {
                    playEntrance(
                        animation.targets,
                        animation
                    );
                });

                observedAnimations.clear();
            }

            /*
            |--------------------------------------------------------------------------
            | Hover lift
            |--------------------------------------------------------------------------
            */

            if (canHover) {
                root
                    .querySelectorAll('[data-motion-lift]')
                    .forEach((element) => {
                        if (!(element instanceof HTMLElement)) {
                            return;
                        }

                        const liftTo = gsap.quickTo(
                            element,
                            'y',
                            {
                                duration: 0.2,
                                ease: 'power2.out',
                                overwrite: 'auto',
                            }
                        );

                        const lift = () => {
                            liftTo(-3);
                        };

                        const settle = () => {
                            liftTo(0);
                        };

                        const handleFocusOut = (event) => {
                            if (
                                !element.contains(
                                    event.relatedTarget
                                )
                            ) {
                                settle();
                            }
                        };

                        element.addEventListener(
                            'pointerenter',
                            lift
                        );

                        element.addEventListener(
                            'pointerleave',
                            settle
                        );

                        element.addEventListener(
                            'focusin',
                            lift
                        );

                        element.addEventListener(
                            'focusout',
                            handleFocusOut
                        );

                        hoverCleanups.push(() => {
                            element.removeEventListener(
                                'pointerenter',
                                lift
                            );

                            element.removeEventListener(
                                'pointerleave',
                                settle
                            );

                            element.removeEventListener(
                                'focusin',
                                lift
                            );

                            element.removeEventListener(
                                'focusout',
                                handleFocusOut
                            );
                        });
                    });
            }

            /*
            |--------------------------------------------------------------------------
            | Cleanup
            |--------------------------------------------------------------------------
            */

            return () => {
                observer?.disconnect();

                typingTimeline?.kill();

                entranceTweens.forEach((tween) => {
                    tween.kill();
                });

                hoverCleanups.forEach((cleanup) => {
                    cleanup();
                });

                resetMotionStyles(allMotionElements);
            };
        },
        root
    );

    window.addEventListener(
        'pagehide',
        () => {
            media.revert();
        },
        {
            once: true,
        }
    );
})();
