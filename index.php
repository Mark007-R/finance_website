<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finase</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;700&display=swap" rel="stylesheet">
  <style>
    /* Global */

    :root {
      --dark-blue: #363F5F;
      --green: #49aa26;
      --light-green: #3dd705;
      --red: #e92929;
      --white: #ffffff;
      --background: #f0f2f5;
      --text-color: #969cb3;
      --card-bg: #ffffff;
      --card-border-radius: 0.25rem;
      --font-family: 'Poppins', sans-serif;
      --font-size-base: 15px;
      /* Base font size for 100% */
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      font-size: 93.75%;
      /* 15px */
    }

    body {
      background: var(--background);
      font-family: var(--font-family);
      color: var(--dark-blue);
    }

    /* Visually hidden */
    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border-width: 0;
    }

    /* Container */
    .container {
      width: min(90vw, 800px);
      margin: auto;
    }

    /* Titles */
    h2 {
      margin-top: 3.2rem;
      margin-bottom: 0.8rem;
      color: var(--dark-blue);
      font-weight: normal;
    }

    /* Links and buttons */
    a {
      color: var(--green);
      text-decoration: none;
    }

    a:hover {
      color: var(--light-green);
    }

    button {
      width: 100%;
      height: 50px;
      border: none;
      color: var(--white);
      background: var(--green);
      padding: 0;
      border-radius: var(--card-border-radius);
      cursor: pointer;
    }

    button:hover {
      background: var(--light-green);
    }

    .button.new {
      display: inline-block;
      margin-bottom: 0.8rem;
    }

    .button.cancel {
      color: var(--red);
      border: 2px var(--red) solid;
      border-radius: var(--card-border-radius);
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0.6;
    }

    .button.cancel:hover {
      opacity: 1;
    }

    /* Header */
    header {
      background: #2D4A22;
      padding: 2rem 0 10rem;
      text-align: center;
    }

    #logo {
      color: var(--white);
      font-weight: 100;
    }

    /* Balance */
    #balance {
      margin-top: -8rem;
    }

    #balance h2 {
      color: var(--white);
      margin-top: 0;
    }

    /* Cards */
    .card {
      background: var(--card-bg);
      padding: 1.5rem 2rem;
      border-radius: var(--card-border-radius);
      margin-bottom: 2rem;
      color: var(--dark-blue);
    }

    .card h3 {
      font-weight: normal;
      font-size: 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .card p {
      font-weight: normal;
      font-size: 2rem;
      line-height: 3rem;
      margin-top: 1rem;
    }

    .card.total {
      background: var(--green);
      color: var(--white);
    }

    /* Table */
    #transaction {
      display: block;
      width: 100%;
      overflow-x: auto;
    }

    #data-table {
      width: 100%;
      border-spacing: 0rem 0.5rem;
      color: var(--text-color);
    }

    table thead tr th:first-child,
    table thead tr td:first-child {
      border-radius: var(--card-border-radius) 0 0 var(--card-border-radius);
    }

    table thead tr th:last-child,
    table thead tr td:last-child {
      border-radius: 0 var(--card-border-radius) var(--card-border-radius) 0;
    }

    table thead th {
      background: #f5f5f5;
      font-weight: normal;
      padding: 1rem 2rem;
      color: var(--text-color);
      text-align: left;
    }

    table tbody tr {
      opacity: 0.7;
    }

    table tbody tr:hover {
      opacity: 1;
    }

    table tbody td {
      background: var(--card-bg);
      padding: 1rem 2rem;
      font-weight: normal;
    }

    td.description {
      color: var(--dark-blue);
    }

    td.income {
      color: #12a454;
    }

    td.expense {
      color: var(--red);
    }

    /* Modal */
    .modal-overlay {
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      position: fixed;
      top: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      z-index: 999;
      transition: opacity 0.3s, visibility 0.3s;
    }

    .modal-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .modal {
      background: var(--background);
      padding: 2.4rem;
      position: relative;
      width: 90%;
      max-width: 500px;
      z-index: 1;
    }

    /* Form */
    #form {
      max-width: 500px;
    }

    #form h2 {
      margin-top: 0;
    }

    #form form input {
      border: none;
      border-radius: 0.2rem;
      padding: 0.8rem;
      width: 100%;
    }

    .input-group {
      margin-top: 0.8rem;
    }

    .input-group .help {
      opacity: 0.4;
    }

    .input-group.actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .input-group.actions .button,
    .input-group.actions button {
      width: 48%;
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 4rem 0 2rem;
      color: var(--dark-blue);
      opacity: 0.6;
    }

    /* Responsive */
    @media (min-width: 800px) {
      html {
        font-size: 87.5%;
        /* Adjust for larger screens */
      }

      #balance {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
      }
    }
  </style>
</head>

