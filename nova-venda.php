<?php
session_start(); // Inicia a sessão
include 'conec.php'; // Conexão com o banco de dados

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header('Location: index.php'); // Redireciona para o login
    exit();
}

$usuarioId = $_SESSION['id'];

// Consulta para obter o nome do vendedor
$vendedorQuery = "SELECT nome FROM usuarios WHERE id = ?";
$stmtVendedor = $conn->prepare($vendedorQuery);
$stmtVendedor->bind_param("i", $usuarioId);
$stmtVendedor->execute();
$stmtVendedor->bind_result($vendedorNome);
$stmtVendedor->fetch();
$stmtVendedor->close();

// Consulta para obter todos os clientes
$clientesQuery = "SELECT id, nome_completo FROM clientes";
$clientesResult = mysqli_query($conn, $clientesQuery);

// Consulta para obter todos os produtos
$produtosQuery = "SELECT codigo, nome FROM produtos";
$produtosResult = mysqli_query($conn, $produtosQuery);

// Armazena os produtos em um array para reutilização
$produtosArray = [];
while ($produto = mysqli_fetch_assoc($produtosResult)) {
    $produtosArray[] = $produto;
}

// Captura o cliente selecionado via GET (caso exista)
$clienteSelecionado = $_GET['cliente_id'] ?? null;
$clienteNome = "";

// Se houver um cliente selecionado, busca o nome dele
if ($clienteSelecionado) {
    $clienteNomeQuery = "SELECT nome_completo FROM clientes WHERE id = ?";
    $stmtCliente = $conn->prepare($clienteNomeQuery);
    $stmtCliente->bind_param("i", $clienteSelecionado);
    $stmtCliente->execute();
    $stmtCliente->bind_result($clienteNome);
    $stmtCliente->fetch();
    $stmtCliente->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Venda</title>
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
        .produto-item {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .produto-item .form-label {
            font-weight: bold;
        }
        .btn-info {
            margin-top: 20px;
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
                <h2 class="mb-4">Cadastrar Venda</h2>
                <form method="POST" action="processa-venda.php">
                    <!-- Campo Vendedor -->
                    <div class="mb-3">
                        <label class="form-label">Vendedor</label>
                        <input type="hidden" name="vendedor" value="<?= $usuarioId ?>">
                        <input type="text" class="form-control" value="<?= $vendedorNome ?>" disabled>
                    </div>

                    <!-- Campo Cliente como texto desativado -->
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <input type="hidden" name="cliente" value="<?= $clienteSelecionado ?>">
                        <input type="text" class="form-control" value="<?= $clienteNome ?>" disabled>
                    </div>

                    <!-- Seção de Produtos -->
                    <div id="produtos-container">
                        <div class="produto-item" id="produto-1">
                            <h4>Produto 1</h4>
                            <div class="mb-3">
                                <label class="form-label">Produto</label>
                                <select class="form-control" name="produto[]" required>
                                    <option value="" disabled selected>Selecione o Produto</option>
                                    <?php foreach ($produtosArray as $produto): ?>
                                        <option value="<?= $produto['codigo'] ?>"><?= $produto['nome'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantidade</label>
                                <input type="number" class="form-control" name="quantidade[]" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Letra da Tabela</label>
                                <input type="text" class="form-control" name="letra_tabela[]">
                            </div>
                        </div>
                    </div>

                    <!-- Botão para adicionar mais produtos -->
                    <button type="button" class="btn btn-info mb-3" onclick="adicionarProduto()">Adicionar mais produto</button>
                    <div class="mb-3">
    <label class="form-label" for="valorTotal">VALOR TOTAL</label>
    <input 
        type="text" 
        id="valorTotal" 
        class="form-control" 
        name="valor" 
        placeholder="R$ 0,00" 
        aria-describedby="valorHelp" 
        oninput="formatarValor(this)"
        onblur="validarValor(this)"
    >
</div>


                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea class="form-control" name="observacao"></textarea>
                    </div>
                   
                 <!-- Botão de Voltar -->
<a href="listar-pedidos.php?id=<?php echo $clienteSelecionado; ?>" class="btn btn-secondary">Voltar</a>


                    <button type="submit" class="btn btn-success">Cadastrar Venda</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Armazena as opções dos produtos em uma variável JavaScript
    const produtosOptions = `<?php
        $options = '<option value="" disabled selected>Selecione o Produto</option>';
        foreach ($produtosArray as $produto) {
            $options .= '<option value="' . $produto['codigo'] . '">' . $produto['nome'] . '</option>';
        }
        echo $options;
    ?>`;
</script>

    <script>
        let produtoCounter = 1;
        function adicionarProduto() {
    produtoCounter++;

    const produtosContainer = document.getElementById('produtos-container');
    const newProdutoItem = document.createElement('div');
    newProdutoItem.classList.add('produto-item');
    newProdutoItem.id = `produto-${produtoCounter}`;

    newProdutoItem.innerHTML = `
        <h4>Produto ${produtoCounter}</h4>
        <div class="mb-3">
            <label class="form-label">Produto</label>
            <select class="form-control" name="produto[]" required>
                ${produtosOptions} <!-- Usa a variável JavaScript corretamente -->
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantidade</label>
            <input type="number" class="form-control" name="quantidade[]" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Letra da Tabela</label>
            <input type="text" class="form-control" name="letra_tabela[]">
        </div>
    `;

    produtosContainer.appendChild(newProdutoItem);
}

    </script>
   
<script>
    // Função para formatar o valor no formato "R$ 0,00"
    function formatarValor(input) {
        let valor = input.value;
        
        // Remove qualquer coisa que não seja número ou vírgula
        valor = valor.replace(/[^0-9,]/g, '');

        // Verifica se há uma vírgula
        if (valor.indexOf(',') === -1) {
            // Adiciona ponto a cada 3 dígitos
            valor = valor.replace(/(\d)(?=(\d{3})+(\,|$))/g, '$1.');
        } else {
            // Formata o número até a vírgula
            valor = valor.replace(/(\d)(?=(\d{3})+\.)/g, '$1.');
        }

        // Adiciona o símbolo "R$" na frente
        input.value = 'R$ ' + valor;
    }

    // Função para garantir que o valor tenha duas casas decimais
    function validarValor(input) {
        let valor = input.value;
        // Remove o símbolo "R$" e qualquer caractere extra
        valor = valor.replace(/[^\d,]/g, '');
        
        // Adiciona vírgula caso não haja
        if (valor.indexOf(',') === -1) {
            valor += ',00';  // Adiciona ",00" se não houver vírgula
        } else {
            let partes = valor.split(',');
            if (partes[1].length < 2) {
                // Se houver apenas 1 casa decimal, adiciona o segundo zero
                valor = valor + '0';
            }
        }

        // Aplica o valor formatado novamente
        input.value = 'R$ ' + valor;
    }
</script>
</body>
</html>
