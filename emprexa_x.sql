-- --------------------------------------------------------
-- Criação do Banco de Dados
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS empresa_x
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE empresa_x;

-- --------------------------------------------------------
-- Tabela: departamentos
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS departamentos (
    id_departamento INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único do departamento',
    descricao VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nome/Descrição do departamento',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação do registro',
    INDEX idx_descricao (descricao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de departamentos da empresa';

-- --------------------------------------------------------
-- Tabela: funcionarios
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS funcionarios (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único do funcionário',
    nome VARCHAR(150) NOT NULL COMMENT 'Nome completo do funcionário',
    afastado ENUM('sim', 'não') NOT NULL DEFAULT 'não' COMMENT 'Indica se o funcionário está afastado',
    id_departamento INT NOT NULL COMMENT 'Referência ao departamento onde o funcionário está alocado',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de cadastro do funcionário',
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização no registro',
    
    -- Chave estrangeira: cada funcionário pertence a UM departamento
    FOREIGN KEY (id_departamento) REFERENCES departamentos(id_departamento)
        ON DELETE RESTRICT  -- Impede exclusão do depto se tiver funcionários (regra de negócio)
        ON UPDATE CASCADE,  -- Se ID do depto mudar (raro), atualiza aqui também

    INDEX idx_nome (nome),
    INDEX idx_afastado (afastado),
    INDEX idx_departamento (id_departamento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de funcionários da empresa';

-- --------------------------------------------------------
-- Inserção de Dados Exemplo (Opcional)
-- --------------------------------------------------------

INSERT INTO departamentos (descricao) VALUES
('Recursos Humanos'),
('Tecnologia da Informação'),
('Financeiro'),
('Marketing'),
('Operações');

INSERT INTO funcionarios (nome, afastado, id_departamento) VALUES
('Ana Beatriz Silva', 'não', 1),
('Carlos Eduardo Pereira', 'sim', 2),
('Fernanda Oliveira Costa', 'não', 3),
('Roberto Almeida Santos', 'não', 2),
('Juliana Mendes Lima', 'não', 4);