/** Provides reusable, accessible open/close behaviour for data-attribute dropdowns. */
const dropdownSelector = '[data-dropdown]';
const triggerSelector = '[data-dropdown-trigger]';
const panelSelector = '[data-dropdown-panel]';

const dropdownParts = (dropdown) => ({
    trigger: dropdown.querySelector(triggerSelector),
    panel: dropdown.querySelector(panelSelector),
});

const isOpen = (dropdown) => {
    const { trigger } = dropdownParts(dropdown);
    return trigger?.getAttribute('aria-expanded') === 'true';
};

const positionFloatingPanel = (trigger, panel) => {
    if (!panel.hasAttribute('data-dropdown-floating')) {
        return;
    }

    const viewportGap = 12;
    const panelGap = 8;
    const triggerRect = trigger.getBoundingClientRect();
    const panelWidth = panel.offsetWidth;
    const panelHeight = panel.offsetHeight;
    const left = Math.min(
        Math.max(viewportGap, triggerRect.right - panelWidth),
        window.innerWidth - panelWidth - viewportGap,
    );
    const hasRoomBelow = triggerRect.bottom + panelGap + panelHeight <= window.innerHeight - viewportGap;
    const top = hasRoomBelow
        ? triggerRect.bottom + panelGap
        : Math.max(viewportGap, triggerRect.top - panelHeight - panelGap);

    panel.style.left = `${left}px`;
    panel.style.top = `${top}px`;
};

const setOpen = (dropdown, shouldOpen, restoreFocus = false) => {
    const { trigger, panel } = dropdownParts(dropdown);

    if (!(trigger instanceof HTMLElement) || !(panel instanceof HTMLElement)) {
        return;
    }

    trigger.setAttribute('aria-expanded', String(shouldOpen));
    panel.dataset.open = String(shouldOpen);

    if (shouldOpen) {
        positionFloatingPanel(trigger, panel);
    }

    if (!shouldOpen && restoreFocus) {
        trigger.focus({ preventScroll: true });
    }
};

export const closeAllDropdowns = ({ except = null, restoreFocus = false } = {}) => {
    document.querySelectorAll(dropdownSelector).forEach((dropdown) => {
        if (dropdown !== except && isOpen(dropdown)) {
            setOpen(dropdown, false, restoreFocus);
        }
    });
};

document.addEventListener('click', (event) => {
    const eventTarget = event.target;

    if (!(eventTarget instanceof Element)) {
        return;
    }

    const trigger = eventTarget.closest(triggerSelector);
    const triggerDropdown = trigger?.closest(dropdownSelector);

    if (trigger instanceof HTMLElement && triggerDropdown instanceof HTMLElement) {
        const shouldOpen = trigger.getAttribute('aria-expanded') !== 'true';

        closeAllDropdowns({ except: triggerDropdown });
        setOpen(triggerDropdown, shouldOpen);
        return;
    }

    const selectedDropdown = eventTarget.closest(dropdownSelector);

    if (selectedDropdown instanceof HTMLElement) {
        const panel = eventTarget.closest(panelSelector);
        const selectedAction = eventTarget.closest('a, button');

        if (
            panel instanceof HTMLElement
            && selectedAction
            && selectedDropdown.dataset.dropdownCloseOnSelect === 'true'
        ) {
            setOpen(selectedDropdown, false);
        }

        return;
    }

    closeAllDropdowns();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeAllDropdowns({ restoreFocus: true });
    }
});

window.addEventListener('resize', () => closeAllDropdowns());
window.addEventListener('scroll', () => closeAllDropdowns(), true);
