CREATE DATABASE marketplace;
USE marketplace;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    data_nascimento DATE NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    imagem VARCHAR(255),
    categoria_id INT,
    usuario_id INT, 
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    produto_id INT,
    quantidade INT NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,

    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

INSERT INTO categorias (nome) VALUES 
('Eletrônicos'),
('Roupas e Moda'),
('Casa e Jardim'),
('Esportes e Lazer'),
('Livros e Educação'),
('Beleza e Saúde'),
('Automóveis'),
('Informática'),
('Celulares e Tablets'),
('Eletrodomésticos');

CREATE TABLE compras (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  produto_id    INT NOT NULL,
  comprador_id  INT NOT NULL,
  data_compra   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status        VARCHAR(20) DEFAULT 'pendente',

  CONSTRAINT fk_compra_produto
    FOREIGN KEY (produto_id)   REFERENCES produtos(id)  ON DELETE CASCADE,
  CONSTRAINT fk_compra_usuario
    FOREIGN KEY (comprador_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;