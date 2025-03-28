<?php 
include 'conec.php'; 

// Verifica se um ID foi passado na URL
$usuarioNome = "Usuário"; // Nome padrão caso não seja encontrado
$usuarioId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($usuarioId > 0) {
    // Consulta para obter o nome do cliente
    $sql = "SELECT nome_completo FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();
    $stmt->bind_result($nomeCliente);
    
    if ($stmt->fetch()) {
        $usuarioNome = $nomeCliente; 
    }
    $stmt->close();

    // Consulta os pedidos do cliente na tabela 'vendas'
    $sqlPedidos = "SELECT v.id, v.data_venda, u.nome AS vendedor 
                   FROM pedidos.vendas v
                   JOIN usuarios u ON v.vendedor_id = u.id
                   WHERE v.cliente_id = ?
                   ORDER BY v.data_venda DESC";

    $stmtPedidos = $conn->prepare($sqlPedidos);
    $stmtPedidos->bind_param("i", $usuarioId);
    $stmtPedidos->execute();
    $resultPedidos = $stmtPedidos->get_result();
} else {
    $resultPedidos = null;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            text-transform: uppercase;
            /* Tamanho de fonte padrão */
            font-size: 16px;
        }
        
        .btn-custom { 
            background-color: #007bff; 
            color: white; 
            border: none; 
        }
        .btn-custom:hover { 
            background-color: #0056b3; 
        }
        .btn-export { 
            background-color: #28a745; 
            color: white; 
            border: none; 
        }
        .btn-export:hover { 
            background-color: #218838; 
        }
        .btn-info { 
            background-color: #17a2b8; 
            color: white; 
        }
        .btn-info:hover { 
            background-color: #138496; 
        }
        .btn { 
            text-transform: uppercase; 
        }
        h2 { 
            text-transform: uppercase; 
        }
        .btn-back { 
            background-color: #6c757d; 
            color: white; 
            border: none; 
        }
        .btn-back:hover { 
            background-color: #5a6268; 
        }
        /* Responsividade para dispositivos móveis */
        @media (max-width: 768px) {
            body {
                font-size: 14px; /* Fonte menor para dispositivos móveis */
            }
            
            .content {
                margin-left: 0;
            }
            h2 {
                font-size: 18px; /* Reduz o tamanho do título */
                margin-left: 40px;
                margin-right: ;
            }
            .btn {
                font-size: 9px; /* Botões com fonte menor */
                padding: 10px 10px; /* Ajuste no padding se necessário */
            }
        }
    </style>
</head>
<body>
<?php include 'menu.php'; ?>
    <div class="d-flex">
      
        <div class="content flex-grow-1">
    
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button class="btn btn-back" onclick="window.location.href='dashboard.php'">
                        <i class="bi bi-arrow-left-circle"></i> Voltar 
                    </button>
                    <h2>Pedidos de <?php echo htmlspecialchars($usuarioNome); ?></h2>
                    <a href="nova-venda.php?cliente_id=<?= $usuarioId ?>">
                        <button class="btn btn-custom">Nova Venda</button>
                    </a>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Vendedor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultPedidos && $resultPedidos->num_rows > 0): ?>
                            <?php while ($pedido = $resultPedidos->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $pedido['id']; ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($pedido['data_venda'])); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['vendedor']); ?></td>
                                    <td>
                                        <a href="editar-vendas.php?id=<?php echo $pedido['id']; ?>" class="btn btn-info btn-sm">Editar</a>
                                        <button class="btn btn-export btn-sm">Exportar</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Nenhuma venda encontrada para este cliente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            // Se for para mobile, alterna a classe active
            if(window.innerWidth <= 768){
                sidebar.classList.toggle("active");
            } else {
                sidebar.classList.toggle("hidden-sidebar");
                document.querySelector(".content").classList.toggle("collapsed");
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
