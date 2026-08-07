This project is an Employee Management System built with PHP and the Symfony framework. It provides CRUD operations for managing employees, their roles, and user accounts, alongside advanced search, filtering capabilities, and a REST API. The application utilizes Doctrine ORM for database management, Twig for templating, HTML with CSS and JavaScript for a responsive frontend.

## Installation

1. **Install dependencies:**:
   ```bash
      composer install
   ```

2. **Configure the database connection in .env:**:
   ```bash
      DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
   ```

3. **Run migrations:**
    ```bash
      php bin/console doctrine:migrations:migrate
    ```

3. **Load test data:**
    ```bash
      php bin/console doctrine:fixtures:load
    ```

4. **Start the Symfony server:**
    ```bash
      php -S localhost:8000 -t public
    ```

5. **The application will be accessible at http://127.0.0.1:8000**
