<?php
session_start();
include 'conec.php';

// Verifica se o usuário logado tem o perfil de gerente (opcional, mas recomendado)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gerente') {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ID do gerente logado
    $gerente_id = $_SESSION['id'];

    // Dados do vendedor
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];

    if (!empty($nome) && !empty($cpf) && !empty($senha)) {
        // Insere o vendedor na tabela de usuários com role 'vendedor'
        $sql = "INSERT INTO usuarios (nome, cpf, senha, role) VALUES ('$nome', '$cpf', '$senha', 'vendedor')";
        if ($conn->query($sql) === TRUE) {
            // Pega o ID do vendedor recém-criado
            $vendedor_id = $conn->insert_id;
            // Associa o vendedor ao gerente na tabela vend_geren
            $sql2 = "INSERT INTO vend_geren (vendedor_id, gerente_id) VALUES ('$vendedor_id', '$gerente_id')";
            if ($conn->query($sql2) === TRUE) {
                $success_message = "Vendedor cadastrado com sucesso!";
            } else {
                $error_message = "Erro ao associar vendedor ao gerente: " . $conn->error;
            }
        } else {
            $error_message = "Erro ao cadastrar vendedor: " . $conn->error;
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
  <title>Cadastrar Vendedor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { text-transform: uppercase; }
    .sidebar {
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      background: #343a40;
      padding-top: 20px;
      transition: all 0.3s;
    }
    .sidebar a {
      color: white;
      display: block;
      padding: 10px;
      text-decoration: none;
    }
    .sidebar a:hover { background: #495057; }
    .content { margin-left: 250px; padding: 20px; transition: all 0.3s; }
    .collapsed { margin-left: 0; }
    .hidden-sidebar { width: 0; overflow: hidden; }
    .btn-back {
      background-color: #6c757d;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      text-transform: uppercase;
      margin-bottom: 15px;
    }
    .btn-back:hover { background-color: #5a6268; }
  </style>
</head>
<body>
  <div class="d-flex">
   <?php include'sidebar.php' ?>
    <div class="content flex-grow-1">
      <!-- Botão para alternar a sidebar -->
      <button class="btn btn-primary mb-3" onclick="toggleSidebar()">☰</button>
      <div class="container mt-4">
        <button class="btn btn-back" onclick="window.location.href='dashboard.php'">
          <i class="bi bi-arrow-left-circle"></i> Voltar
        </button>
        <h2 class="mb-4">Cadastrar Vendedor</h2>
        
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
          <button type="submit" class="btn btn-success">Salvar Vendedor</button>
        </form>
      </div>
    </div>
  </div>
  
  <script>
    function toggleSidebar() {
      let sidebar = document.getElementById("sidebar");
      let content = document.querySelector(".content");
      sidebar.classList.toggle("hidden-sidebar");
      content.classList.toggle("collapsed");
    }
  </script>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
