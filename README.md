# Controle de Frota Ferroviária

CRUD de trens em PHP puro com MySQL, com leituras de sensores IoT simuladas e alerta quando algum valor passa do limite.

## Funcionalidades

- Cadastro, edição e exclusão de trens (prefixo, modelo, ano, capacidade e situação)
- Simulador que gera de 1 a 200 leituras para um trem (velocidade, temperatura do motor, consumo e vibração)
- Tela de leituras com filtro por trem e opção de mostrar só as leituras com alerta
- Limites de cada sensor definidos em `limites.php`; valores acima do limite ficam destacados

## Como rodar (XAMPP)

1. Inicie o Apache e o MySQL no painel do XAMPP.
2. No phpMyAdmin, importe o arquivo `create_db.sql` (cria o banco `db_ferrovia` com alguns trens de exemplo).
3. Copie a pasta `crud_trens` para `C:\xampp\htdocs\`.
4. Acesse `http://localhost/crud_trens`.
5. Vá em **Simulador** para gerar as primeiras leituras.

A conexão com o banco fica em `config.php` (usuário `root`, sem senha, host `localhost`).

## Autor

Guilherme Wohl
