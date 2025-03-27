<?php
include 'conec.php'; // Incluindo a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    if (!empty($nome_completo) && !empty($cpf_cnpj) && !empty($email)) {
        $sql = "INSERT INTO clientes (nome_completo, cpf_cnpj, rg, fonefixo, email, fazenda, endereco_cobranca, rua_cobranca, cidade_cobranca, endereco_entrega, rua_entrega, cidade_entrega) 
                VALUES ('$nome_completo', '$cpf_cnpj', '$rg', '$fonefixo', '$email', '$fazenda', '$endereco_cobranca', '$rua_cobranca', '$cidade_cobranca', '$endereco_entrega', '$rua_entrega', '$cidade_entrega')";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Cliente cadastrado com sucesso!";
        } else {
            $error_message = "Erro ao cadastrar o cliente: " . mysqli_error($conn);
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
  <title>Cadastrar Cliente</title>
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
  </style>
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
</head>
<body>
  <div class="d-flex">
    <div id="sidebar" class="sidebar">
      <a href="dashboard.php">Listar clientes</a>
      <a href="novo-cliente.php">Cadastrar cliente</a>
      <a href="listar-produtos.php">Listar produtos</a>
      <a href="cadastrar-produto.php">Cadastrar produtos</a>
      <a href="nova-venda.php">Fazer pedido</a>
    </div>
    <div class="content flex-grow-1">
      <!-- Botão para alternar a sidebar -->
      <button class="btn btn-primary mb-3" onclick="toggleSidebar()">☰</button>
      <div class="container mt-4">
      <button class="btn btn-back" onclick="window.location.href='dashboard.php'">
                        <i class="bi bi-arrow-left-circle"></i> Voltar para os Clientes
                    </button>
        <h2 class="mb-4">Cadastrar Cliente</h2>
        
        <?php if (isset($success_message)): ?>
          <div class="alert alert-success"><?= $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
          <div class="alert alert-danger"><?= $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nome_completo" class="form-label">Nome Completo</label>
              <input type="text" class="form-control" id="nome_completo" name="nome_completo" required>
            </div>
            <div class="col-md-6">
              <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
              <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="rg" class="form-label">RG</label>
              <input type="text" class="form-control" id="rg" name="rg">
            </div>
            <div class="col-md-6">
              <label for="fonefixo" class="form-label">FONE FIXO</label>
              <input type="text" class="form-control" id="fonefixo" name="fonefixo">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="email" class="form-label">E-MAIL</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-md-6">
              <label for="fazenda" class="form-label">FAZENDA</label>
              <input type="text" class="form-control" id="fazenda" name="fazenda">
            </div>
          </div>
          <hr>
          <h4>ENDEREÇO DE COBRANÇA</h4>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="endereco_cobranca" class="form-label">ENDEREÇO</label>
              <input type="text" class="form-control" id="endereco_cobranca" name="endereco_cobranca">
            </div>
            <div class="col-md-6">
              <label for="rua_cobranca" class="form-label">RUA</label>
              <input type="text" class="form-control" id="rua_cobranca" name="rua_cobranca">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="cidade_cobranca" class="form-label">CIDADE</label>
              <input type="text" class="form-control" id="cidade_cobranca" name="cidade_cobranca">
            </div>
          </div>
          <hr>
          <h4>LOCAL DE ENTREGA</h4>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="endereco_entrega" class="form-label">ENDEREÇO</label>
              <input type="text" class="form-control" id="endereco_entrega" name="endereco_entrega">
            </div>
            <div class="col-md-6">
              <label for="rua_entrega" class="form-label">RUA</label>
              <input type="text" class="form-control" id="rua_entrega" name="rua_entrega">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="cidade_entrega" class="form-label">CIDADE</label>
              <input type="text" class="form-control" id="cidade_entrega" name="cidade_entrega">
            </div>
          </div>
          <button type="submit" class="btn btn-success">Salvar Cliente</button>
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
