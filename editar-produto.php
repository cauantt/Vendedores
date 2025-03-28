<?php 
include 'conec.php'; 

// Verifica se o código do produto foi informado via GET
if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
} else {
    die("Código do produto não especificado.");
}

// Busca os dados do produto para pré-preencher o formulário
$query = "SELECT * FROM produtos WHERE codigo = '$codigo'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    $produto = mysqli_fetch_assoc($result);
} else {
    die("Produto não encontrado.");
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Se o botão de exclusão foi acionado
    if (isset($_POST['delete'])) {
        $queryInativar = "UPDATE produtos SET status = 'inativo' WHERE codigo = '$codigo'";
        if (mysqli_query($conn, $queryInativar)) {
            header("Location: listar-produtos.php");
            exit();
        } 
    } else {
        // Obtém os dados do formulário para atualização (exceto o código, que não deve ser alterado)
        $nome = $_POST['nome'];
        $embalagem = $_POST['embalagem'];
        $descricao = $_POST['descricao'];

        // Atualiza os dados na tabela 'produtos'
        $queryUpdate = "UPDATE produtos SET nome = '$nome', embalagem = '$embalagem', descricao = '$descricao' 
                        WHERE codigo = '$codigo'";
        if (mysqli_query($conn, $queryUpdate)) {
            header("Location: listar-produtos.php?success=Produto+atualizado+com+sucesso");
            exit();
        } else {
            echo "Erro ao atualizar o produto: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            text-transform: uppercase;
        }
        
        .btn-info {
            background-color: #007bff;
            color: white;
        }
        .btn-info:hover {
            background-color: #0056b3;
        }
        /* Para alinhar os botões de ação lado a lado */
        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
<?php include 'menu.php'; ?>
    <div class="d-flex">
       
        <div class="content flex-grow-1">
           
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Editar Produto</h2>
                    <a href="listar-produtos.php" class="btn btn-secondary">Voltar</a>
                </div>
                <form method="POST" action="">
                    <!-- Código do produto (somente leitura) -->
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" value="<?= htmlspecialchars($produto['codigo']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Informe o nome do produto" value="<?= htmlspecialchars($produto['nome']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="embalagem" class="form-label">Embalagem</label>
                        <input type="text" class="form-control" id="embalagem" name="embalagem" placeholder="Informe a embalagem do produto" value="<?= htmlspecialchars($produto['embalagem']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="4" placeholder="Descreva o produto" required><?= htmlspecialchars($produto['descricao']); ?></textarea>
                    </div>
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-info">Salvar Alterações</button>
                        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir Produto</button>
                    </div>
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
