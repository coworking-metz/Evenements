document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[href="#set-nb"]').addEventListener('click', e => {
        e.preventDefault();

        document.body.classList.toggle('set-nb');
    });
})