// Pré-visualização da imagem + validação rápida dos formulários do painel
(function () {
    var input = document.getElementById('imagem');
    var img = document.getElementById('preview');
    var txt = document.getElementById('upload-texto');
    var padrao = txt ? txt.textContent : '';

    if (input) {
        input.addEventListener('change', function () {
            var arq = this.files[0];
            if (!arq) { img.style.display = 'none'; txt.textContent = padrao; return; }
            img.src = URL.createObjectURL(arq);
            img.style.display = 'block';
            txt.textContent = arq.name + ' (clique para trocar)';
        });
    }

    // Formulário de produto
    var form = document.getElementById('form');
    if (form) {
        form.addEventListener('submit', function (ev) {
            var msg = null;
            if (!document.getElementById('categoria').value) msg = 'Escolha uma categoria.';
            else if (document.getElementById('nome').value.trim().length < 3) msg = 'Informe o nome do produto.';
            else if (!document.getElementById('valor').value.trim()) msg = 'Informe o valor.';
            else if (!input.files[0]) msg = 'Escolha uma imagem para o produto.';
            else if (input.files[0].size > 5 * 1024 * 1024) msg = 'A imagem deve ter no máximo 5 MB.';
            if (msg) { ev.preventDefault(); alert(msg); }
        });
    }
})();
