<?php
session_start();
include 'conec.php';

// Verifica se o usuário logado tem o perfil de gerente ou admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['gerente', 'admin'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ID do usuário logado
    $usuario_id = $_SESSION['id'];
    $role_logado = $_SESSION['role'];

    // Dados do novo usuário
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Hash da senha
    $role_novo = ($role_logado === 'admin') ? $_POST['role'] : 'vendedor';

    if (!empty($nome) && !empty($cpf) && !empty($_POST['senha'])) {
        // Insere o novo usuário na tabela
        $sql = "INSERT INTO usuarios (nome, cpf, senha, role) VALUES ('$nome', '$cpf', '$senha', '$role_novo')";
        if ($conn->query($sql) === TRUE) {
            // Pega o ID do usuário recém-criado
            $novo_usuario_id = $conn->insert_id;
            
            // Se for gerente cadastrando um vendedor, associa na tabela vend_geren
            if ($role_logado === 'gerente' && $role_novo === 'vendedor') {
                $sql2 = "INSERT INTO vend_geren (vendedor_id, gerente_id) VALUES ('$novo_usuario_id', '$usuario_id')";
                if ($conn->query($sql2) === TRUE) {
                    $success_message = "Usuário cadastrado com sucesso!";
                } else {
                    $error_message = "Erro ao associar vendedor ao gerente: " . $conn->error;
                }
            } else {
                $success_message = "Usuário cadastrado com sucesso!";
            }
        } else {
            $error_message = "Erro ao cadastrar usuário: " . $conn->error;
        }
    } else {
        $error_message = "Por favor, preencha os campos obrigatórios!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seus Clientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php include 'menu.php'; ?>

<div class="d-flex">
  <div class="content flex-grow-1">
    <div class="container mt-4">
      <button class="btn btn-back" onclick="window.location.href='listar-usuarios.php'">
        <i class="bi bi-arrow-left-circle"></i> Voltar
      </button>
      <h2 class="mb-4">Cadastrar vendedor</h2>
      <!-- Mensagens de sucesso ou erro -->
      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?= $success_message; ?></div>
      <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message; ?></div>
      <?php endif; ?>
      
      <form method="POST" action="">
        <div class="mb-3">
          <label for="nome" class="form-label">Nome Completo</label>
          <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
          <label for="cpf" class="form-label">CPF</label>
          <input type="text" class="form-control" id="cpf" name="cpf" required>
        </div>
        <div class="mb-3">
          <label for="senha" class="form-label">Senha</label>
          <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="mb-3">
          <label for="role" class="form-label">Tipo de Usuário</label>
          <select class="form-control" id="role" name="role" required>
            <option value="vendedor">Vendedor</option>
            <option value="gerente">Gerente</option>
          </select>
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-success">Salvar Usuário</button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>