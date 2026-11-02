# 🧑‍💼 Human Resource Management Module – Laravel

This Laravel application is a **Human Resource Management (HRM)** system designed to manage employees and their leave (congés).
It allows administrators to add, edit, and delete employees, as well as manage their leave requests easily through a clean and modern interface built with Blade and TailwindCSS.

---

## 🚀 Features

### 👨‍💻 Employee Management

* Create, view, edit, and delete employees.
* Each employee has:

  * Name
  * Position
  * Email
  * Mobile number

### 🌴 Leave (Congé) Management

* Add new leave requests for employees.
* Edit or delete existing leave records.
* Validate leave period (start date < end date).
* View all leaves in a table format.
* Automatically links each leave to an employee.

---

## 🏧 Project Structure

### Controllers

* **EmployeeController** → Handles CRUD operations for employees.
* **CongesController** → Manages leave requests (create, edit, delete, list).
* **ProfileController** → Manages user profile and authentication.
* **StudentController** → Example resource controller (not yet used).

### Models

* `Employee`
* `Conges`
* (Optional) `Student`

### Views (Blade)

* `employees/` → Employee management views.
* `conges/` → Leave management views:

  * `index.blade.php` → List of all leaves.
  * `create.blade.php` → Add a new leave.
  * `edit.blade.php` → Edit leave information.
  * `confirm_delete.blade.php` → Confirm deletion before removing a record.

---

## ⚙️ Installation Guide

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/yourusername/hrm-laravel.git
cd hrm-laravel
```

### 2️⃣ Install Dependencies

```bash
composer install
npm install
```

### 3️⃣ Configure Environment

Copy `.env.example` to `.env` and configure your database:

```bash
cp .env.example .env
```

Edit the `.env` file:

```env
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4️⃣ Generate Application Key

```bash
php artisan key:generate
```

### 5️⃣ Run Migrations

```bash
php artisan migrate
```

### 6️⃣ Start the Development Server

```bash
php artisan serve
```

Now visit **[http://127.0.0.1:8000](http://127.0.0.1:8000)** in your browser 🎉

---

## 🧩 Technologies Used

* **Laravel 11**
* **Blade Templates**
* **Tailwind CSS**
* **PHP 8+**
* **MySQL**

---

## 📊 Database Structure

### Employees Table

| Field    | Type   | Description            |
| -------- | ------ | ---------------------- |
| id       | int    | Primary key            |
| name     | string | Employee full name     |
| position | string | Employee role/position |
| email    | string | Unique email           |
| mobile   | string | Phone number           |

### Conges Table

| Field         | Type   | Description          |
| ------------- | ------ | -------------------- |
| id            | int    | Primary key          |
| employee_name | string | Linked employee name |
| start_date    | date   | Leave start date     |
| end_date      | date   | Leave end date       |
| reason        | text   | Reason for leave     |

---

## 💡 Example Pages

### 🏠 Congés List

Displays all leave records with options to edit or delete.

### ➕ Add Congé

Form to select employee, set start and end dates, and specify reason.

### ✏️ Edit Congé

Form pre-filled with existing data to update a leave record.

---

## 👨‍🔧 Author

**Skander Bardaoui**
🌍 Tunisia
🎓 Business Intelligence & Software Engineering Student
💼 Interested in Data Science & Web Development

---

## 🪪 License

This project is open-source and available under the [MIT License](LICENSE).

---

> 🧠 *Simple Laravel HR module to manage employees and their leaves efficiently!*