<body>
  <header>
    <img src="./assets/logo.svg" alt="Dev Finance Logo" aria-label="Dev Finance Logo">
  </header>

  <main class="container">
    <section id="balance">
      <h2 class="sr-only">Balance Overview</h2>

      <div class="card">
        <h3>
          <span>Income</span>
          <img src="./assets/income.svg" alt="Income icon">
        </h3>
        <p id="incomeDisplay">R$ 0.00</p>
      </div>

      <div class="card">
        <h3>
          <span>Expenses</span>
          <img src="./assets/expense.svg" alt="Expenses icon">
        </h3>
        <p id="expenseDisplay">R$ 0.00</p>
      </div>

      <div class="card total">
        <h3>
          <span>Total</span>
          <img src="./assets/total.svg" alt="Total icon">
        </h3>
        <p id="totalDisplay">R$ 0.00</p>
      </div>
    </section>

    <section id="transaction">
      <h2 class="sr-only">Transaction History</h2>

      <a href="#" onclick="Modal.open()" class="button new">+ New Transaction</a>

      <table id="data-table">
        <thead>
          <tr>
            <th>Description</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Transaction rows will be added here dynamically -->
        </tbody>
      </table>
    </section>
  </main>

  <div class="modal-overlay active">
    <div class="modal">
      <div id="form">
        <h2>New Transaction</h2>
        <form action="" onsubmit="Form.submit(event)">
          <div class="input-group">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" placeholder="Description" required>
          </div>

          <div class="input-group">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" id="amount" name="amount" placeholder="0.00" required>
            <small class="help">Use a negative sign (-) for expenses and a comma (,) for decimal places.</small>
          </div>

          <div class="input-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" required>
          </div>

          <div class="input-group actions">
            <a href="#" class="button cancel" onclick="Modal.close()">Cancel</a>
            <button type="submit">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2024 Finase</p>
  </footer>

  <script>
    // Modal functionality to handle opening and closing of the modal
    const Modal = {
      open() {
        document.querySelector('.modal-overlay').classList.add('active');
      },
      close() {
        document.querySelector('.modal-overlay').classList.remove('active');
      }
    };

    // Storage functionality to handle saving and retrieving data from localStorage
    const Storage = {
      get() {
        return JSON.parse(localStorage.getItem("dev.finances:transactions")) || [];
      },
      set(transactions) {
        localStorage.setItem("dev.finances:transactions", JSON.stringify(transactions));
      }
    };

    // Transaction management
    const Transaction = {
      all: Storage.get(),

      add(transaction) {
        Transaction.all.push(transaction);
        App.reload();
      },

      remove(index) {
        Transaction.all.splice(index, 1);
        App.reload();
      },

      incomes() {
        return Transaction.all.reduce((income, transaction) =>
          transaction.amount > 0 ? income + transaction.amount : income, 0
        );
      },

      expenses() {
        return Transaction.all.reduce((expense, transaction) =>
          transaction.amount < 0 ? expense + transaction.amount : expense, 0
        );
      },

      total() {
        return Transaction.incomes() + Transaction.expenses();
      }
    };

    // DOM manipulation
    const DOM = {
      transactionsContainer: document.querySelector('#data-table tbody'),

      addTransaction(transaction, index) {
        const tr = document.createElement('tr');
        tr.innerHTML = DOM.innerHTMLTransaction(transaction, index);
        tr.dataset.index = index;
        DOM.transactionsContainer.appendChild(tr);
      },

      innerHTMLTransaction(transaction, index) {
        const CSSclass = transaction.amount > 0 ? "income" : "expense";
        const amount = Utils.formatCurrency(transaction.amount);

        return `
        <td class="description">${transaction.description}</td>
        <td class="${CSSclass}">${amount}</td>
        <td class="date">${transaction.date}</td>
        <td>
          <img onclick="Transaction.remove(${index})" src="./assets/minus.svg" alt="Remove transaction">
        </td>
      `;
      },

      updateBalance() {
        document.getElementById('incomeDisplay').innerHTML = Utils.formatCurrency(Transaction.incomes());
        document.getElementById('expenseDisplay').innerHTML = Utils.formatCurrency(Transaction.expenses());
        document.getElementById('totalDisplay').innerHTML = Utils.formatCurrency(Transaction.total());
      },

      clearTransactions() {
        DOM.transactionsContainer.innerHTML = "";
      }
    };

    // Utility functions for formatting values
    const Utils = {
      formatAmount(value) {
        return Number(value) * 100;
      },

      formatDate(date) {
        const [year, month, day] = date.split("-");
        return `${day}/${month}/${year}`;
      },

      formatCurrency(value) {
        const signal = Number(value) < 0 ? "-" : "";
        value = (Math.abs(Number(value)) / 100).toLocaleString("pt-BR", {
          style: "currency",
          currency: "BRL"
        });

        return signal + value;
      }
    };

    // Form handling
    const Form = {
      description: document.querySelector('input#description'),
      amount: document.querySelector('input#amount'),
      date: document.querySelector('input#date'),

      getValues() {
        return {
          description: Form.description.value,
          amount: Form.amount.value,
          date: Form.date.value
        };
      },

      validateFields() {
        const {
          description,
          amount,
          date
        } = Form.getValues();

        if (!description.trim() || !amount.trim() || !date.trim()) {
          throw new Error("Please fill out all fields.");
        }
      },

      formatValues() {
        let {
          description,
          amount,
          date
        } = Form.getValues();
        amount = Utils.formatAmount(amount);
        date = Utils.formatDate(date);
        return {
          description,
          amount,
          date
        };
      },

      clearFields() {
        Form.description.value = "";
        Form.amount.value = "";
        Form.date.value = "";
      },

      submit(event) {
        event.preventDefault();

        try {
          Form.validateFields();
          const transaction = Form.formatValues();
          Transaction.add(transaction);
          Form.clearFields();
          Modal.close();
        } catch (error) {
          alert(error.message);
        }
      }
    };

    // Application initialization and reloading
    const App = {
      init() {
        Transaction.all.forEach(DOM.addTransaction);
        DOM.updateBalance();
        Storage.set(Transaction.all);
      },

      reload() {
        DOM.clearTransactions();
        App.init();
      }
    };

    App.init();
  </script>
</body>

</html>