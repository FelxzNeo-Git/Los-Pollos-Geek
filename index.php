<html>

<head>
    <title>Los Pollos Geek</title>
    <link rel="stylesheet" href="./css/login.css">
    <link rel="icon" type="image/png" href="./img/Los Pollos Geek_ logo.png">
</head>

<body>

    <img src="./img/Los Pollos Geek_ logo.png" alt="lospollosgeeklogo" class="imagelpg">

    <div class="loginbox">
        <h2>LOGIN</h2>
        <form action="/criardados" method="post">
            <div>
                <label for="email">Email:</label>
                <input type="text" id="email" name="nome" required>

                <div>
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="nome" required >
                </div>

                <a href="LPG.php"><button type="submit">Enviar</button></a>
            </div>
        </form>

        <h5>Não tem uma conta? <a href="cadastro.php">cadastre-se</a></h5>
    </div>



    <div class="rodape">
        <p>&copy; 2023 Los Pollos Geek. Todos os direitos reservados.</p>
    </div>
</body>

</html>