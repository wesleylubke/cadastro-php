<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'db_conexao.php';

$sql = "SELECT p.cod AS id, p.nome, p.email, p.data_nascimento, p.estado, p.endereco, p.sexo, p.login, p.foto,
               GROUP_CONCAT(i.nome SEPARATOR ', ') AS interesses
        FROM pessoa p
        LEFT JOIN pessoa_interesse pi ON p.cod = pi.fk_pessoa_cod
        LEFT JOIN interesse i ON pi.fk_interesse_cod = i.cod
        GROUP BY p.cod";
$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários Cadastrados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <style>
        .user-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .user-info {
            margin-bottom: 15px;
        }

        .user-actions {
            display: flex;
            justify-content: flex-end;
        }

        .user-actions a {
            margin-left: 10px;
        }

        .user-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .user-card {
            width: 100%;
            max-width: 400px;
        }

        .user-card img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .actions-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <section class="section">
        <div class="container">
            <h1 class="title">Usuários Cadastrados</h1>
            
            <div class="actions-container">
                <a href="cadastro.php" class="button is-primary">Novo Cadastro</a>
                <a href="logout.php" class="button is-info">Logout</a>
            </div>
            <div class="user-container">
                <?php
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <div class="user-card">
                    <div class="user-info">
                        <img src="imagens/<?php echo htmlspecialchars($row['foto'] ?? 'imagem_padrao.jpg'); ?>" alt="Foto do Usuário"><br>
                        <strong>ID:</strong> <?php echo $row['id']; ?><br>
                        <strong>Nome Completo:</strong> <?php echo htmlspecialchars($row['nome']); ?><br>
                        <strong>E-mail:</strong> <?php echo htmlspecialchars($row['email']); ?><br>
                        <strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($row['data_nascimento']); ?><br>
                        <strong>Estado:</strong> <?php echo htmlspecialchars($row['estado']); ?><br>
                        <strong>Endereço:</strong> <?php echo htmlspecialchars($row['endereco']); ?><br>
                        <strong>Sexo:</strong> <?php echo htmlspecialchars($row['sexo']); ?><br>
                        <strong>Interesses:</strong> <?php echo htmlspecialchars($row['interesses']); ?><br>
                        <strong>Login:</strong> <?php echo htmlspecialchars($row['login']); ?>
                    </div>
                    <div class="user-actions">
                        <a href="editar.php?id=<?php echo $row['id']; ?>" class="button is-small is-info">Editar</a>
                        <a href="excluir.php?id=<?php echo $row['id']; ?>" class="button is-small is-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </div>
                </div>
                <?php
                    endwhile;
                else:
                ?>
                <p>Nenhum usuário cadastrado.</p>
                <?php
                endif;
                ?>
            </div>
        </div>
    </section>
</body>

</html>

<?php
$conexao->close();
?>
