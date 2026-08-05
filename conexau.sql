CREATE DATABASE conexau;
USE conexau;

CREATE TABLE tbl_usuario (
  id bigint NOT NULL AUTO_INCREMENT,
  nome varchar(100) NOT NULL,
  email varchar(50) UNIQUE NOT NULL,
  senha varchar(255) NOT NULL,
  perfil varchar(50) NOT NULL,
  status varchar(50) NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (id)
);

CREATE TABLE tbl_profissional (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  especialidade varchar(150) NOT NULL,
  numero_registro varchar(100) NOT NULL,
  criado_em timestamp DEFAULT current_timestamp(),
  fk_usuario bigint NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (fk_usuario) REFERENCES tbl_usuario(id)
);

CREATE TABLE tbl_paciente (
  id bigint NOT NULL AUTO_INCREMENT,
  contato_emergencia varchar(150) NOT NULL,
  data_nascimento date NOT NULL,
  criado_em timestamp DEFAULT current_timestamp(),
  fk_usuario bigint NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (fk_usuario) REFERENCES tbl_usuario(id)
);

CREATE TABLE tbl_agendamento (
  id bigint NOT NULL AUTO_INCREMENT,
  data_agendamento date NOT NULL,
  horario_inicio time NOT NULL,
  horario_fim time NOT NULL,
  status enum('agendado','em_andamento', 'pendente', 'concluido','cancelado') DEFAULT 'pendente',
  criado_em timestamp DEFAULT current_timestamp(),
  fk_profissional bigint NOT NULL,
  fk_paciente bigint NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (fk_profissional) REFERENCES tbl_profissional(id),
  FOREIGN KEY (fk_paciente) REFERENCES tbl_paciente(id)
);

CREATE TABLE tbl_triagem (
  id bigint NOT NULL AUTO_INCREMENT,
  nome_solicitante varchar(150) NOT NULL,
  status enum('pendente','aprovada','recusada') DEFAULT 'pendente',
  criado_em timestamp DEFAULT current_timestamp(),
  fk_paciente bigint NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (fk_paciente) REFERENCES tbl_paciente(id)
);

CREATE TABLE tbl_visita (
  id bigint NOT NULL AUTO_INCREMENT,
  horario_inicio datetime NOT NULL,
  horario_fim datetime NOT NULL,
  status enum('agendado','em_andamento','concluido','cancelado') DEFAULT 'agendado',
  criado_em timestamp DEFAULT current_timestamp(),
  fk_profissional bigint NOT NULL,
  fk_paciente bigint NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (fk_profissional) REFERENCES tbl_profissional(id),
  FOREIGN KEY (fk_paciente) REFERENCES tbl_paciente(id)
);

CREATE TABLE tbl_prontuario (
    id bigint NOT NULL AUTO_INCREMENT,
    fk_paciente bigint NOT NULL,
    fk_agendamento bigint NOT NULL,
    queixa_principal text,
    historico_medico text,
    sinais_vitais text,
    analise_ia json,
    diagnostico_final text,
    conduta_medica text,
    criado_em timestamp DEFAULT current_timestamp(),
    updated_at timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (id),
    FOREIGN KEY (fk_paciente) REFERENCES tbl_paciente(id),
    FOREIGN KEY (fk_agendamento) REFERENCES tbl_agendamento(id)
);

