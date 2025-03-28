<?php
session_start();
include 'conec.php';

// Verifica se o usuário está logado e se possui role admin ou gerente
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'gerente'])) {
    header('Location: dashboard.php');
    exit();
}

$role = $_SESSION['role'];
$usuario_id = $_SESSION['id'];

if ($role === 'admin') {
    // Admin: Seleciona todos os vendedores e gerentes (neste caso, listaremos apenas os vendedores para edição)
    $sql = "SELECT id, nome, cpf, role FROM usuarios WHERE role = 'vendedor' ORDER BY nome";
} else {
    // Gerente: Seleciona apenas os vendedores associados a este gerente
    $sql = "SELECT u.id, u.nome, u.cpf, u.role 
            FROM usuarios u 
            INNER JOIN vend_geren vg ON u.id = vg.vendedor_id 
            WHERE vg.gerente_id = '$usuario_id' 
            ORDER BY u.nome";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listar Vendedores</title>
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
    
    .btn-add {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      text-transform: uppercase;
      margin-bottom: 15px;
    }
    .btn-add:hover { background-color: #218838; }
  </style>
</head>
<body>
<?php include 'menu.php'; ?>
  <div class="d-flex">

    <div class="content flex-grow-1">
      <div class="container mt-4">
        <button class="btn btn-back" onclick="window.location.href='dashboard.php'">
          <i class="bi bi-arrow-left-circle"></i> Voltar
        </button>
        <button class="btn btn-add" onclick="window.location.href='novo-vendedor.php'">
          <i class="bi bi-plus-circle"></i> Cadastrar Vendedor
        </button>
        
        <h2 class="mb-4">Listar Vendedores</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>NOME</th>
                <th>CPF</th>
                <th>ROLE</th>
                <th>AÇÕES</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id']; ?></td>
                  <td><?= $row['nome']; ?></td>
                  <td><?= $row['cpf']; ?></td>
                  <td><?= $row['role']; ?></td>
                  <td>
                    <a href="editar-vendedor.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil-square"></i> Editar
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-info">Nenhum vendedor encontrado.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>