
<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // Redireciona para login, se não estiver logado
    header("Location: ../../public/login.php");
    exit;
}

header('Content-type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <title>Menu Principal</title>
    <link rel="stylesheet" href="../../public/assets/CSS/menu.css">
</head>

<body>
    <header>
        <img src="../../public/assets/image/logo.jpg" alt="Logo" />
        <div>
            <h1>Contratos</h1>
        </div>
    </header>

    <div class="container">
        <h2>Selecione uma opção</h2>
        <div class="buttons-grid">
            <a class="acomp-button" href="acomp.php">ACOMPANHAMENTO</a>
            <a class="conc-button" href="concluidos.php">CONCLUÍDOS</a>
            <a class="menu-button" href="novExtrav.php">EXTRAVIO </br> (Retidada do equipamento)</a>
            <a class="menu-button" href="subsMaq.php">SUBS ITEM </br> (Extravio/Substituição)</a>
            <a class="menu-button" href="subsRazSocial.php">SUBSTITUIR </br> RAZÃO </br> SOCIAL </a>
            <!--<a class="menu-button" href="saldocredforn/public/index.php">SALDO CRÉDITOS FORNECEDORES</a>
            <a class="menu-button" href="beneficios/public/index.php">SALDO BENEFÍCIOS</a> -->
            <!-- <a class="menu-button" href="">SALDO ESTOQUE</a> -->
            <a class="sair-button" href="../../public/login.php">SAIR</a>
        </div>
    </div>

    <footer>
        &copy; 2025 Databit. Todos os direitos reservados.
    </footer>
</body>

</html>
