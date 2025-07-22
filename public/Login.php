<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>DATABIT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/CSS/login.css">
</head>

<body>

  <form class="login-box" method="POST" action="../app/controllers/validarUser.php">
    <div class="logo">
      <img src="assets\image\logo.jpg" alt="logo">
    </div>

    <?php if (isset($_GET['erro'])): ?>
      <div class="error"><?= htmlspecialchars($_GET['erro']) ?></div>
    <?php endif; ?>

    <input type="text" name="usuario" placeholder="Usuário" required>
    <input type="password" name="senha" placeholder="Senha" required>
    <button type="submit">Entrar</button>
  </form>

</body>

</html>