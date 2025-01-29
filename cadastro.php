<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <style>
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
            <h1 class="title">Cadastro de novo usuário</h1>
            <form action="formulario.php" method="post" enctype="multipart/form-data">
            
            <div class="actions-container">
                <a href="home.php" class="button is-primary">Verificar cadastros</a>
                <a href="logout.php" class="button is-info">Logout</a>
            </div>

                <div class="field">
                    <label class="label" for="nome">Nome Completo</label>
                    <div class="control">
                        <input class="input" type="text" name="nome" id="nome" placeholder="Seu nome completo" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="email">E-mail</label>
                    <div class="control">
                        <input class="input" type="email" name="email" id="email" placeholder="email@exemplo.com" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="data-nascimento">Data de Nascimento</label>
                    <div class="control">
                        <input class="input" type="date" name="data-nascimento" id="data-nascimento" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="opcoes">Estado:</label>
                    <div class="control">
                        <div class="select">
                            <select name="opcoes" id="opcoes" required>
                                <option value="SC">SC</option>
                                <option value="PR">PR</option>
                                <option value="RS">RS</option>
                                <option value="SP">SP</option>
                                <option value="MG">MG</option>
                                <option value="ES">ES</option>
                                <option value="RJ">RJ</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="endereco">Endereço</label>
                    <div class="control">
                        <input class="input" type="text" name="endereco" id="endereco" placeholder="Seu endereço" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Sexo</label>
                        <div class="control">
                            <label class="radio">
                            <input type="radio" name="sexo" value="Masculino" required>
                            Masculino
                            </label>
                    <label class="radio">
                        <input type="radio" name="sexo" value="Feminino" required>
                            Feminino
                    </label>
             </div>
        </div>


                <div class="field">
                    <label class="label">Categorias de Interesse</label>
                    <div class="control">
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Praia">
                            Praia
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Campo">
                            Campo
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Nacionais">
                            Nacionais
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Internacionais">
                            Internacionais
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Serra">
                            Serra
                        </label>
                        <label class="checkbox">
                            <input type="checkbox" name="interesse[]" value="Cidade">
                            Cidade
                        </label>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="login">Login</label>
                    <div class="control">
                        <input class="input" type="text" name="login" id="login" placeholder="Escolha seu login" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="senha">Senha</label>
                    <div class="control">
                        <input class="input" type="password" name="senha" id="senha" placeholder="Digite sua senha" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="foto">Foto do Perfil</label>
                    <div class="control">
                        <input type="file" name="foto" id="foto" accept="image/*" required>
                    </div>
                </div>

                <div class="control">
                    <button class="button is-primary" type="submit">Cadastrar</button>
                    <button class="button is-light" type="reset">Limpar campos</button>
                </div>
            </form>
        </div>
    </section>
</body>

</html>
