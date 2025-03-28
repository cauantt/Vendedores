<?php 
session_start();
include 'conec.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['id'];

// Define o cabeçalho conforme a role
if ($role == 'vendedor') {
    $header = "Meus Clientes";
} else if ($role == 'gerente') {
    $header = "Clientes da Minha Equipe";
} else if ($role == 'admin') {
    $header = "Todos os Clientes";
} else {
    $header = "Clientes";
}

// Define a consulta SQL conforme a role
if ($role == 'vendedor') {
    $sql = "SELECT id, nome_completo, cpf_cnpj, email, fonefixo FROM clientes WHERE vendedor = '$user_id'";
} else if ($role == 'gerente') {
    $sql = "SELECT id, nome_completo, cpf_cnpj, email, fonefixo 
            FROM clientes 
            WHERE vendedor IN (SELECT vendedor_id FROM vend_geren WHERE gerente_id = '$user_id')";
} else if ($role == 'admin') {
    $sql = "SELECT id, nome_completo, cpf_cnpj, email, fonefixo FROM clientes";
} else {
    $sql = "SELECT id, nome_completo, cpf_cnpj, email, fonefixo FROM clientes";
}

$result = $conn->query($sql);
if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listar Clientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      text-transform: uppercase;
    }
    .card {
      cursor: pointer;
      transition: transform 0.2s ease-in-out;
    }
    .card:hover {
      transform: scale(1.03);
    }
    .ellipsis {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 180px;
      display: inline-block;
    }
  </style>
</head>
<body>
<?php include 'menu.php'; ?>
<div class="container mt-4">
  <h2 class="mb-4"><?= $header; ?></h2>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="novo-cliente.php" class="btn btn-success">Cadastrar Novo Cliente</a>
    <div class="input-group w-50">
      <span class="input-group-text"><i class="bi bi-search"></i></span>
      <input type="text" class="form-control" placeholder="Pesquisar usuário...">
    </div>
  </div>
  <div class="row">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card shadow-sm" onclick="redirectToOrders('<?= $row['id'] ?>')">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['nome_completo']) ?></h5>
              <p class="card-text"><strong>CPF/CNPJ:</strong> <span class="ellipsis"><?= htmlspecialchars($row['cpf_cnpj']) ?></span></p>
              <p class="card-text"><strong>Email:</strong> <span class="ellipsis"><?= htmlspecialchars($row['email']) ?></span></p>
              <p class="card-text"><strong>Fone:</strong> <?= htmlspecialchars($row['fonefixo']) ?></p>
              <a href="editar-cliente.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm" onclick="event.stopPropagation();">Editar</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center">Nenhum cliente encontrado.</p>
    <?php endif; ?>
    <?php $conn->close(); ?>
  </div>
</div>
<script>
  function redirectToOrders(id) {
    window.location.href = `listar-pedidos.php?id=${encodeURIComponent(id)}`;
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
