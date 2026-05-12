document.addEventListener('DOMContentLoaded', function() {
    const cssmenu = document.querySelector('#cssmenu');
    if (!cssmenu) return;

    const menuButton = document.createElement('div');
    menuButton.id = 'menu-button';
    menuButton.textContent = 'Menu';
    cssmenu.insertBefore(menuButton, cssmenu.firstChild);

    menuButton.addEventListener('click', function() {
        const menu = this.nextElementSibling;
        if (menu.classList.contains('open')) {
            menu.classList.remove('open');
        } else {
            menu.classList.add('open');
        }
    });
});