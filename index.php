<?php
require 'conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresa X - Sistema de RH Avançado</title>
    <link rel="stylesheet" href="estilo/style.css">
</head>
<body>

<div class="toast" id="toast"></div>

<div class="container">
    <header>
        <h1>🏢 Sistema de Gestão de RH</h1>
        <p>Empresa X — Cadastro e gerenciamento de departamentos e funcionários</p>
    </header>

    <div class="tabs">
        <button class="tab active" data-tab="departamentos">📋 Departamentos</button>
        <button class="tab" data-tab="funcionarios">👥 Funcionários</button>
        <button class="tab" data-tab="gerenciar">⚙️ Gerenciar Registros</button>
    </div>

    <!-- Aba: Departamentos -->
    <div class="tab-content active" id="departamentos">
        <div class="card">
            <h2>➕ Cadastrar Novo Departamento</h2>
            <form id="form-departamento">
                <div class="form-group">
                    <label for="descricao">Descrição do Departamento</label>
                    <input type="text" id="descricao" name="descricao" required placeholder="Ex: Recursos Humanos">
                </div>
                <button type="submit" class="btn-primary">Cadastrar Departamento</button>
            </form>
        </div>

        <div class="card">
            <h2>📊 Lista de Departamentos</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Qtd. Funcionários</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="lista-departamentos">
                        <?php
                        $stmt = $pdo->query("
                            SELECT d.id_departamento, d.descricao, COUNT(f.id_funcionario) as qtd
                            FROM departamentos d
                            LEFT JOIN funcionarios f ON d.id_departamento = f.id_departamento
                            GROUP BY d.id_departamento, d.descricao
                            ORDER BY d.descricao
                        ");
                        while ($row = $stmt->fetch()) {
                            $qtd = $row['qtd'];
                            $btnExcluir = $qtd == 0 
                                ? "<button class='action-btn btn-danger btn-excluir-depto' data-id='{$row['id_departamento']}'>🗑️ Excluir</button>"
                                : "<span style='color:#9ca3af;'>🚫 Não permitido</span>";
                            
                            echo "<tr>
                                    <td>{$row['id_departamento']}</td>
                                    <td>{$row['descricao']}</td>
                                    <td>{$qtd}</td>
                                    <td>{$btnExcluir}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Aba: Funcionários -->
    <div class="tab-content" id="funcionarios">
        <div class="card">
            <h2>➕ Cadastrar Novo Funcionário</h2>
            <form id="form-funcionario">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required placeholder="Ex: Maria Silva">
                </div>
                <div class="form-group">
                    <label for="afastado">Status de Afastamento</label>
                    <select id="afastado" name="afastado">
                        <option value="não">Não</option>
                        <option value="sim">Sim</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="depto_id">Departamento</label>
                    <select id="depto_id" name="id_departamento" required>
                        <option value="">-- Selecione --</option>
                        <?php
                        $stmt = $pdo->query("SELECT id_departamento, descricao FROM departamentos ORDER BY descricao");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id_departamento']}'>{$row['descricao']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Cadastrar Funcionário</button>
            </form>
        </div>

        <div class="card">
            <h2>📋 Lista de Funcionários</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Afastado</th>
                            <th>Departamento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT f.id_funcionario, f.nome, f.afastado, d.descricao as depto
                            FROM funcionarios f
                            LEFT JOIN departamentos d ON f.id_departamento = d.id_departamento
                            ORDER BY f.nome
                        ");
                        while ($row = $stmt->fetch()) {
                            $depto = $row['depto'] ?? '—';
                            $afastado = $row['afastado'] == 'sim' ? '🔴 Sim' : '🟢 Não';
                            echo "<tr>
                                    <td>{$row['id_funcionario']}</td>
                                    <td>{$row['nome']}</td>
                                    <td>{$afastado}</td>
                                    <td>{$depto}</td>
                                    <td>
                                        <button class='action-btn btn-primary btn-editar' data-id='{$row['id_funcionario']}'>✏️ Editar</button>
                                        <button class='action-btn btn-danger btn-excluir-func' data-id='{$row['id_funcionario']}'>🗑️ Excluir</button>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Aba: Gerenciar -->
    <div class="tab-content" id="gerenciar">
        <div class="card">
            <h2>🔍 Buscar e Gerenciar Funcionário</h2>
            <form id="form-busca" style="max-width:400px; margin-bottom:1.5rem;">
                <div class="form-group">
                    <label for="busca-id">ID do Funcionário</label>
                    <input type="number" id="busca-id" placeholder="Digite o ID" min="1">
                </div>
                <button type="submit" class="btn-primary">Buscar Funcionário</button>
            </form>

            <div id="resultado-edicao" style="display:none;">
                <h3>📝 Editando Funcionário: <span id="edit-nome-titulo"></span></h3>
                <form id="form-edicao">
                    <input type="hidden" id="edit-id" name="id_funcionario">
                    <div class="form-group">
                        <label for="edit-nome">Nome</label>
                        <input type="text" id="edit-nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-afastado">Afastado?</label>
                        <select id="edit-afastado" name="afastado">
                            <option value="não">Não</option>
                            <option value="sim">Sim</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-depto">Departamento</label>
                        <select id="edit-depto" name="id_departamento" required>
                            <option value="">-- Selecione --</option>
                            <?php
                            $stmt = $pdo->query("SELECT id_departamento, descricao FROM departamentos ORDER BY descricao");
                            while ($row = $stmt->fetch()) {
                                echo "<option value='{$row['id_departamento']}'>{$row['descricao']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">💾 Atualizar Dados</button>
                    <button type="button" id="cancelar-edicao" class="btn-warning" style="margin-left:10px;">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    const toast = document.getElementById('toast');
    const resultadoEdicao = document.getElementById('resultado-edicao');
    const formBusca = document.getElementById('form-busca');
    const formEdicao = document.getElementById('form-edicao');
    const cancelarEdicaoBtn = document.getElementById('cancelar-edicao');

    // Função para mostrar toast
    function showToast(message, type = 'success') {
        toast.textContent = message;
        toast.className = 'toast ' + type;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4000);
    }

    // Troca de abas
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });

    // Cadastro de Departamento
    document.getElementById('form-departamento')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        const res = await fetch('acoes/cadastrar_departamento.php', {
            method: 'POST',
            body: formData
        });
        const result = await res.json();
        
        showToast(result.message, result.status);
        if (result.status === 'success') {
            this.reset();
            location.reload();
        }
    });

    // Cadastro de Funcionário
    document.getElementById('form-funcionario')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        const res = await fetch('acoes/cadastrar_funcionario.php', {
            method: 'POST',
            body: formData
        });
        const result = await res.json();
        
        showToast(result.message, result.status);
        if (result.status === 'success') {
            this.reset();
            location.reload();
        }
    });

    // Exclusão de Funcionário
    document.querySelectorAll('.btn-excluir-func').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Tem certeza que deseja excluir este funcionário?')) return;
            
            const id = this.dataset.id;
            const res = await fetch('acoes/excluir_funcionario.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id)
            });
            const result = await res.json();
            
            showToast(result.message, result.status);
            if (result.status === 'success') {
                location.reload();
            }
        });
    });

    // Exclusão de Departamento
    document.querySelectorAll('.btn-excluir-depto').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Tem certeza? Isso só é permitido se não houver funcionários vinculados.')) return;
            
            const id = this.dataset.id;
            const res = await fetch('acoes/excluir_departamento.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id)
            });
            const result = await res.json();
            
            showToast(result.message, result.status);
            if (result.status === 'success') {
                location.reload();
            }
        });
    });

    // ✅ NOVO: Editar ao clicar no botão da lista
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;

            try {
                const res = await fetch(`acoes/buscar_funcionario.php?id=${id}`);
                const func = await res.json();

                if (!func || !func.id_funcionario) {
                    showToast('Funcionário não encontrado.', 'error');
                    return;
                }

                // Preenche o formulário de edição
                document.getElementById('edit-id').value = func.id_funcionario;
                document.getElementById('edit-nome').value = func.nome;
                document.getElementById('edit-afastado').value = func.afastado;
                document.getElementById('edit-depto').value = func.id_departamento;
                document.getElementById('edit-nome-titulo').textContent = func.nome;

                // Mostra o formulário
                resultadoEdicao.style.display = 'block';

                // Muda para a aba "Gerenciar"
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                document.querySelector('.tab[data-tab="gerenciar"]').classList.add('active');
                document.getElementById('gerenciar').classList.add('active');

                // Scroll suave até o formulário
                resultadoEdicao.scrollIntoView({ behavior: 'smooth' });
            } catch (err) {
                showToast('Erro ao carregar dados do funcionário.', 'error');
            }
        });
    });

    // Buscar funcionário por ID (manualmente)
    formBusca?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('busca-id').value;
        if (!id) {
            showToast('Informe um ID válido.', 'warning');
            return;
        }

        try {
            const res = await fetch(`acoes/buscar_funcionario.php?id=${id}`);
            const func = await res.json();

            if (!func || !func.id_funcionario) {
                showToast('Funcionário não encontrado.', 'error');
                resultadoEdicao.style.display = 'none';
                return;
            }

            document.getElementById('edit-id').value = func.id_funcionario;
            document.getElementById('edit-nome').value = func.nome;
            document.getElementById('edit-afastado').value = func.afastado;
            document.getElementById('edit-depto').value = func.id_departamento;
            document.getElementById('edit-nome-titulo').textContent = func.nome;
            resultadoEdicao.style.display = 'block';
        } catch (err) {
            showToast('Erro ao buscar funcionário.', 'error');
        }
    });

    // Cancelar edição
    cancelarEdicaoBtn?.addEventListener('click', function() {
        resultadoEdicao.style.display = 'none';
        formBusca?.reset();
    });

    // Atualizar funcionário
    formEdicao?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        const res = await fetch('acoes/atualizar_funcionario.php', {
            method: 'POST',
            body: formData
        });
        const result = await res.json();
        
        showToast(result.message, result.status);
        if (result.status === 'success') {
            location.reload();
        }
    });
});
</script>

</body>
</html>