CREATE TABLE tbl_protocolos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria ENUM('cronico', 'agudo', 'investigacao', 'acompanhamento') DEFAULT 'agudo',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Perguntas dos Protocolos
CREATE TABLE tbl_perguntas_protocolo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fk_protocolo INT NOT NULL,
    ordem INT NOT NULL DEFAULT 0,
    tipo ENUM('texto', 'numero', 'select', 'radio', 'checkbox', 'textarea') DEFAULT 'texto',
    pergunta TEXT NOT NULL,
    placeholder VARCHAR(255),
    obrigatorio BOOLEAN DEFAULT FALSE,
    opcoes JSON, -- Para campos select/radio/checkbox: ["Opção 1", "Opção 2"]
    condicional JSON, -- Lógica condicional: {"campo": "tem_alergia", "operador": "=", "valor": "sim"}
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fk_protocolo) REFERENCES tbl_protocolos(id) ON DELETE CASCADE,
    INDEX idx_protocolo (fk_protocolo),
    INDEX idx_ordem (ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO tbl_usuario (id, nome, email, senha, perfil) VALUES
(1, 'Dr. Arnaldo Silva', 'arnaldo.silva@conexau.com', 'hash_senha_1', 'medico'),
(2, 'Dra. Beatriz Santos', 'beatriz.santos@conexau.com', 'hash_senha_2', 'medico'),
(3, 'Dr. Carlos Oliveira', 'carlos.oliveira@conexau.com', 'hash_senha_3', 'medico'),
(4, 'Dra. Daniela Costa', 'daniela.costa@conexau.com', 'hash_senha_4', 'medico'),
(5, 'Dr. Eduardo Souza', 'eduardo.souza@conexau.com', 'hash_senha_5', 'enfermeiro'),
(6, 'Dra. Fernanda Lima', 'fernanda.lima@conexau.com', 'hash_senha_6', 'enfermeiro'),
(7, 'Dr. Gabriel Almeida', 'gabriel.almeida@conexau.com', 'hash_senha_7', 'enfermeiro'),
(8, 'Dra. Helena Ribeiro', 'helena.ribeiro@conexau.com', 'hash_senha_8', 'enfermeiro'),
(9, 'Dr. Igor Carvalho', 'igor.carvalho@conexau.com', 'hash_senha_9', 'tecnico_enfermagem'),
(10, 'Dra. Juliana Martins', 'juliana.martins@conexau.com', 'hash_senha_10', 'tecnico_enfermagem'),
(11, 'Dr. Lucas Ferreira', 'lucas.ferreira@conexau.com', 'hash_senha_11', 'tecnico_enfermagem'),
(12, 'Dra. Mariana Rocha', 'mariana.rocha@conexau.com', 'hash_senha_12', 'tecnico_enfermagem'),
(13, 'Dr. Nicolas Gomes', 'nicolas.gomes@conexau.com', 'hash_senha_13', 'tecnico_enfermagem'),
(14, 'Dra. Olivia Mendes', 'olivia.mendes@conexau.com', 'hash_senha_14', 'tecnico_enfermagem'),
(15, 'Dr. Pedro Rezende', 'pedro.rezende@conexau.com', 'hash_senha_15', 'tecnico_enfermagem'),
(16, 'Roberto Alencar', 'roberto.alencar@gmail.com', 'hash_senha_16', 'Paciente'),
(17, 'Sandra Regina', 'sandra.regina@gmail.com', 'hash_senha_17', 'Paciente'),
(18, 'Thiago Ramos', 'thiago.ramos@gmail.com', 'hash_senha_18', 'Paciente'),
(19, 'Vanessa Lopes', 'vanessa.lopes@gmail.com', 'hash_senha_19', 'Paciente'),
(20, 'William Pires', 'william.pires@gmail.com', 'hash_senha_20', 'Paciente'),
(21, 'Aline Guimarães', 'aline.guimaraes@gmail.com', 'hash_senha_21', 'Paciente'),
(22, 'Bruno Castro', 'bruno.castro@gmail.com', 'hash_senha_22', 'Paciente'),
(23, 'Camila Viana', 'camila.viana@gmail.com', 'hash_senha_23', 'Paciente'),
(24, 'Diego Antunes', 'diego.antunes@gmail.com', 'hash_senha_24', 'Paciente'),
(25, 'Eliane Soares', 'eliane.soares@gmail.com', 'hash_senha_25', 'Paciente'),
(26, 'Fábio Teixeira', 'fabio.teixeira@gmail.com', 'hash_senha_26', 'Paciente'),
(27, 'Giovana Dias', 'giovana.dias@gmail.com', 'hash_senha_27', 'Paciente'),
(28, 'Henrique Malta', 'henrique.malta@gmail.com', 'hash_senha_28', 'Paciente'),
(29, 'Isabela Frota', 'isabela.frota@gmail.com', 'hash_senha_29', 'Paciente'),
(30, 'Jorge Amado', 'jorge.amado@gmail.com', 'hash_senha_30', 'Paciente');

INSERT INTO tbl_profissional (id, especialidade, numero_registro, fk_usuario) VALUES
(1, 'Cardiologia', 'CRM/SP 123456', 1),
(2, 'Pediatria', 'CRM/RJ 234567', 2),
(3, 'Dermatologia', 'CRM/MG 345678', 3),
(4, 'Ortopedia', 'CRM/RS 456789', 4),
(5, 'Ginecologia', 'CRM/PR 567890', 5),
(6, 'Neurologia', 'CRM/SC 678901', 6),
(7, 'Psiquiatria', 'CRM/BA 789012', 7),
(8, 'Endocrinologia', 'CRM/PE 890123', 8),
(9, 'Oftalmologia', 'CRM/CE 901234', 9),
(10, 'Otorrinolaringologia', 'CRM/GO 012345', 10),
(11, 'Clínica Médica', 'CRM/DF 112233', 11),
(12, 'Urologia', 'CRM/ES 223344', 12),
(13, 'Gastrenterologia', 'CRM/AM 334455', 13),
(14, 'Infectologia', 'CRM/MT 445566', 14),
(15, 'Nefrologia', 'CRM/MS 556677', 15);

INSERT INTO tbl_paciente (id, contato_emergencia, data_nascimento, fk_usuario) VALUES
(1, 'Maria Alencar (Mãe) - (11) 99999-1111', '1985-04-12', 16),
(2, 'José Regina (Irmão) - (21) 99999-2222', '1990-08-23', 17),
(3, 'Carla Ramos (Esposa) - (31) 99999-3333', '1978-12-02', 18),
(4, 'Marcos Lopes (Pai) - (51) 99999-4444', '1995-01-15', 19),
(5, 'Patrícia Pires (Esposa) - (41) 99999-5555', '1963-06-30', 20),
(6, 'Fernanda Guimarães (Irmã) - (48) 99999-6666', '1989-10-10', 21),
(7, 'Letícia Castro (Mãe) - (71) 99999-7777', '2000-03-25', 22),
(8, 'Ricardo Viana (Pai) - (81) 99999-8888', '1992-07-14', 23),
(9, 'Juliana Antunes (Esposa) - (85) 99999-9999', '1982-11-21', 24),
(10, 'Arnaldo Soares (Filho) - (62) 99999-0000', '1955-09-05', 25),
(11, 'Sônia Teixeira (Mãe) - (61) 98888-1111', '1997-05-18', 26),
(12, 'Lucas Dias (Marido) - (27) 98888-2222', '1988-02-28', 27),
(13, 'Amanda Malta (Irmã) - (92) 98888-3333', '1974-10-09', 28),
(14, 'Renato Frota (Pai) - (65) 98888-4444', '2002-12-12', 29),
(15, 'Teresa Amado (Mãe) - (67) 98888-5555', '1967-01-01', 30);

INSERT INTO tbl_agendamento (data_agendamento, horario_inicio, horario_fim, status, fk_profissional, fk_paciente) VALUES
('2026-07-06', '08:00:00', '08:30:00', 'concluido', 1, 1),
('2026-07-06', '09:00:00', '09:30:00', 'concluido', 2, 2),
('2026-07-06', '10:00:00', '10:30:00', 'concluido', 3, 3),
('2026-07-06', '11:00:00', '11:30:00', 'cancelado', 4, 4),
('2026-07-06', '14:00:00', '14:30:00', 'concluido', 5, 5),
('2026-07-07', '08:30:00', '09:00:00', 'concluido', 6, 6),
('2026-07-07', '09:30:00', '10:00:00', 'concluido', 7, 7),
('2026-07-07', '10:30:00', '11:00:00', 'concluido', 8, 8),
('2026-07-07', '14:30:00', '15:00:00', 'cancelado', 9, 9),
('2026-07-07', '15:30:00', '16:00:00', 'concluido', 10, 10),
('2026-07-08', '08:00:00', '08:45:00', 'concluido', 11, 11),
('2026-07-08', '09:00:00', '09:45:00', 'concluido', 12, 12),
('2026-07-08', '10:00:00', '10:45:00', 'concluido', 13, 13),
('2026-07-08', '11:00:00', '11:45:00', 'concluido', 14, 14),
('2026-07-08', '14:00:00', '14:45:00', 'concluido', 15, 15),
('2026-07-09', '08:00:00', '08:30:00', 'agendado', 1, 2),
('2026-07-09', '09:00:00', '09:30:00', 'agendado', 2, 3),
('2026-07-09', '10:00:00', '10:30:00', 'agendado', 3, 4),
('2026-07-09', '11:00:00', '11:30:00', 'agendado', 4, 5),
('2026-07-09', '14:00:00', '14:30:00', 'agendado', 5, 6),
('2026-07-10', '08:30:00', '09:00:00', 'agendado', 6, 7),
('2026-07-10', '09:30:00', '10:00:00', 'agendado', 7, 8),
('2026-07-10', '10:30:00', '11:00:00', 'agendado', 8, 9),
('2026-07-10', '14:30:00', '15:00:00', 'agendado', 9, 10),
('2026-07-10', '15:30:00', '16:00:00', 'agendado', 10, 11),
('2026-07-13', '08:00:00', '08:45:00', 'agendado', 11, 12),
('2026-07-13', '09:00:00', '09:45:00', 'agendado', 12, 13),
('2026-07-13', '10:00:00', '10:45:00', 'agendado', 13, 14),
('2026-07-13', '11:00:00', '11:45:00', 'agendado', 14, 15),
('2026-07-13', '14:00:00', '14:45:00', 'agendado', 15, 1);

INSERT INTO tbl_triagem (nome_solicitante, status, fk_paciente) VALUES
('Pronto Socorro Central', 'aprovada', 1),
('Ambulatório de Especialidades', 'aprovada', 2),
('Unidade Básica de Saúde', 'aprovada', 3),
('Próprio Paciente', 'recusada', 4),
('UPA Norte', 'aprovada', 5),
('Próprio Paciente', 'aprovada', 6),
('Clínica da Família', 'aprovada', 7),
('Pronto Socorro Central', 'aprovada', 8),
('UPA Sul', 'recusada', 9),
('Ambulatório de Especialidades', 'aprovada', 10),
('Unidade Básica de Saúde', 'aprovada', 11),
('Próprio Paciente', 'aprovada', 12),
('UPA Oeste', 'aprovada', 13),
('Clínica da Família', 'aprovada', 14),
('Pronto Socorro Central', 'aprovada', 15),
('Próprio Paciente', 'pendente', 1),
('Unidade Básica de Saúde', 'pendente', 2),
('UPA Norte', 'pendente', 3),
('Próprio Paciente', 'pendente', 4),
('Clínica da Família', 'pendente', 5),
('Ambulatório de Especialidades', 'pendente', 6),
('Pronto Socorro Central', 'pendente', 7),
('UPA Sul', 'pendente', 8),
('Próprio Paciente', 'pendente', 9),
('Unidade Básica de Saúde', 'pendente', 10),
('UPA Oeste', 'pendente', 11),
('Clínica da Família', 'pendente', 12),
('Pronto Socorro Central', 'pendente', 13),
('Ambulatório de Especialidades', 'pendente', 14),
('Próprio Paciente', 'pendente', 15);

INSERT INTO tbl_visita (horario_inicio, horario_fim, status, fk_profissional, fk_paciente) VALUES
('2026-07-06 08:00:00', '2026-07-06 08:30:00', 'concluido', 1, 1),
('2026-07-06 09:00:00', '2026-07-06 09:30:00', 'concluido', 2, 2),
('2026-07-06 10:00:00', '2026-07-06 10:30:00', 'concluido', 3, 3),
('2026-07-06 11:00:00', '2026-07-06 11:30:00', 'cancelado', 4, 4),
('2026-07-06 14:00:00', '2026-07-06 14:30:00', 'concluido', 5, 5),
('2026-07-07 08:30:00', '2026-07-07 09:00:00', 'concluido', 6, 6),
('2026-07-07 09:30:00', '2026-07-07 10:00:00', 'concluido', 7, 7),
('2026-07-07 10:30:00', '2026-07-07 11:00:00', 'concluido', 8, 8),
('2026-07-07 14:30:00', '2026-07-07 15:00:00', 'cancelado', 9, 9),
('2026-07-07 15:30:00', '2026-07-07 16:00:00', 'concluido', 10, 10),
('2026-07-08 08:00:00', '2026-07-08 08:45:00', 'concluido', 11, 11),
('2026-07-08 09:00:00', '2026-07-08 09:45:00', 'concluido', 12, 12),
('2026-07-08 10:00:00', '2026-07-08 10:45:00', 'concluido', 13, 13),
('2026-07-08 11:00:00', '2026-07-08 11:45:00', 'concluido', 14, 14),
('2026-07-08 14:00:00', '2026-07-08 14:45:00', 'concluido', 15, 15),
('2026-07-09 08:00:00', '2026-07-09 08:30:00', 'agendado', 1, 2),
('2026-07-09 09:00:00', '2026-07-09 09:30:00', 'agendado', 2, 3),
('2026-07-09 10:00:00', '2026-07-09 10:30:00', 'agendado', 3, 4),
('2026-07-09 11:00:00', '2026-07-09 11:30:00', 'agendado', 4, 5),
('2026-07-09 14:00:00', '2026-07-09 14:30:00', 'agendado', 5, 6),
('2026-07-10 08:30:00', '2026-07-10 09:00:00', 'agendado', 6, 7),
('2026-07-10 09:30:00', '2026-07-10 10:00:00', 'agendado', 7, 8),
('2026-07-10 10:30:00', '2026-07-10 11:00:00', 'agendado', 8, 9),
('2026-07-10 14:30:00', '2026-07-10 15:00:00', 'agendado', 9, 10),
('2026-07-10 15:30:00', '2026-07-10 16:00:00', 'agendado', 10, 11),
('2026-07-13 08:00:00', '2026-07-13 08:45:00', 'agendado', 11, 12),
('2026-07-13 09:00:00', '2026-07-13 09:45:00', 'agendado', 12, 13),
('2026-07-13 10:00:00', '2026-07-13 10:45:00', 'agendado', 13, 14),
('2026-07-13 11:00:00', '2026-07-13 11:45:00', 'agendado', 14, 15),
('2026-07-13 14:00:00', '2026-07-13 14:45:00', 'agendado', 15, 1);

-- Dados de Protocolos Médicos
INSERT INTO tbl_protocolos (id, nome, descricao, categoria, ativo) VALUES
(1, 'Investigação Diagnóstica', 'Protocolo genérico para casos onde o diagnóstico ainda não é conhecido', 'investigacao', TRUE),
(2, 'Dengue', 'Protocolo para suspeita de dengue e outras arboviroses', 'agudo', TRUE),
(3, 'Diabetes Tipo 2', 'Protocolo para acompanhamento de diabetes mellitus tipo 2', 'cronico', TRUE),
(4, 'Hipertensão Arterial', 'Protocolo para acompanhamento de hipertensão arterial sistêmica', 'cronico', TRUE),
(5, 'Infarto Agudo do Miocárdio', 'Protocolo para suspeita de infarto agudo do miocárdio', 'agudo', TRUE),
(6, 'AVC (Acidente Vascular Cerebral)', 'Protocolo para suspeita de acidente vascular cerebral', 'agudo', TRUE),
(7, 'Asma', 'Protocolo para crise asmática e acompanhamento de asma', 'cronico', TRUE),
(8, 'COVID-19', 'Protocolo para suspeita de infecção por COVID-19', 'agudo', TRUE);

-- Perguntas do Protocolo de Investigação Diagnóstica
INSERT INTO tbl_perguntas_protocolo (fk_protocolo, ordem, tipo, pergunta, placeholder, obrigatorio) VALUES
(1, 1, 'textarea', 'Descreva detalhadamente os sintomas que motivaram a consulta', 'Ex: dor de cabeça, febre, cansaço...', TRUE),
(1, 2, 'texto', 'Há quanto tempo os sintomas começaram?', 'Ex: 2 dias, 1 semana...', TRUE),
(1, 3, 'select', 'Os sintomas são contínuos ou intermitentes?', NULL, TRUE),
(1, 4, 'textarea', 'Já fez algum tratamento para estes sintomas?', 'Descreva medicamentos ou procedimentos...', FALSE),
(1, 5, 'textarea', 'Possui algum histórico familiar de doenças similares?', 'Descreva histórico familiar...', FALSE);

-- Opções para pergunta 3 do protocolo de investigação
UPDATE tbl_perguntas_protocolo SET opcoes = '["Contínuos", "Intermitentes", "Variável"]' WHERE id = 3;

-- Perguntas do Protocolo de Dengue
INSERT INTO tbl_perguntas_protocolo (fk_protocolo, ordem, tipo, pergunta, placeholder, obrigatorio) VALUES
(2, 1, 'texto', 'Tem febre há quantos dias?', 'Ex: 2 dias, 5 dias...', TRUE),
(2, 2, 'select', 'Apresenta dor atrás dos olhos?', NULL, TRUE),
(2, 3, 'select', 'Apresenta dor muscular ou nas articulações?', NULL, TRUE),
(2, 4, 'select', 'Apresenta manchas vermelhas na pele?', NULL, TRUE),
(2, 5, 'select', 'Apresenta náuseas ou vômitos?', NULL, FALSE),
(2, 6, 'numero', 'Qual a temperatura máxima medida?', 'Ex: 38.5', TRUE),
(2, 7, 'textarea', 'Descreva outros sintomas presentes', 'Ex: dor abdominal, sangramentos...', FALSE);

-- Opções para perguntas do protocolo de dengue
UPDATE tbl_perguntas_protocolo SET opcoes = '["Sim", "Não"]' WHERE id IN (7, 8, 9, 10);

-- Perguntas do Protocolo de Diabetes Tipo 2
INSERT INTO tbl_perguntas_protocolo (fk_protocolo, ordem, tipo, pergunta, placeholder, obrigatorio) VALUES
(3, 1, 'numero', 'Qual a glicemia de jejum mais recente?', 'Ex: 110, 140...', TRUE),
(3, 2, 'numero', 'Qual a hemoglobina glicada (HbA1c) mais recente?', 'Ex: 6.5, 7.2...', TRUE),
(3, 3, 'select', 'Está em uso de medicamentos para diabetes?', NULL, TRUE),
(3, 4, 'textarea', 'Quais medicamentos está utilizando?', 'Liste os medicamentos e dosagens...', FALSE),
(3, 5, 'select', 'Apresenta sintomas de hipoglicemia?', NULL, FALSE),
(3, 6, 'textarea', 'Descreva a dieta atual', 'Ex: restrita em carboidratos, balanceada...', FALSE);

-- Opções para perguntas do protocolo de diabetes
UPDATE tbl_perguntas_protocolo SET opcoes = '["Sim", "Não"]' WHERE id IN (17, 19);

-- Perguntas do Protocolo de Hipertensão Arterial
INSERT INTO tbl_perguntas_protocolo (fk_protocolo, ordem, tipo, pergunta, placeholder, obrigatorio) VALUES
(4, 1, 'texto', 'Qual a pressão arterial mais recente medida?', 'Ex: 140/90, 130/80...', TRUE),
(4, 2, 'select', 'Está em uso de anti-hipertensivos?', NULL, TRUE),
(4, 3, 'textarea', 'Quais anti-hipertensivos está utilizando?', 'Liste os medicamentos...', FALSE),
(4, 4, 'select', 'Apresenta tonturas ou dores de cabeça frequentes?', NULL, FALSE),
(4, 5, 'select', 'Pratica atividade física regularmente?', NULL, FALSE),
(4, 6, 'textarea', 'Descreva hábitos alimentares atuais', 'Ex: sal em excesso, frituras...', FALSE);

-- Opções para perguntas do protocolo de hipertensão
UPDATE tbl_perguntas_protocolo SET opcoes = '["Sim", "Não"]' WHERE id IN (23, 25, 26);

-- Perguntas do Protocolo de Infarto Agudo do Miocárdio
INSERT INTO tbl_perguntas_protocolo (fk_protocolo, ordem, tipo, pergunta, placeholder, obrigatorio) VALUES
(5, 1, 'select', 'Apresenta dor no peito?', NULL, TRUE),
(5, 2, 'select', 'A dor irradia para braço esquerdo, pescoço ou mandíbula?', NULL, TRUE),
(5, 3, 'select', 'Apresenta falta de ar ou dificuldade respiratória?', NULL, TRUE),
(5, 4, 'select', 'Apresenta sudorese fria ou palidez?', NULL, TRUE),
(5, 5, 'select', 'Possui histórico de doenças cardíacas?', NULL, FALSE),
(5, 6, 'textarea', 'Descreva outros sintomas associados', 'Ex: náuseas, tontura, ansiedade...', FALSE);

-- Opções para perguntas do protocolo de infarto
UPDATE tbl_perguntas_protocolo SET opcoes = '["Sim", "Não"]' WHERE id IN (28, 29, 30, 31, 32);

-- Perguntas do Protocolo de AVC
INSERT INTO tbl_perguntas_protocolo (fk_protocolo, ordem, tipo, pergunta, placeholder, obrigatorio) VALUES
(6, 1, 'select', 'Apresenta fraqueza ou paralisia em um lado do corpo?', NULL, TRUE),
(6, 2, 'select', 'Apresenta dificuldade para falar ou entender fala?', NULL, TRUE),
(6, 3, 'select', 'Apresenta alteração visual súbita?', NULL, TRUE),
(6, 4, 'select', 'Apresenta tontura severa ou perda de equilíbrio?', NULL, TRUE),
(6, 5, 'select', 'Apresenta dor de cabeça intensa e súbita?', NULL, TRUE),
(6, 6, 'textarea', 'Descreva quando os sintomas começaram', 'Ex: há 30 minutos, há 2 horas...', TRUE);

-- Opções para perguntas do protocolo de AVC
UPDATE tbl_perguntas_protocolo SET opcoes = '["Sim", "Não"]' WHERE id IN (34, 35, 36, 37, 38);

-- Perguntas do Protocolo de Asma
INSERT INTO tbl_perguntas_protocolo (fk_protocolo, ordem, tipo, pergunta, placeholder, obrigatorio) VALUES
(7, 1, 'select', 'Apresenta chiado no peito (sibilância)?', NULL, TRUE),
(7, 2, 'select', 'Apresenta falta de ar ou dispneia?', NULL, TRUE),
(7, 3, 'select', 'Apresenta tosse seca ou com expectoração?', NULL, TRUE),
(7, 4, 'select', 'Está em uso de bombinha (broncodilatador)?', NULL, TRUE),
(7, 5, 'select', 'Quantas crises asmáticas teve no último mês?', NULL, FALSE),
(7, 6, 'textarea', 'Descreva fatores desencadeantes conhecidos', 'Ex: poeira, frio, exercício...', FALSE);

-- Opções para perguntas do protocolo de asma
UPDATE tbl_perguntas_protocolo SET opcoes = '["Sim", "Não"]' WHERE id IN (40, 41, 42, 43);
UPDATE tbl_perguntas_protocolo SET opcoes = '["Nenhuma", "1-2 crises", "3-5 crises", "Mais de 5 crises"]' WHERE id = 44;

-- Perguntas do Protocolo de COVID-19
INSERT INTO tbl_perguntas_protocolo (fk_protocolo, ordem, tipo, pergunta, placeholder, obrigatorio) VALUES
(8, 1, 'select', 'Apresenta febre?', NULL, TRUE),
(8, 2, 'select', 'Apresenta tosse seca?', NULL, TRUE),
(8, 3, 'select', 'Apresenta perda de olfato ou paladar?', NULL, TRUE),
(8, 4, 'select', 'Apresenta dificuldade respiratória?', NULL, TRUE),
(8, 5, 'select', 'Teve contato recente com caso confirmado de COVID-19?', NULL, TRUE),
(8, 6, 'textarea', 'Descreva outros sintomas presentes', 'Ex: dor de garganta, dor muscular, diarreia...', FALSE);

-- Opções para perguntas do protocolo de COVID-19
UPDATE tbl_perguntas_protocolo SET opcoes = '["Sim", "Não"]' WHERE id IN (46, 47, 48, 49, 50);