<?php 
include 'conec.php'; 

// Realiza a consulta ao banco para obter os produtos
$query = "SELECT * FROM produtos";
$result = mysqli_query($conn, $query);

// Verifica se há produtos no banco
if (!$result) {
    die("Erro na consulta ao banco de dados: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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

        /* Botões com cores normais */
        .btn-custom {
            background-color:rgba(0, 132, 255, 0.54);
            color: white;
        }

        .btn-custom:hover {
            background-color: #6fa3f8;
        }

        .btn-info {
            background-color: #007bff;
            color: white;
        }

        .btn-info:hover {
            background-color: #0056b3;
        }

        .btn-warning {
            background-color: #28a745;
            color: white;
        }

        .btn-warning:hover {
            background-color: #218838;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
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
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Produtos Cadastrados</h2>
                    <a href="cadastrar-produto.php" class="btn btn-custom">Cadastrar Produto</a>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                           
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Embalagem</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Preenche a tabela com os dados do banco
                        while ($produto = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                          
                            echo "<td>" . $produto['codigo'] . "</td>";
                            echo "<td>" . $produto['nome'] . "</td>";
                            echo "<td>" . $produto['embalagem'] . "</td>";
                            echo "<td>" . $produto['descricao'] . "</td>";
                            echo "<td>
                                    <button class='btn btn-info btn-sm'>Detalhes</button>
                                    <button class='btn btn-warning btn-sm'>Editar</button>
                                    <button class='btn btn-danger btn-sm'>Excluir</button>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
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
