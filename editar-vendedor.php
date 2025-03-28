<?php
session_start();
include 'conec.php';

// Verifica se o usuário possui permissão para editar (admin ou gerente)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'gerente'])) {
    header('Location: dashboard.php');
    exit();
}

// Obtém o ID do vendedor via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    die("ID do vendedor não especificado.");
}

// Se o formulário de inativação for enviado, atualiza o status para 'inativo'
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $sql = "UPDATE usuarios SET status = 'inativo' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php?msg=updated");
        exit;
    } else {
        $error_message = "Erro ao inativar o vendedor: " . mysqli_error($conn);
    }
}

// Se o formulário de atualização for enviado, atualiza os dados do vendedor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];

    // Atualiza os dados na tabela usuarios
    $sql = "UPDATE usuarios SET 
                nome = '$nome',
                cpf = '$cpf',
                senha = '$senha'
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Vendedor atualizado com sucesso!";
    } else {
        $error_message = "Erro ao atualizar o vendedor: " . mysqli_error($conn);
    }
}

// Consulta para buscar os dados do vendedor
$sql = "SELECT * FROM usuarios WHERE id = $id AND role = 'vendedor'";
$result = mysqli_query($conn, $sql);
if ($result->num_rows > 0) {
    $vendedor = mysqli_fetch_assoc($result);
} else {
    die("Vendedor não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Vendedor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { text-transform: uppercase; }
    
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
<?php include 'menu.php'; ?>
  <div class="d-flex">
  
    <div class="content flex-grow-1">
     
      <div class="container mt-4">
        <button class="btn btn-back" onclick="window.location.href='listar-usuarios.php'">
          <i class="bi bi-arrow-left-circle"></i> Voltar para Vendedores
        </button>
        <h2 class="mb-4">Editar Vendedor</h2>
        
        <?php if (isset($success_message)): ?>
          <div class="alert alert-success"><?= $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
          <div class="alert alert-danger"><?= $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label for="nome" class="form-label">NOME COMPLETO</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?= $vendedor['nome'] ?>" required>
          </div>
          <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" class="form-control" id="cpf" name="cpf" value="<?= $vendedor['cpf'] ?>" required>
          </div>
          <div class="mb-3">
            <label for="senha" class="form-label">SENHA</label>
            <input type="password" class="form-control" id="senha" name="senha" value="<?= $vendedor['senha'] ?>" required>
          </div>
          <button type="submit" class="btn btn-success">Salvar Alterações</button>
        </form>

        <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja inativar este vendedor?');" class="mt-3">
          <button type="submit" name="delete" class="btn btn-danger">
            <i class="bi bi-trash"></i> Inativar Vendedor
          </button>
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
