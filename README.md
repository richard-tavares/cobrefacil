
# Cobrefacil API - Personal Expense Manager

Este projeto é uma API RESTful desenvolvida como parte de um desafio técnico para gerenciar despesas pessoais, utilizando autenticação JWT e arquitetura baseada em boas práticas como SOLID, camadas de serviço e validações robustas. A aplicação foi construída com foco em segurança, performance, consistência e padronização de respostas.

---

## 🚀 Tecnologias Utilizadas

- **PHP 8.3**
- **Yii2 Framework**
- **MySQL**
- **Docker**
- **Nginx**
- **JWT Authentication**
- **Codeception (Testes Automatizados)**

---

## 🔐 Autenticação

A autenticação é feita via JWT (JSON Web Token). Após o login, o usuário recebe um token que deve ser enviado no header de todas as requisições protegidas:

```
Authorization: Bearer SEU_TOKEN_AQUI
```

---

## 🧪 Testes

Todos os endpoints possuem cobertura de testes automatizados utilizando **Codeception**, garantindo a confiabilidade da aplicação. Os testes podem ser executados com:

```bash
codecept run
```

### 🔄 Importar no Postman

Você também pode importar o arquivo abaixo no [Postman](https://www.postman.com):

- [`cobrefacil.postman_collection.json`](cobrefacil.postman_collection.json)

---

## 📂 Endpoints da API

### 🧍 Usuário

#### POST `/user/register`

Registra um novo usuário.

**Payload:**
```json
{
  "email": "john.doe@example.com",
  "password": "123456"
}
```

**Respostas:**
- `201 Created`: Usuário cadastrado
- `400 Bad Request`: Erro de validação

---

#### POST `/user/login`

Realiza o login e retorna o token JWT.

**Payload:**
```json
{
  "email": "john.doe@example.com",
  "password": "123456"
}
```

**Respostas:**
- `200 OK`: Login realizado
- `401 Unauthorized`: Credenciais inválidas

---

### 💸 Despesas

#### GET `/expense`

Lista todas as despesas do usuário autenticado, com filtros opcionais por `date` e `category`.

**Query Params:**
- `date` (YYYY-MM-DD)
- `category` (alimentação, transporte, lazer)

**Respostas:**
- `200 OK`
- `401 Unauthorized`

---

#### GET `/expense/{id}`

Retorna uma única despesa pelo ID.

**Respostas:**
- `200 OK`
- `401 Unauthorized`
- `404 Not Found`

---

#### POST `/expense`

Cria uma nova despesa.

**Payload:**
```json
{
  "description": "Cinema",
  "category": "lazer",
  "amount": 29.90,
  "date": "2025-06-02"
}
```

**Respostas:**
- `201 Created`
- `400 Bad Request`
- `401 Unauthorized`

---

#### PUT `/expense/{id}`

Atualiza uma despesa existente.

**Payload:**
```json
{
  "description": "Cinema - atualizado",
  "category": "lazer",
  "amount": 30.00,
  "date": "2025-06-02"
}
```

**Respostas:**
- `200 OK`
- `400 Bad Request`
- `401 Unauthorized`
- `404 Not Found`

---

#### DELETE `/expense/{id}`

Remove uma despesa (soft delete).

**Respostas:**
- `204 No Content`
- `401 Unauthorized`
- `404 Not Found`

---

## 📦 Como rodar

1. Clone o repositório
2. Suba os containers com Docker:
```bash
docker compose up -d
```
3. Acesse a aplicação em `http://localhost:8000`
4. Execute os testes com:
```bash
codecept run
```

---

## 🧑‍💻 Autor

Desenvolvido por [Richard Tavares](https://github.com/richard-tavares) como parte do processo seletivo da **Cobrefacil**.

---

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.
