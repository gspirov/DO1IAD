document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('nav-toggle');
    const nav = document.getElementById('nav-main');

    if (toggleButton && nav) {
        toggleButton.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('open');
            toggleButton.setAttribute('aria-expanded', String(isOpen));
        });
    }
})
