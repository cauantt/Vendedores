<?php 
include 'conec.php'; 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listar Clientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    .table tr {
      cursor: pointer;
    }
    .input-group-text, .form-control, .btn {
      text-transform: uppercase;
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
      <button class="btn btn-primary mb-3" onclick="toggleSidebar()">☰</button>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="novo-cliente.php"><button class="btn btn-success">Cadastrar Novo Cliente</button></a>
        <div class="input-group w-50">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" placeholder="Pesquisar usuário...">
        </div>
      </div>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nome</th>
            <th>CPF/CNPJ</th>
            <th>Email</th>
            <th>FONE FIXO</th>
            <th>AÇÕES</th>
          </tr>
        </thead>
        <tbody>
          <?php
            // Consulta SQL para pegar os clientes do banco de dados, incluindo o id
            $sql = "SELECT id, nome_completo, cpf_cnpj, email, fonefixo FROM clientes";
            $result = $conn->query($sql);

            // Verifica se existem resultados
            if ($result->num_rows > 0) {
                // Exibe cada linha de resultado
                while($row = $result->fetch_assoc()) {
                    echo "<tr onclick=\"redirectToOrders('".$row['id']."')\">";
                    echo "<td>" . $row['nome_completo'] . "</td>";
                    echo "<td>" . $row['cpf_cnpj'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['fonefixo'] . "</td>";
                    echo "<td><a href='editar-cliente.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm' onclick='event.stopPropagation();'>Editar</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Nenhum cliente encontrado.</td></tr>";
            }
            $conn->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      let sidebar = document.getElementById("sidebar");
      let content = document.querySelector(".content");
      sidebar.classList.toggle("hidden-sidebar");
      content.classList.toggle("collapsed");
    }
    function redirectToOrders(id) {
      // Redireciona para a página de pedidos passando o id do cliente como parâmetro
      window.location.href = `listar-pedidos.php?id=${encodeURIComponent(id)}`;
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
