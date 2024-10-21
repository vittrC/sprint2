<?php include('valida_sessao.php'); ?>
<?php include('conexao.php'); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $fornecedor_id = $_POST['fornecedor_id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];

    if ($id) {
        $sql = "UPDATE produtos SET fornecedor_id='$fornecedor_id', nome='$nome', descricao='$descricao', preco='$preco' WHERE id='$id'";
        $mensagem = "Produto atualizado com sucesso!";
    } else {
        $sql = "INSERT INTO produtos (fornecedor_id, nome, descricao, preco) VALUES ('$fornecedor_id', '$nome', '$descricao', '$preco')";
        $mensagem = "Produto cadastrado com sucesso!";
    }

    if ($conn->query($sql) !== TRUE) {
        $mensagem = "Erro: " . $conn->error;
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM produtos WHERE id='$delete_id'";
    if ($conn->query($sql) === TRUE) {
        $mensagem = "Produto excluído com sucesso!";
    } else {
        $mensagem = "Erro ao excluir produto: " . $conn->error;
    }
}

$produtos = $conn->query("SELECT p.id, p.nome, p.descricao, p.preco, f.nome AS fornecedor_nome FROM produtos p JOIN fornecedores f ON p.fornecedor_id = f.id");

$produto = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $produto = $conn->query("SELECT * FROM produtos WHERE id='$edit_id'")->fetch_assoc();
}

$fornecedores = $conn->query("SELECT id, nome FROM fornecedores");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produto</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container">
        <h2>Cadastro de Produto</h2>
        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo $produto['id'] ?? ''; ?>">
            <label for="fornecedor_id">Fornecedor:</label>
            <select name="fornecedor_id" required>
                <?php while ($row = $fornecedores->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" <?php if ($produto && $produto['fornecedor_id'] == $row['id']) echo 'selected'; ?>><?php echo $row['nome']; ?></option>
                <?php endwhile; ?>
            </select>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" value="<?php echo $produto['nome'] ?? ''; ?>" required>
            <label for="descricao">Descrição:</label>
            <textarea name="descricao"><?php echo $produto['descricao'] ?? ''; ?></textarea>
            <label for="preco">Preço:</label>
            <input type="text" name="preco" value="<?php echo $produto['preco'] ?? ''; ?>" required>
            <button type="submit"><?php echo $produto ? 'Atualizar' : 'Cadastrar'; ?></button>
        </form>
        <?php if (isset($mensagem)) echo "<p class='message " . ($conn->error ? "error" : "success") . "'>$mensagem</p>"; ?>

        <h2>Listagem de Produtos</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Fornecedor</th>
                <th>Ações</th>
            </tr>
            <?php while ($row = $produtos->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nome']; ?></td>
                <td><?php echo $row['descricao']; ?></td>
                <td><?php echo $row['preco']; ?></td>
                <td><?php echo $row['fornecedor_nome']; ?></td>
                <td>
                    <a href="?edit_id=<?php echo $row['id']; ?>">Editar</a>
                    <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
