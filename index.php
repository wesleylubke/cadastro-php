<?php
session_start();
require_once 'db_conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $senha = $_POST['senha'];

    if (!empty($login) && !empty($senha)) {
        $sql = "SELECT cod, nome, login, senha FROM pessoa WHERE login = ?";
        if ($stmt = $conexao->prepare($sql)) {
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $nome, $login_db, $senha_hash);
                $stmt->fetch();

                if (password_verify($senha, $senha_hash)) {
                    $_SESSION['usuario_id'] = $id;
                    $_SESSION['usuario_nome'] = $nome;
                    header('Location: home.php');
                    exit;
                } else {
                    $_SESSION['erro'] = "Senha incorreta.";
                    header('Location: index.php');
                    exit;
                }
            } else {
                $_SESSION['erro'] = "Usuário não encontrado.";
                header('Location: index.php');
                exit;
            }
            $stmt->close();
        } else {
            $_SESSION['erro'] = "Erro ao preparar a consulta.";
            header('Location: index.php');
            exit;
        }
    } else {
        $_SESSION['erro'] = "Preencha todos os campos.";
        header('Location: index.php');
        exit;
    }
    $conexao->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="title">Login</h1>
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="notification is-danger">
                    <?php 
                    echo $_SESSION['erro']; 
                    unset($_SESSION['erro']);
                    ?>
                </div>
            <?php endif; ?>
            <form action="index.php" method="post">
                <div class="field">
                    <label class="label" for="login">Login</label>
                    <div class="control">
                        <input class="input" type="text" name="login" id="login" placeholder="Seu login" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="senha">Senha</label>
                    <div class="control">
                        <input class="input" type="password" name="senha" id="senha" placeholder="Sua senha" required>
                    </div>
                </div>

                <div class="control">
                    <button class="button is-primary" type="submit">Entrar</button>
                </div>
            </form>
        </div>
    </section>
</body>
</html>
