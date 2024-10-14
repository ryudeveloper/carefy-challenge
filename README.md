# Carefy Challenge - Censo Hospitalar

Este projeto é uma aplicação para o cadastro e gerenciamento de dados de censo hospitalar. Ele permite o upload de arquivos CSV, a revisão dos dados carregados e a persistência dos dados no banco de dados.

## Funcionalidades

- **Upload do CSV** Permite que o usuário envie um arquivo CSV com os dados do censo hospitalar.
- **Revisão de Dados:** Exibe os dados válidos e inválidos para o usuário revisar antes de salvá-los no banco de dados.
- **Lista de Pacientes:** Exibe uma lista de todos os pacientes cadastrados e suas internações.
- **Notificação de Novos Pacientes e Internações:** Informa ao usuário a quantidade de novos pacientes e internações cadastradas após a confirmação do upload.

## Rotas da aplicação

- **url/Upload:** Permite que o usuário envie um arquivo CSV com os dados do censo hospitalar.
- **url/Review:** Exibe os dados válidos e inválidos para o usuário revisar antes de salvá-los no banco de dados.
- **url/Patients:** Exibe uma lista de todos os pacientes cadastrados e suas internações.

## Pré-requisitos

Antes de começar, você precisará ter instalado em sua máquina:

- [PHP](https://www.php.net/downloads) (>= 8.0)
- [Composer](https://getcomposer.org/download/)
- [MySQL](https://www.mysql.com/downloads/)

## Configuração do Ambiente

1. **Clone o repositório:**

   ```bash
   git clone https://github.com/ryudeveloper/carefy-challenge.git
   cd carefy-challenge

2. **Instale as dependências do PHP:**

   ```bash
   composer install

3. **Crie um arquivo .env a partir do arquivo .env.example:**

   ```bash
   cp .env.example .env

4. **Gere a chave da aplicação:**

   ```bash
   php artisan key:generate

5. **Configure seu banco de dados no arquivo .env:**

    **Altere as seguintes linhas no seu .env com as configurações do seu banco de dados:**
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nome_do_banco_de_dados
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha

5. **Execute as migrações do banco de dados:**
   ```bash
   php artisan migrate

6. **Inicie o servidor do Laravel:**
   ```bash
    php artisan serve
    Isso iniciará o servidor em http://localhost:8000.

7. **Acesse a aplicação no navegador:**
   ```bash
    Abra seu navegador e entre na URL http://localhost:8000 para acessar a aplicação.

## Estrutura do Projeto
 **app/: Contém a lógica do aplicativo.**

 **resources/views/: Contém as views Blade do Laravel.**
