<?php
session_start(); // Inicia a sessão
include 'conec.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validação básica
        if (empty($_POST['cliente'])) {
            throw new Exception("Cliente não especificado!");
        }

        // Dados da sessão e formulário
        $vendedor_id = $_SESSION['id'];
        $cliente_id = (int)$_POST['cliente'];

        // Verifica se o cliente existe
        $checkCliente = mysqli_query($conn, "SELECT id FROM clientes WHERE id = $cliente_id");
        if (mysqli_num_rows($checkCliente) === 0) {
            throw new Exception("Cliente inválido!");
        }

        // Recebe os dados do formulário
        $observacao = mysqli_real_escape_string($conn, $_POST['observacao']);
        $valor = mysqli_real_escape_string($conn, $_POST['valor']); // Recebe o valor do campo "valor"
        $produtos = $_POST['produto'];
        $quantidades = $_POST['quantidade'];
        $letras = $_POST['letra_tabela'];

        // Verifica se o valor foi passado corretamente
        if (empty($valor)) {
            throw new Exception("Valor não especificado!");
        }

        // Transação para garantir integridade
        mysqli_begin_transaction($conn);

        // Insere a venda no banco de dados
        $sqlVenda = "INSERT INTO vendas (vendedor_id, cliente_id, observacao, valor) 
                    VALUES ('$vendedor_id', '$cliente_id', '$observacao', '$valor')";
        
        if (!mysqli_query($conn, $sqlVenda)) {
            throw new Exception("Erro na venda: " . mysqli_error($conn));
        }

        $venda_id = mysqli_insert_id($conn);

        // Insere os produtos relacionados à venda
        foreach ($produtos as $index => $produto_codigo) {
            $quantidade = (int)$quantidades[$index];
            $letra = mysqli_real_escape_string($conn, $letras[$index] ?? '');

            // Verifica se o produto existe
            $checkProduto = mysqli_query($conn, "SELECT codigo FROM produtos WHERE codigo = '$produto_codigo'");
            if (mysqli_num_rows($checkProduto) === 0) {
                throw new Exception("Produto inválido: $produto_codigo");
            }

            $sqlItem = "INSERT INTO venda_produtos (venda_id, produto_codigo, quantidade, letra_tabela) 
                       VALUES ('$venda_id', '$produto_codigo', '$quantidade', '$letra')";
            
            if (!mysqli_query($conn, $sqlItem)) {
                throw new Exception("Erro no item: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        header("Location: listar-pedidos.php?id=$cliente_id");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        die("Erro: " . $e->getMessage());
    }
} else {
    die("Método inválido!");
}
