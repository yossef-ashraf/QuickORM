-- 1. **Using Alias ​​(nickname)**:
--  ```sql
 SELECT customer_id AS id, customer_name AS name
 FROM customers;
--  ```

-- 2. **Using Group By**:
--  ```sql
 SELECT department, COUNT(*) AS employee_count
 FROM employees
 GROUP BY department;
--  ```

-- 3. **Usage of Having**:
--  ```sql
 SELECT department, COUNT(*) AS employee_count
 FROM employees
 GROUP BY department
 HAVING COUNT(*) > 10;
--  ```

-- 4. **Using Order By ASC and DESC**:
--  ```sql
 SELECT customer_name, credit_limit
 FROM customers
 ORDER BY credit_limit ASC;

 SELECT customer_name, credit_limit
 FROM customers
 ORDER BY credit_limit DESC;
--  ```

-- 5. **Using Joins**:
--  - Inner Join:
--  ```sql
 SELECT employees.name, departments.department_name
 FROM employees
 INNER JOIN departments ON employees.department_id = departments.id;
--  ```
--  - LeftJoin:
--  ```sql
 SELECT employees.name, departments.department_name
 FROM employees
 LEFT JOIN departments ON employees.department_id = departments.id;
--  ```
--  - Right Join:
--  ```sql
 SELECT employees.name, departments.department_name
 FROM employees
 RIGHT JOIN departments ON employees.department_id = departments.id;
--  ```
--  - Full Join:
--  ```sql
 SELECT employees.name, departments.department_name
 FROM employees
 FULL JOIN departments ON employees.department_id = departments.id;
--  ```

-- 6. **Using Functions**:
--  -AVG():
--  ```sql
 SELECT AVG(salary) AS average_salary
 FROMemployees;
--  ```
--  -SUM():
--  ```sql
 SELECT SUM(salary) AS total_salary
 FROMemployees;
--  ```
--  -COUNT():
--  ```sql
 SELECT COUNT(*) AS employee_count
 FROMemployees;
--  ```
--  - MIN():
--  ```sql
 SELECT MIN(salary) AS minimum_salary
 FROMemployees;
--  ```
--  - MAX():
--  ```sql
 SELECT MAX(salary) AS maximum_salary
 FROMemployees;
--  ```

-- 7. **Use of WHERE**:
--  -LIKE:
--  ```sql
 SELECT *
 FROM customers
 WHERE customer_name LIKE 'A%';
--  ```
--  -IN:
--  ```sql
 SELECT *
 FROM employees
 WHERE department_id IN (1, 2, 3);
--  ```
--  -BETWEEN:
--  ```sql
 SELECT *
 FROM employees
 WHERE salary BETWEEN 50000 AND 100000;
--  ```
--  - ANY:
--  ```sql
 SELECT *
 FROM employees
 WHERE salary > ANY (SELECT salary FROM employees WHERE department_id = 2);
--  ```
--  -EXISTS:
--  ```sql
 SELECT *
 FROM customers
 WHERE EXISTS (SELECT * FROM orders WHERE customers.customer_id = orders.customer_id);
--  ```
--  -ALL:
--  ```sql
 SELECT *
 FROM employees
 WHERE salary > ALL (SELECT salary FROM employees WHERE department_id = 2);
--  ```
--  - AND, OR, NOT:
--  ```sql
 SELECT *
 FROM employees
 WHERE department_id = 1 AND salary > 50000;

 SELECT *
 FROM employees
 WHERE department_id = 1 OR salary > 50000;

 SELECT *
 FROM employees
 WHERE NOT department_id = 1;
--  ```
--  - mathematical calculations:
--  ```sql
 SELECT *
 FROM employees
 WHERE salary > 50000 AND department_id < 5;
--  ```