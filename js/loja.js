// Menu da conta (botão do usuário)
function toggleNav() {
    document.querySelector('.menu-usuario').classList.toggle('open');
}

// Fecha o menu ao clicar fora dele
document.addEventListener('click', function (ev) {
    var menu = document.querySelector('.menu-usuario');
    if (menu && !menu.contains(ev.target)) menu.classList.remove('open');
});
