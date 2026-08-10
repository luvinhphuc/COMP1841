/** Runs the landing hero sequence and provides a static reduced-motion presentation. */
(() => {
    const hero = document.querySelector('[data-landing-hero]');
    const video = document.querySelector('[data-landing-video]');
    const overlays = Array.from(document.querySelectorAll('[data-landing-overlay]'));
    const content = document.querySelector('[data-landing-content]');
    const gsap = window.gsap;

    if (!hero || !content || !gsap) {
        return;
    }

    const media = gsap.matchMedia();

    media.add(
        {
            isDesktop: '(min-width: 768px)',
            reduceMotion: '(prefers-reduced-motion: reduce)',
        },
        (context) => {
            const { isDesktop, reduceMotion } = context.conditions;
            const contentItems = Array.from(content.children);

            if (reduceMotion) {
                video?.pause();
                video?.removeAttribute('autoplay');
                gsap.set([...contentItems, ...overlays, ...(video ? [video] : [])], {
                    clearProps: 'transform,opacity,visibility,willChange',
                });
                return undefined;
            }

            if (video) {
                video.setAttribute('autoplay', '');
                video.play().catch(() => undefined);
            }

            gsap.set(contentItems, {
                autoAlpha: 0,
                y: isDesktop ? 24 : 17,
                willChange: 'transform, opacity',
            });
            gsap.set(overlays, {
                autoAlpha: 0.5,
                willChange: 'opacity',
            });

            if (video) {
                gsap.set(video, {
                    scale: isDesktop ? 1.045 : 1.025,
                    transformOrigin: '50% 50%',
                    willChange: 'transform',
                });
            }

            const timeline = gsap.timeline({
                defaults: { ease: 'power3.out' },
            });

            timeline.addLabel('heroStart', 0);

            if (video) {
                timeline.to(video, {
                    scale: 1,
                    duration: 1.6,
                    ease: 'power2.out',
                    clearProps: 'transform,willChange',
                }, 'heroStart');
            }

            timeline
                .to(overlays, {
                    autoAlpha: 1,
                    duration: 0.8,
                    clearProps: 'opacity,visibility,willChange',
                }, 'heroStart')
                .to(contentItems, {
                    autoAlpha: 1,
                    y: 0,
                    duration: isDesktop ? 0.58 : 0.5,
                    stagger: 0.075,
                    clearProps: 'transform,opacity,visibility,willChange',
                }, 'heroStart+=0.12');

            return () => timeline.kill();
        },
        hero
    );

    window.addEventListener('pagehide', () => media.revert(), { once: true });
})();
