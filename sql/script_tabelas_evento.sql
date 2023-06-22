-- Tabela para armazenar os estados
CREATE TABLE IF NOT EXISTS uf(
  cd_uf SERIAL      NOT NULL,
  ds_uf VARCHAR(50) NOT NULL,
  ds_sigla VARCHAR(2) NOT NULL,
  CONSTRAINT pf_cd_uf PRIMARY KEY (cd_uf)
);

-- Tabela cidade
CREATE TABLE IF NOT EXISTS cidade(
  cd_cidade SERIAL      NOT NULL,
  nm_cidade VARCHAR(60) NOT NULL,
  cd_uf     SMALLINT    NOT NULL;
  CONSTRAINT pk_cd_cidade PRIMARY KEY (cd_cidade),
  CONSTRAINT fk_cd_uf 	  FOREIGN KEY (cd_uf) REFERENCES uf (cd_uf)
);

-- Tabela evento
CREATE TABLE IF NOT EXISTS evento(
  cd_evento SERIAL    NOT NULL,
  dt_evento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nm_evento VARCHAR(100),
  cd_cidade INTEGER NOT NULL,
  CONSTRAINT pk_cd_evento PRIMARY KEY (cd_evento),
  CONSTRAINT fk_cd_cidade FOREIGN KEY (cd_cidade) REFERENCES cidade (cd_cidade)
);

-- Tabela modalidade
CREATE TABLE IF NOT EXISTS modalidade(
  cd_modalidade   SERIAL        NOT NULL,
  ds_descricao    VARCHAR(100)  NOT NULL,
  vl_km_distancia INTEGER       DEFAULT 5,
  dt_largada      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  vl_valor        NUMERIC(15,2) NOT NULL,
  CONSTRAINT pk_cd_modalidade PRIMARY KEY (cd_modalidade)
);

-- Tabela Tipo de Pessoa
CREATE TABLE IF NOT EXISTS tipo_pessoa(
  cd_id_tipo	SERIAL NOT NULL,
  nm_tipo	VARCHAR(30) NOT NULL,
  CONSTRAINT pk_cd_id_tipo PRIMARY KEY (cd_id_tipo)
);

-- Tabela pessoa
CREATE TABLE IF NOT EXISTS pessoa (
  cd_pessoa     SERIAL      NOT NULL,
  nm_pessoa     VARCHAR(60) NOT NULL,
  nr_telefone   CHAR(11)    NOT NULL,
  dt_nascimento DATE,
  ds_sexo       VARCHAR(1)  CHECK (ds_sexo IN ('F', 'M')),
  cd_cidade     INTEGER     NOT NULL,
  ds_fator_rh   VARCHAR(10),
  cd_id_tipo	INTEGER	    NOT NULL,
  ds_email	VARCHAR(50) NOT NULL UNIQUE,
  ds_senha	VARCHAR(50) NOT NULL,
  CONSTRAINT pk_cd_pessoa     PRIMARY KEY (cd_pessoa),
  CONSTRAINT fk_pessoa_cidade FOREIGN KEY (cd_cidade)  REFERENCES cidade (cd_cidade),
  CONSTRAINT fk_cd_id_tipo    FOREIGN KEY (cd_id_tipo) REFERENCES tipo_pessoa (cd_id_tipo)
);
	
-- Tabela inscricoes
CREATE TABLE IF NOT EXISTS inscricao(
  cd_inscricao  SERIAL    NOT NULL,
  dt_inscricao  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_status     SMALLINT  NOT NULL CHECK (id_status IN (0, 1, 2)),
  ds_contato    VARCHAR(100),
  ds_tempo      TIME,
  ds_equipe     VARCHAR(50),
  cd_modalidade INTEGER   NOT NULL,
  cd_pessoa     INTEGER   NOT NULL,
  cd_evento	INTEGER   NOT NULL,
  CONSTRAINT pk_cd_inscricao            PRIMARY KEY (cd_inscricao),
  CONSTRAINT fk_cd_modalidade_inscricao FOREIGN KEY (cd_modalidade) REFERENCES modalidade (cd_modalidade),
  CONSTRAINT fk_cd_pessoa_inscricao     FOREIGN KEY (cd_pessoa)     REFERENCES pessoa     (cd_pessoa),
  CONSTRAINT fk_cd_evento_inscricao	FOREIGN KEY (cd_evento)	    REFERENCES evento	  (cd_evento)
);

-- Tabela modalidade_evento
CREATE TABLE IF NOT EXISTS modalidade_evento(
  cd_evento_modalidade SERIAL NOT NULL,
  cd_modalidade INTEGER NOT NULL,
  cd_evento     INTEGER NOT NULL,
  CONSTRAINT pk_cd_evento_modalidade 		PRIMARY KEY (cd_evento_modalidade),
  CONSTRAINT fk_cd_modalidade_evento_modalidade FOREIGN KEY (cd_modalidade) REFERENCES modalidade (cd_modalidade),
  CONSTRAINT fk_cd_modalidade_evento_evento     FOREIGN KEY (cd_evento)     REFERENCES evento     (cd_evento)
);

-- Tabela tipo pessoa tipo (Para validar o tipo de pessoa no login
CREATE TABLE IF NOT EXISTS tipo_pessoa_tipo(
  cd_id_tipo	INTEGER,
  cd_pessoa	INTEGER,
  CONSTRAINT fk_cd_id_tipo FOREIGN KEY (cd_id_tipo) REFERENCES tipo_pessoa (cd_id_tipo),
  CONSTRAINT fk_cd_pessoa  FOREIGN KEY (cd_pessoa)  REFERENCES pessoa 	   (cd_pessoa)
);
