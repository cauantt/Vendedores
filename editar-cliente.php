<?php
include 'conec.php';

// Obtém o ID do cliente via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    die("ID do cliente não especificado.");
}

// Se o formulário de exclusão for enviado, exclui o cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $sql = "DELETE FROM clientes WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php?msg=deleted");
        exit;
    } else {
        $error_message = "Erro ao excluir o cliente: " . mysqli_error($conn);
    }
}

// Se o formulário de atualização for enviado, atualiza os dados
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $nome_completo = $_POST['nome_completo'];
    $cpf_cnpj = $_POST['cpf_cnpj'];
    $rg = $_POST['rg'];
    $fonefixo = $_POST['fonefixo'];
    $email = $_POST['email'];
    $fazenda = $_POST['fazenda'];
    $endereco_cobranca = $_POST['endereco_cobranca'];
    $rua_cobranca = $_POST['rua_cobranca'];
    $cidade_cobranca = $_POST['cidade_cobranca'];
    $endereco_entrega = $_POST['endereco_entrega'];
    $rua_entrega = $_POST['rua_entrega'];
    $cidade_entrega = $_POST['cidade_entrega'];

    // Atualiza os dados do cliente na tabela
    $sql = "UPDATE clientes SET 
                nome_completo = '$nome_completo',
                cpf_cnpj = '$cpf_cnpj',
                rg = '$rg',
                fonefixo = '$fonefixo',
                email = '$email',
                fazenda = '$fazenda',
                endereco_cobranca = '$endereco_cobranca',
                rua_cobranca = '$rua_cobranca',
                cidade_cobranca = '$cidade_cobranca',
                endereco_entrega = '$endereco_entrega',
                rua_entrega = '$rua_entrega',
                cidade_entrega = '$cidade_entrega'
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Cliente atualizado com sucesso!";
    } else {
        $error_message = "Erro ao atualizar o cliente: " . mysqli_error($conn);
    }
}

// Consulta para buscar os dados do cliente
$sql = "SELECT * FROM clientes WHERE id = $id";
$result = mysqli_query($conn, $sql);
if ($result->num_rows > 0) {
    $cliente = mysqli_fetch_assoc($result);
} else {
    die("Cliente não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    /* Aplica o texto em CAPSLOCK em toda a página */
    body {
      text-transform: uppercase;
    }
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
    .sidebar a:hover {
      background: #495057;
    }
    .content {
      margin-left: 250px;
      padding: 20px;
      transition: all 0.3s;
    }
    .collapsed {
      margin-left: 0;
    }
    .hidden-sidebar {
      width: 0;
      overflow: hidden;
    }
    .btn-back {
      background-color: #6c757d;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      text-transform: uppercase;
      margin-bottom: 15px;
    }
    .btn-back:hover {
      background-color: #5a6268;
    }
  </style>

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
    .content {
      margin-left: 250px;
      padding: 20px;
      transition: all 0.3s;
    }
    .collapsed { margin-left: 0; }
    .hidden-sidebar { width: 0; overflow: hidden; }
  </style>
</head>
<body>
  <div class="d-flex">
  <?php include'sidebar.php' ?>
    <div class="content flex-grow-1">
      <button class="btn btn-primary mb-3" onclick="toggleSidebar()">☰</button>
      <div class="container mt-4">
        <button class="btn btn-back" onclick="window.location.href='dashboard.php'">
          <i class="bi bi-arrow-left-circle"></i> Voltar para os Clientes
        </button>
        <h2 class="mb-4">Editar Cliente</h2>
        
        <?php if (isset($success_message)): ?>
          <div class="alert alert-success"><?= $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
          <div class="alert alert-danger"><?= $error_message; ?></div>
        <?php endif; ?>

        <!-- Formulário de Atualização -->
        <form method="POST" action="">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nome_completo" class="form-label">Nome Completo</label>
              <input type="text" class="form-control" id="nome_completo" name="nome_completo" value="<?= $cliente['nome_completo'] ?>" required>
            </div>
            <div class="col-md-6">
              <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
              <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" value="<?= $cliente['cpf_cnpj'] ?>" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="rg" class="form-label">RG</label>
              <input type="text" class="form-control" id="rg" name="rg" value="<?= $cliente['rg'] ?>">
            </div>
            <div class="col-md-6">
              <label for="fonefixo" class="form-label">FONE FIXO</label>
              <input type="text" class="form-control" id="fonefixo" name="fonefixo" value="<?= $cliente['fonefixo'] ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="email" class="form-label">E-MAIL</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= $cliente['email'] ?>" required>
            </div>
            <div class="col-md-6">
              <label for="fazenda" class="form-label">FAZENDA</label>
              <input type="text" class="form-control" id="fazenda" name="fazenda" value="<?= $cliente['fazenda'] ?>">
            </div>
          </div>
          <hr>
          <h4>ENDEREÇO DE COBRANÇA</h4>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="endereco_cobranca" class="form-label">ENDEREÇO</label>
              <input type="text" class="form-control" id="endereco_cobranca" name="endereco_cobranca" value="<?= $cliente['endereco_cobranca'] ?>">
            </div>
            <div class="col-md-6">
              <label for="rua_cobranca" class="form-label">RUA</label>
              <input type="text" class="form-control" id="rua_cobranca" name="rua_cobranca" value="<?= $cliente['rua_cobranca'] ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="cidade_cobranca" class="form-label">CIDADE</label>
              <input type="text" class="form-control" id="cidade_cobranca" name="cidade_cobranca" value="<?= $cliente['cidade_cobranca'] ?>">
            </div>
          </div>
          <hr>
          <h4>LOCAL DE ENTREGA</h4>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="endereco_entrega" class="form-label">ENDEREÇO</label>
              <input type="text" class="form-control" id="endereco_entrega" name="endereco_entrega" value="<?= $cliente['endereco_entrega'] ?>">
            </div>
            <div class="col-md-6">
              <label for="rua_entrega" class="form-label">RUA</label>
              <input type="text" class="form-control" id="rua_entrega" name="rua_entrega" value="<?= $cliente['rua_entrega'] ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="cidade_entrega" class="form-label">CIDADE</label>
              <input type="text" class="form-control" id="cidade_entrega" name="cidade_entrega" value="<?= $cliente['cidade_entrega'] ?>">
            </div>
          </div>
          <button type="submit" class="btn btn-success">Salvar Alterações</button>
          <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');" class="mt-3">
          <button type="submit" name="delete" class="btn btn-danger">
            <i class="bi bi-trash"></i> Excluir Cliente
          </button>
        </form>
        </form>

        <!-- Botão de Exclusão -->
        
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
