<?php
include 'conec.php';

// Obtém o ID da venda via GET
if (isset($_GET['id'])) {
    $vendaId = $_GET['id'];
} else {
    die("ID da venda não especificado.");
}

// Obtém a observação da venda
$sqlObservacao = "SELECT observacao FROM vendas WHERE id = $vendaId";
$resultObservacao = mysqli_query($conn, $sqlObservacao);
$observacao = "";
if ($row = mysqli_fetch_assoc($resultObservacao)) {
    $observacao = $row['observacao'];
}

// Obtém o ID do cliente antes de deletar a venda ou atualizar (caso seja necessário redirecionar)
$sqlCliente = "SELECT cliente_id FROM vendas WHERE id = ?";
$stmtCliente = mysqli_prepare($conn, $sqlCliente);
mysqli_stmt_bind_param($stmtCliente, "i", $vendaId);
mysqli_stmt_execute($stmtCliente);
mysqli_stmt_bind_result($stmtCliente, $clienteId);
mysqli_stmt_fetch($stmtCliente);
mysqli_stmt_close($stmtCliente);

// Verifica se foi enviada a requisição para atualizar a venda
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Recebe os valores do formulário
    $valorInput = $_POST['valor'];
    $observacaoInput = $_POST['observacao'];

    // Converte o valor recebido (formato "1.234,56" ou "R$ 1.234,56") para float
    $valorClean = str_replace(['R$', '.'], '', $valorInput); // remove "R$" e pontos
    $valorClean = str_replace(',', '.', $valorClean); // troca a vírgula pelo ponto
    $valorFloat = (float) $valorClean;

    // Prepara a query de atualização
    $sqlUpdate = "UPDATE vendas SET valor = ?, observacao = ? WHERE id = ?";
    $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "dsi", $valorFloat, $observacaoInput, $vendaId);
    mysqli_stmt_execute($stmtUpdate);
    $affectedRowsUpdate = mysqli_stmt_affected_rows($stmtUpdate);
    mysqli_stmt_close($stmtUpdate);

    if ($affectedRowsUpdate > 0) {
        // Redireciona para o dashboard com mensagem de sucesso
        header("Location: listar-pedidos.php?id=$clienteId");
        exit();
    } 
}

// Verifica se foi enviada a requisição para deletar a venda
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    // Deleta os produtos associados à venda para evitar problemas de chave estrangeira
    $sqlDeleteProdutos = "DELETE FROM venda_produtos WHERE venda_id = ?";
    $stmtProdutos = mysqli_prepare($conn, $sqlDeleteProdutos);
    mysqli_stmt_bind_param($stmtProdutos, "i", $vendaId);
    mysqli_stmt_execute($stmtProdutos);
    mysqli_stmt_close($stmtProdutos);

    // Deleta a venda
    $sqlDeleteVenda = "DELETE FROM vendas WHERE id = ?";
    $stmtVenda = mysqli_prepare($conn, $sqlDeleteVenda);
    mysqli_stmt_bind_param($stmtVenda, "i", $vendaId);
    mysqli_stmt_execute($stmtVenda);
    $affectedRows = mysqli_stmt_affected_rows($stmtVenda);
    mysqli_stmt_close($stmtVenda);

    if ($affectedRows > 0) {
        // Redireciona para a página de listar pedidos do cliente
        header("Location: listar-pedidos.php?id=$clienteId");
        exit();
    }  
}

// Consulta para buscar os dados da venda (após update, se houver, esta consulta buscará os dados atualizados)
$sql = "SELECT * FROM vendas WHERE id = $vendaId";
$result = mysqli_query($conn, $sql);
if ($result->num_rows > 0) {
    $venda = mysqli_fetch_assoc($result);
} else {
    die("Venda não encontrada.");
}

// Converte o valor para float e formata para exibição (considerando que o valor vem formatado)
$valor = str_replace(['R$', '.'], '', $venda['valor']);
$valor = str_replace(',', '.', $valor);
$valor = (float) $valor;
$valorFormatado = number_format($valor, 2, ',', '.');

// Formata a data da venda
$dataVenda = date("d/m/Y", strtotime($venda['data_venda']));

// Consulta para listar os clientes (caso necessário para exibição)
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
        <button class="btn btn-secondary mb-3" onclick="window.location.href='listar-pedidos.php?id=<?= $clienteId ?>'">
                <i class="bi bi-arrow-left-circle"></i> Voltar para os Pedidos
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
                        <!-- Exibe o nome do cliente em uma caixa -->
                        <?php
                        $clienteSql = "SELECT nome_completo FROM clientes WHERE id = " . $venda['cliente_id'];
                        $clienteResult = mysqli_query($conn, $clienteSql);
                        $cliente = mysqli_fetch_assoc($clienteResult);
                        ?>
                        <input type="text" class="form-control" value="<?= $cliente['nome_completo']; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="vendedor_id" class="form-label">Vendedor</label>
                        <?php
                        // Busca o vendedor a partir do id da venda
                        $vendaSql = "SELECT vendedor_id FROM vendas WHERE id = $vendaId";
                        $vendaResult = mysqli_query($conn, $vendaSql);
                        $vendaRow = mysqli_fetch_assoc($vendaResult);
                        $vendedorId = $vendaRow['vendedor_id'];
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
                    <textarea class="form-control" id="observacao" name="observacao" rows="4"><?= htmlspecialchars($observacao); ?></textarea>
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

                <!-- Botão de atualizar -->
                <div class="d-flex" style="gap: 10px;">
                    <button type="submit" name="update" class="btn btn-success">Salvar Alterações</button>
                </div>
            </form>

            <!-- Formulário separado para exclusão -->
            <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja excluir esta venda?');" class="mt-3">
                <button type="submit" name="delete" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Excluir Venda
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
