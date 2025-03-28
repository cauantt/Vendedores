<?php
// Definindo as credenciais de conexão
$host = '127.0.0.1:3308';
$usuario = 'root';
$senha = 'root';
$banco_de_dados = 'pedidos';

// Criando a conexão
$conn = new mysqli($host, $usuario, $senha, $banco_de_dados);

// Verificando se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


?>
