<?php 
include 'conec.php'; 

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $codigo = $_POST['codigo'];
    $nome = $_POST['nome'];
    $embalagem = $_POST['embalagem'];
    $descricao = $_POST['descricao'];

    // Prepara a consulta para inserir os dados na tabela 'produtos'
    $query = "INSERT INTO produtos (codigo, nome, embalagem, descricao) 
              VALUES ('$codigo', '$nome', '$embalagem', '$descricao')";

    // Executa a consulta
    if (mysqli_query($conn, $query)) {
        // Sucesso, redireciona para a página de listagem de produtos
        header("Location: listar-produtos.php");
        exit();
    } else {
        // Erro ao inserir os dados
        echo "Erro: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
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
            background-color: #a2d2ff;
            color: white;
            border: none;
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
                    <h2>Cadastrar Produto</h2>
                    <a href="produtos.html" class="btn btn-back">Voltar</a>
                </div>
                <form method="POST" action="cadastrar-produto.php">
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Informe o código do produto" required>
                    </div>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Informe o nome do produto" required>
                    </div>
                    <div class="mb-3">
                        <label for="embalagem" class="form-label">Embalagem</label>
                        <input type="text" class="form-control" id="embalagem" name="embalagem" placeholder="Informe a embalagem do produto" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="4" placeholder="Descreva o produto" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-info">Salvar Produto</button>
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
