### 1. What is ORM?
ORM stands for **Object-Relational Mapping**, which is a technique used in programming to transform data between a relational database system and objects in code. Using ORM, you can interact with the database using objects instead of directly working with tables and SQL.

### 2. Basics of Databases
- **Tables:** Tables represent objects in the database. Each table contains rows and columns.
- **Rows:** Represent records in tables. Each row represents a single object.
- **Columns:** Represent properties of objects. For example, if we have a table that represents "Users", the columns might contain "Name", "Email", and "Password".
- **Primary Keys:** These are used to uniquely identify each row in the table.
- **Relationships:** Shows how tables are related to each other. There are several types of relationships such as One-to-One, One-to-Many, and Many-to-Many.

### 3. Types of ORM
- **Active Record:** Includes ORM objects that are directly linked to a database table, where each object represents a row in the table. Active Record includes both data and operations that can be performed on the data such as saving, updating, or deleting.

- **Data Mapper:** Separates objects from operations that are performed on the database. Data Mapper allows for more flexibility and the ability to handle complex databases.

### 4. Explain your ORM code
In your repository, you create your own ORM using PHP. The goal of this code is to allow developers to interact with databases in a more convenient way without having to write SQL queries directly.

- **Model Class:** This class represents the object that links the tables in the database to the application code. This class contains functions to perform operations such as save, update, and delete.

- **QueryBuilder Class:** This class is responsible for dynamically building SQL queries based on the operations you want to perform on the data. For example, this class can be used to create a query to select all rows from a specific table, or update a specific row based on certain conditions.

- **Database Connection:** This code includes the database connection settings, which are used by the ORM to perform queries and other operations on the database.

In short, the ORM you create aims to simplify interaction with databases through the use of objects and programming functions, reducing the need to write SQL queries directly.