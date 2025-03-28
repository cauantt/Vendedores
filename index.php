<?php
session_start();
include('conec.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cpf = $_POST['cpf'];
    $senha = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE cpf = '$cpf' AND senha = '$senha'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $_SESSION['usuario'] = $cpf;
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['role'] = $usuario['role']; // Salvando a role do usuário

        header('Location: dashboard.php');
        exit();
    } else {
        $erro = "CPF ou senha inválidos!";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-sm" style="width: 300px;">
        <h4 class="text-center mb-3">Login</h4>

        <?php if (isset($erro)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $erro; ?>
            </div>
        <?php } ?>

        <!-- Formulário de login -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf" placeholder="Digite seu CPF" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>
</body>
</html>
