<html>

<head>
    <title>Los Pollos Geek</title>
    <link rel="stylesheet" href="./css/cadastro.css">
    <link rel="icon" type="image/png" href="./img/Los Pollos Geek_ logo.png">
</head>

<body>

    <img src="./img/Los Pollos Geek_ logo.png" alt="lospollosgeeklogo" class="imagelpg">

    <div class="loginbox">
        <h2>CADASTRO</h2>
        <form action="/enviardados" method="post">
            <div class="textform">

                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="textform">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="textform">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div class="textform">
                <label for="idade">Idade:</label>
                <input type="number" id="idade" name="idade">
            </div>


            <button type="submit">Enviar</button>

            <h5>Já tem uma conta? <a href="index.php">login</a></h5>
    </div>
    </form>


    <div class="rodape">
        <p>&copy; 2023 Los Pollos Geek. Todos os direitos reservados.</p>
    </div>
</body>

</html>