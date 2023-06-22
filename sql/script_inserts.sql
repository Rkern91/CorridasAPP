--Estes dois inserts abaixo são os únicos necessários para conseguir usar 
--o projeto, o restante pode ser feito diretamente cadastrando nas respectivas telas.

-- INSERTs tabela uf
INSERT INTO uf (ds_uf, ds_sigla) VALUES ('RIO GRANDE DO SUL', 'RS');
INSERT INTO uf (ds_uf, ds_sigla) VALUES ('SANTA CATARINA',    'SC');
INSERT INTO uf (ds_uf, ds_sigla) VALUES ('PARANA',            'PR');

-- INSERTs dos tipos de pessoas
INSERT INTO tipo_pessoa (cd_id_tipo, nm_tipo) VALUES (1, 'adm');
INSERT INTO tipo_pessoa (cd_id_tipo, nm_tipo) VALUES (2, 'comum');


------------------------------------------------------------------------------------

-- INSERTs tabela cidade
INSERT INTO cidade (nm_cidade, cd_uf) VALUES('Soledade',  1);
INSERT INTO cidade (nm_cidade, cd_uf) VALUES('Lajeado',   1);
INSERT INTO cidade (nm_cidade, cd_uf) VALUES('Marau',     1);
INSERT INTO cidade (nm_cidade, cd_uf) VALUES('Carazinho', 1);
INSERT INTO cidade (nm_cidade, cd_uf) VALUES('Mormaço',   1);
INSERT INTO cidade (nm_cidade, cd_uf) VALUES('Criciuma',  2);
INSERT INTO cidade (nm_cidade, cd_uf) VALUES('Floripa',   2);
INSERT INTO cidade (nm_cidade, cd_uf) VALUES('Cascavel',  3);

-- INSERTs tabela modalidade
INSERT INTO modalidade (ds_descricao, vl_km_distancia, dt_largada, vl_valor) VALUES ('Corrida 5km',	5, 	'2023-01-01 08:00:00', 25.00);
INSERT INTO modalidade (ds_descricao, vl_km_distancia, dt_largada, vl_valor) VALUES ('Meia Maratona',	21, 	'2023-02-12 09:30:00', 50.00);
INSERT INTO modalidade (ds_descricao, vl_km_distancia, dt_largada, vl_valor) VALUES ('Corrida 10km',	10, 	'2023-03-05 07:45:00', 30.00);
INSERT INTO modalidade (ds_descricao, vl_km_distancia, dt_largada, vl_valor) VALUES ('Maratona', 	42,	'2023-04-23 06:00:00', 80.00);
INSERT INTO modalidade (ds_descricao, vl_km_distancia, dt_largada, vl_valor) VALUES ('Corrida 8km',	8, 	'2023-05-14 08:15:00', 20.00);

-- INSERTs tabela evento
INSERT INTO evento (dt_evento, nm_evento, cd_cidade) VALUES ('2023-01-01 08:00:00', 'Corrida da Virada',     1);
INSERT INTO evento (dt_evento, nm_evento, cd_cidade) VALUES ('2023-02-12 09:30:00', 'Meia Maratona do Rio',  2);
INSERT INTO evento (dt_evento, nm_evento, cd_cidade) VALUES ('2023-03-05 07:45:00', 'Corrida dos Amigos',    3);
INSERT INTO evento (dt_evento, nm_evento, cd_cidade) VALUES ('2023-04-23 06:00:00', 'Maratona de São Paulo', 4);
INSERT INTO evento (dt_evento, nm_evento, cd_cidade) VALUES ('2023-05-14 08:15:00', 'Corrida da Primavera',  5);


-- INSERTS tabela modalidade_evento (Se der erro nestes inserts, pode ignorar aqui estamos só 
-- ligando as modalidades aos eventos e isso pode ser feito na listagem de eventos manualmente)
INSERT INTO modalidade_evento (cd_modalidade, cd_evento) VALUES (1, 2);
INSERT INTO modalidade_evento (cd_modalidade, cd_evento) VALUES (3, 2);
INSERT INTO modalidade_evento (cd_modalidade, cd_evento) VALUES (4, 3);
INSERT INTO modalidade_evento (cd_modalidade, cd_evento) VALUES (2, 4);
INSERT INTO modalidade_evento (cd_modalidade, cd_evento) VALUES (5, 4);
INSERT INTO modalidade_evento (cd_modalidade, cd_evento) VALUES (5, 5);
INSERT INTO modalidade_evento (cd_modalidade, cd_evento) VALUES (5, 6);

-- INSERTs de usuários
INSERT INTO pessoa (nm_pessoa, nr_telefone, dt_nascimento, ds_sexo, cd_cidade, cd_id_tipo, ds_email, ds_senha)
   VALUES ('Rafael Kern', '54981028211', '1991-12-12', 'M', 28, 1, '156999@upf.br', '12345');


