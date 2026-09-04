# Investments API

A REST API built with Laravel for managing clients, accounts, and investment transactions. The application uses MySQL as the database.

## 1. Local Setup

### Requirements

Before running the project, make sure you have installed:

* PHP
* Composer
* MySQL
* phpMyAdmin (optional, for managing the MySQL database)

### Step 1: Clone the repository

```bash
git clone https://github.com/simona155/investments-api.git
cd investments-api
```

### Step 2: Install dependencies

```bash
composer install
```

### Step 3: Configure the environment

Create the `.env` file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the MySQL database in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=investments
DB_USERNAME=root
DB_PASSWORD=
```

Create a database named `investments` in MySQL or phpMyAdmin.

### Step 4: Run migrations and seed the database

```bash
php artisan migrate --seed
```

This creates the required database tables and inserts example clients, accounts, and transactions.

### Step 5: Run the tests

To run the full test suite, use:

```bash
php artisan test
```

The tests cover input validation, transaction operations, business rules, account balance, holdings, transaction history, and account isolation.

### Step 6: Start the Laravel server

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000
```

---

## 2. Communication with the System

The system communicates through REST API endpoints.

### Deposit

**Request:**

```http
POST /api/transactions
```

```json
{
    "account_id": 1,
    "type": "deposit",
    "amount": 1000
}
```

**Response:**

```json
{
    "account_id": 1,
    "type": "deposit",
    "amount": "1000.00",
    "instrument": null,
    "quantity": null,
    "price": null
}
```

### Withdrawal

**Request:**

```http
POST /api/transactions
```

```json
{
    "account_id": 1,
    "type": "withdrawal",
    "amount": 500
}
```

**Response:**

```json
{
    "account_id": 1,
    "type": "withdrawal",
    "amount": "500.00",
    "instrument": null,
    "quantity": null,
    "price": null
}
```

### Buy

**Request:**

```http
POST /api/transactions
```

```json
{
    "account_id": 1,
    "type": "buy",
    "instrument": "AAPL",
    "quantity": 2,
    "price": 100
}
```

**Response:**

```json
{
    "account_id": 1,
    "type": "buy",
    "amount": "200.00",
    "instrument": "AAPL",
    "quantity": 2,
    "price": "100.00"
}
```

The transaction amount is calculated by the system:

```text
quantity × price = amount
2 × 100 = 200
```

### Sell

**Request:**

```http
POST /api/transactions
```

```json
{
    "account_id": 1,
    "type": "sell",
    "instrument": "AAPL",
    "quantity": 1,
    "price": 150
}
```

**Response:**

```json
{
    "account_id": 1,
    "type": "sell",
    "amount": "150.00",
    "instrument": "AAPL",
    "quantity": 1,
    "price": "150.00"
}
```

The transaction amount is calculated by the system:

```text
quantity × price = amount
1 × 150 = 150
```

### Balance

**Request:**

```http
GET /api/accounts/1/balance
```

**Response:**

```json
{
    "balance": 1000
}
```

### Holdings

**Request:**

```http
GET /api/accounts/1/holdings
```

**Response:**

```json
{
    "holdings": {
        "AAPL": "10",
        "MSFT": "5"
    }
}
```

### Transaction History

**Request:**

```http
GET /api/accounts/1/transactions
```

**Response:**

```json
[
    {
        "id": 1,
        "account_id": 1,
        "type": "deposit",
        "amount": "5000.00",
        "instrument": null,
        "quantity": null,
        "price": null,
        "created_at": "..."
    },
    {
        "id": 2,
        "account_id": 1,
        "type": "buy",
        "amount": "2000.00",
        "instrument": "AAPL",
        "quantity": 10,
        "price": "200.00",
        "created_at": "..."
    }
]
```

---

## 3. Why This Way?

I chose Laravel with an MVC architecture and an additional Service Layer to divide the application into logical parts, with each part having its own responsibility. The Controllers are responsible for communication with the API, while the main business logic is separated into Service classes.

I placed input validation in `StoreTransactionRequest` so that validation is separated from the business logic. For balance and holdings, I use the transaction history as the source of truth instead of storing separate balance and holdings values.

Transactions are immutable, meaning that there is no possibility of editing or deleting them. This keeps the transaction history unchanged. I used database relationships between Client, Account, and Transaction, while the unique constraint on `client_id` ensures that each client can have only one account.

For business rules, I use a separate `BusinessRuleException`. This allows errors such as insufficient funds or insufficient units to be returned with a clear message and HTTP status `422`.
