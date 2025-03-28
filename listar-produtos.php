<?php 
include 'conec.php'; 

// Realiza a consulta ao banco para obter os produtos com status ativo
$query = "SELECT * FROM produtos WHERE status = 'ativo'";
$result = mysqli_query($conn, $query);

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
        .card {
            margin-bottom: 20px;
        }
        .btn-custom {
            background-color: rgba(0, 132, 255, 0.54);
            color: white;
        }
        .btn-custom:hover {
            background-color: #6fa3f8;
        }
        .btn-warning {
            background-color: #28a745;
            color: white;
        }
        .btn-warning:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Produtos Cadastrados</h2>
        <a href="cadastrar-produto.php" class="btn btn-custom" style="background-color: blue;">Cadastrar Produto</a>
    </div>

    <div class="row">
        <?php while ($produto = mysqli_fetch_assoc($result)) : ?>
            <div class="col-12 col-md-6 col-lg-4"> <!-- Responsivo: 1 em mobile, 2 em tablet, 3 em desktop -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($produto['nome']) ?></h5>
                        <p class="card-text"><strong>Código:</strong> <?= htmlspecialchars($produto['codigoVenda']) ?></p>
                        <p class="card-text"><strong>Embalagem:</strong> <?= htmlspecialchars($produto['embalagem']) ?></p>
                        <p class="card-text"><strong>Descrição:</strong> <?= htmlspecialchars($produto['descricao']) ?></p>
                        <a href="editar-produto.php?codigo=<?= urlencode($produto['codigo']) ?>" class="btn btn-warning btn-sm">Editar</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
