<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'db_conexao.php';

$id = $_GET['id'] ?? null;

if ($id === null) {
    header('Location: home.php');
    exit;
}

$sql = "SELECT p.nome, p.email, p.data_nascimento, p.estado, p.endereco, p.sexo, p.login, p.foto,
               GROUP_CONCAT(i.nome SEPARATOR ',') AS interesses
        FROM pessoa p
        LEFT JOIN pessoa_interesse pi ON p.cod = pi.fk_pessoa_cod
        LEFT JOIN interesse i ON pi.fk_interesse_cod = i.cod
        WHERE p.cod = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    header('Location: home.php');
    exit;
}

$stmt->bind_result($nome, $email, $data_nascimento, $estado, $endereco, $sexo, $login, $foto_atual, $interesses);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
    $data_nascimento = $_POST['data-nascimento'];
    $estado = filter_input(INPUT_POST, 'opcoes', FILTER_SANITIZE_SPECIAL_CHARS);
    $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_SPECIAL_CHARS);
    $sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_SPECIAL_CHARS);
    $interesses = $_POST['interesse'] ?? [];
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);

    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_nome = uniqid() . '-' . basename($_FILES['foto']['name']);
        $caminho_foto = 'imagens/' . $foto_nome;

        if (file_exists('imagens/' . $foto_atual)) {
            unlink('imagens/' . $foto_atual);
        }

        if (move_uploaded_file($foto_tmp, $caminho_foto)) {
            $sql = "UPDATE pessoa SET nome = ?, email = ?, data_nascimento = ?, estado = ?, endereco = ?, sexo = ?, login = ?, foto = ? WHERE cod = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssssssssi", $nome, $email, $data_nascimento, $estado, $endereco, $sexo, $login, $foto_nome, $id);
        }
    } else {
        $sql = "UPDATE pessoa SET nome = ?, email = ?, data_nascimento = ?, estado = ?, endereco = ?, sexo = ?, login = ? WHERE cod = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sssssssi", $nome, $email, $data_nascimento, $estado, $endereco, $sexo, $login, $id);
    }

    if ($stmt->execute()) {
        
        $delete_sql = "DELETE FROM pessoa_interesse WHERE fk_pessoa_cod = ?";
        $stmt_delete = $conexao->prepare($delete_sql);
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();
        $stmt_delete->close();

        foreach ($interesses as $interesse_nome) {
            
            $sql_interesse = "SELECT cod FROM interesse WHERE nome = ?";
            $stmt_interesse = $conexao->prepare($sql_interesse);
            $stmt_interesse->bind_param("s", $interesse_nome);
            $stmt_interesse->execute();
            $stmt_interesse->bind_result($interesse_cod);
            if ($stmt_interesse->fetch()) {
                
                $stmt_interesse->close();
            } else {
                
                $stmt_interesse->close();
                $sql_insert_interesse = "INSERT INTO interesse (nome) VALUES (?)";
                $stmt_insert = $conexao->prepare($sql_insert_interesse);
                $stmt_insert->bind_param("s", $interesse_nome);
                $stmt_insert->execute();
                $interesse_cod = $stmt_insert->insert_id;
                $stmt_insert->close();
            }

            
            $sql_pessoa_interesse = "INSERT INTO pessoa_interesse (fk_pessoa_cod, fk_interesse_cod) VALUES (?, ?)";
            $stmt_pessoa_interesse = $conexao->prepare($sql_pessoa_interesse);
            $stmt_pessoa_interesse->bind_param("ii", $id, $interesse_cod);
            $stmt_pessoa_interesse->execute();
            $stmt_pessoa_interesse->close();
        }

        header('Location: home.php');
        exit;
    } else {
        echo "<div class='notification is-danger'>Erro ao atualizar o usuário: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>

<body>
    <section class="section">
        <div class="container">
            <h1 class="title">Editar Usuário</h1>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label class="label" for="nome">Nome Completo</label>
                    <div class="control">
                        <input class="input" type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="email">E-mail</label>
                    <div class="control">
                        <input class="input" type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="data-nascimento">Data de Nascimento</label>
                    <div class="control">
                        <input class="input" type="date" name="data-nascimento" id="data-nascimento" value="<?php echo htmlspecialchars($data_nascimento); ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="opcoes">Estado:</label>
                    <div class="control">
                        <div class="select">
                            <select name="opcoes" id="opcoes" required>
                                <option value="SC" <?php echo $estado == 'SC' ? 'selected' : ''; ?>>SC</option>
                                <option value="PR" <?php echo $estado == 'PR' ? 'selected' : ''; ?>>PR</option>
                                <option value="RS" <?php echo $estado == 'RS' ? 'selected' : ''; ?>>RS</option>
                                <option value="SP" <?php echo $estado == 'SP' ? 'selected' : ''; ?>>SP</option>
                                <option value="MG" <?php echo $estado == 'MG' ? 'selected' : ''; ?>>MG</option>
                                <option value="ES" <?php echo $estado == 'ES' ? 'selected' : ''; ?>>ES</option>
                                <option value="RJ" <?php echo $estado == 'RJ' ? 'selected' : ''; ?>>RJ</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="endereco">Endereço</label>
                    <div class="control">
                        <input class="input" type="text" name="endereco" id="endereco" value="<?php echo htmlspecialchars($endereco); ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Sexo</label>
                    <div class="control">
                        <label class="radio">
                            <input type="radio" name="sexo" value="Masculino" <?php echo $sexo == 'Masculino' ? 'checked' : ''; ?>>
                            Masculino
                        </label>
                        <label class="radio">
                            <input type="radio" name="sexo" value="Feminino" <?php echo $sexo == 'Feminino' ? 'checked' : ''; ?>>
                            Feminino
                        </label>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Categorias de Interesse</label>
                    <div class="control">
                        <?php
                        $interesses_array = explode(",", $interesses);
                        ?>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Praia" <?php echo in_array('Praia', $interesses_array) ? 'checked' : ''; ?>>
                            Praia
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Campo" <?php echo in_array('Campo', $interesses_array) ? 'checked' : ''; ?>>
                            Campo
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Nacionais" <?php echo in_array('Nacionais', $interesses_array) ? 'checked' : ''; ?>>
                            Nacionais
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Internacionais" <?php echo in_array('Internacionais', $interesses_array) ? 'checked' : ''; ?>>
                            Internacionais
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Serra" <?php echo in_array('Serra', $interesses_array) ? 'checked' : ''; ?>>
                            Serra
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Cidade" <?php echo in_array('Cidade', $interesses_array) ? 'checked' : ''; ?>>
                            Cidade
                        </label>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Foto Atual</label>
                    <div class="control">
                        <figure class="image is-128x128">
                            <img src="imagens/<?php echo htmlspecialchars($foto_atual ?? 'imagem_padrao.jpg'); ?>" alt="Foto do Usuário"><br>
                        </figure>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="foto">Alterar Foto</label>
                    <div class="control">
                        <input type="file" name="foto" id="foto" accept="image/*">
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="login">Login</label>
                    <div class="control">
                        <input class="input" type="text" name="login" id="login" value="<?php echo htmlspecialchars($login); ?>" required>
                    </div>
                </div>

                <div class="control">
                    <button class="button is-primary" type="submit">Salvar Alterações</button>
                    <a href="home.php" class="button is-light">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</body>
</html>
