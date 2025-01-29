<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'db_conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $data_nascimento = $_POST['data-nascimento'];
    $estado = filter_input(INPUT_POST, 'opcoes', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $interesses = $_POST['interesse'] ?? [];
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
  

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = $_FILES['foto'];
        $nome_foto = uniqid() . "-" . basename($foto["name"]);
        $destino_foto = "imagens/" . $nome_foto;

        if (!move_uploaded_file($foto["tmp_name"], $destino_foto)) {
            die("Erro ao enviar a foto.");
        }
    } else {
        die("Nenhuma foto foi enviada ou ocorreu um erro.");
    }

    $sql_pessoa = "INSERT INTO pessoa (nome, email, data_nascimento, estado, endereco, sexo, login, senha, foto)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt_pessoa = $conexao->prepare($sql_pessoa)) {
        $stmt_pessoa->bind_param(
            "sssssssss",
            $nome, $email, $data_nascimento, $estado, $endereco, $sexo, $login, $senha, $nome_foto
        );

        if ($stmt_pessoa->execute()) {
            $pessoa_id = $stmt_pessoa->insert_id;

            foreach ($interesses as $interesse_nome) {
                $sql_get_interesse = "SELECT cod FROM interesse WHERE nome = ?";
                if ($stmt_get_interesse = $conexao->prepare($sql_get_interesse)) {
                    $stmt_get_interesse->bind_param("s", $interesse_nome);
                    $stmt_get_interesse->execute();
                    $result_interesse = $stmt_get_interesse->get_result();

                    if ($result_interesse->num_rows > 0) {
                        $row = $result_interesse->fetch_assoc();
                        $interesse_cod = $row['cod'];
                    } else {
                        $sql_insert_interesse = "INSERT INTO interesse (nome) VALUES (?)";
                        if ($stmt_insert_interesse = $conexao->prepare($sql_insert_interesse)) {
                            $stmt_insert_interesse->bind_param("s", $interesse_nome);
                            if ($stmt_insert_interesse->execute()) {
                                $interesse_cod = $stmt_insert_interesse->insert_id;
                            } else {
                                echo "<div class='notification is-danger'>Erro ao inserir interesse: " . $stmt_insert_interesse->error . "</div>";
                            }
                            $stmt_insert_interesse->close();
                        }
                    }

                    $sql_interesse = "INSERT INTO pessoa_interesse (fk_pessoa_cod, fk_interesse_cod) VALUES (?, ?)";
                    if ($stmt_interesse = $conexao->prepare($sql_interesse)) {
                        $stmt_interesse->bind_param("ii", $pessoa_id, $interesse_cod);

                        if (!$stmt_interesse->execute()) {
                            echo "<div class='notification is-danger'>Erro ao inserir interesse na relação pessoa_interesse: " . $stmt_interesse->error . "</div>";
                        }

                        $stmt_interesse->close();
                    }

                    $stmt_get_interesse->close();
                } else {
                    echo "<div class='notification is-danger'>Erro na preparação do SQL para obter interesse: " . $conexao->error . "</div>";
                }
            }

            echo "<div class='notification is-success'>Usuário cadastrado com sucesso!</div>";
        } else {
            echo "<div class='notification is-danger'>Erro ao cadastrar o usuário: " . $stmt_pessoa->error . "</div>";
        }

        $stmt_pessoa->close();
    } else {
        echo "<div class='notification is-danger'>Erro na preparação do SQL: " . $conexao->error . "</div>";
    }
} else {
    echo "<div class='notification is-warning'>Método de requisição inválido.</div>";
}

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Cadastro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>

<body>
    <section class="section">
        <div class="container has-text-centered">
            <h1 class="title">Operação Concluída</h1>
            <p>Clique no botão abaixo para voltar à página inicial.</p>
            <a href="home.php" class="button is-primary" style="margin-top: 20px;">Voltar para a Página Inicial</a>
        </div>
    </section>
</body>

</html>
