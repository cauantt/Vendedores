<?php
include 'conec.php';

// Obtém o ID da venda via GET
if (isset($_GET['id'])) {
    $vendaId = $_GET['id'];
} else {
    die("ID da venda não especificado.");
}

// Se o formulário de exclusão for enviado, exclui a venda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $sql = "DELETE FROM vendas WHERE id = $vendaId";
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php?msg=deleted");
        exit;
    } else {
        $error_message = "Erro ao excluir a venda: " . mysqli_error($conn);
    }
}

// Se o formulário de atualização for enviado, atualiza os dados
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $cliente_id = $_POST['cliente_id'];
    $observacao = $_POST['observacao'];
    $valor = $_POST['valor'];

    // Atualiza os dados da venda na tabela
    $sql = "UPDATE vendas SET 
                cliente_id = '$cliente_id',
                observacao = '$observacao',
                valor = '$valor'
            WHERE id = $vendaId";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Venda atualizada com sucesso!";
    } else {
        $error_message = "Erro ao atualizar a venda: " . mysqli_error($conn);
    }
}

// Consulta para buscar os dados da venda
$sql = "SELECT * FROM vendas WHERE id = $vendaId";
$result = mysqli_query($conn, $sql);
if ($result->num_rows > 0) {
    $venda = mysqli_fetch_assoc($result);
} else {
    die("Venda não encontrada.");
}

// Convertendo o valor para um número float antes de formatá-lo
$valor = str_replace(['R$', '.'], '', $venda['valor']); // Remove "R$" e "." (se houver)
$valor = str_replace(',', '.', $valor); // Troca a vírgula por ponto
$valor = (float) $valor; // Converte para float

// Agora aplica o number_format
$valorFormatado = number_format($valor, 2, ',', '.');

// Formata a data (considerando que data_venda está no formato 'Y-m-d')
$dataVenda = date("d/m/Y", strtotime($venda['data_venda']));

// Consulta para listar os clientes
$sqlClientes = "SELECT id, nome_completo FROM clientes";
$resultClientes = mysqli_query($conn, $sqlClientes);

// Consulta para listar os produtos associados à venda
$sqlProdutos = "SELECT p.nome, vp.quantidade, vp.letra_tabela 
                FROM venda_produtos vp 
                JOIN produtos p ON vp.produto_codigo = p.codigo 
                WHERE vp.venda_id = $vendaId";
$resultProdutos = mysqli_query($conn, $sqlProdutos);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Venda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
        .form-control, .form-select, textarea {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <div id="sidebar" class="sidebar">
        <a href="dashboard.php">Listar vendas</a>
        <a href="nova-venda.php">Fazer pedido</a>
        <a href="listar-produtos.php">Listar produtos</a>
        <a href="cadastrar-produto.php">Cadastrar produtos</a>
        <a href="listar-clientes.php">Listar clientes</a>
        <a href="novo-cliente.php">Cadastrar clientes</a>
    </div>
    <div class="content flex-grow-1">
        <button class="btn btn-primary mb-3" onclick="toggleSidebar()">☰</button>
        <div class="container mt-4">
            <button class="btn btn-secondary mb-3" onclick="window.location.href='dashboard.php'">
                <i class="bi bi-arrow-left-circle"></i> Voltar para o Dashboard
            </button>
            
            <h2 class="mb-4">Editar Venda</h2>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?= $success_message; ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message; ?></div>
            <?php endif; ?>

            <!-- Formulário de Atualização -->
            <form method="POST" action="">

                <div class="row mb-3">
                    <div class="col-md-6">
    <label for="cliente_id" class="form-label">Cliente</label>
    <!-- Exibe o nome do cliente dentro de uma caixa -->
    <?php
    // Busca o nome do cliente a partir do cliente_id da venda
    $clienteSql = "SELECT nome_completo FROM clientes WHERE id = " . $venda['cliente_id'];
    $clienteResult = mysqli_query($conn, $clienteSql);
    $cliente = mysqli_fetch_assoc($clienteResult);
    ?>
    <input type="text" class="form-control" value="<?= $cliente['nome_completo']; ?>" readonly>
</div>
<div class="col-md-6">
    <label for="vendedor_id" class="form-label">Vendedor</label>
    <?php
    // Busca o vendedor_id da venda
    $vendaSql = "SELECT vendedor_id FROM vendas WHERE id = $vendaId"; // Assumindo que a variável $vendaId contém o ID da venda
    $vendaResult = mysqli_query($conn, $vendaSql);
    $venda = mysqli_fetch_assoc($vendaResult);

    // Agora, pega o nome do vendedor a partir do vendedor_id
    $vendedorId = $venda['vendedor_id'];
    $vendedorSql = "SELECT nome FROM usuarios WHERE id = $vendedorId";
    $vendedorResult = mysqli_query($conn, $vendedorSql);
    $vendedor = mysqli_fetch_assoc($vendedorResult);
    ?>
    <input type="text" class="form-control" value="<?= $vendedor['nome']; ?>" readonly>
</div>




                    
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="data_venda" class="form-label">Data da Venda</label>
                        <input type="text" class="form-control" value="<?= $dataVenda; ?>" readonly>
                    </div>
                
                <div class="col-md-6">
                        <label for="valor" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="valor" name="valor" value="<?= $valorFormatado; ?>" required>
                    </div>

                    </div>
                    <div class="mb-3">
    <label for="observacao" class="form-label">Observação</label>
    <textarea class="form-control" id="observacao" name="observacao" rows="4"><?= isset($venda['observacao']) ? htmlspecialchars($venda['observacao']) : ''; ?></textarea>
</div>



                <!-- Exibição dos produtos da venda -->
                <h4 class="mb-3">Produtos da Venda</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Produto</th>
                            <th scope="col">Quantidade</th>
                            <th scope="col">Letra da Tabela</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($produto = mysqli_fetch_assoc($resultProdutos)): ?>
                            <tr>
                                <td><?= $produto['nome']; ?></td>
                                <td><?= $produto['quantidade']; ?></td>
                                <td><?= $produto['letra_tabela']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Botões lado a lado -->
                <div class="d-flex" style="gap: 10px;">
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                    <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja excluir esta venda?');">
                        <button type="submit" name="delete" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Excluir Venda
                        </button>
                    </form>
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
