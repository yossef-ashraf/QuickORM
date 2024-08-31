### Why Active Record?

- **Direct Link to Tables:** In Active Record, each Model class is directly linked to a table in the database. The `Model` classes in your code appear to represent the tables in the database, with each object handling its own data.

- **Built-in CRUD Functions:** A Model class typically includes CRUD (Create, Read, Update, Delete) operations that can be performed directly on data. If your Model classes have functions for saving, updating, and deleting, this indicates the Active Record pattern.

### Properties of Active Record

- **Tight Link between Objects and Tables:** Each object in Active Record represents a single row in the table to which it is linked.

- **Direct Control over Data:** Active Record typically includes methods for performing basic operations such as inserting, updating, deleting, and searching for data.

### Active Record Examples

- **Laravel's Eloquent ORM:** is a popular example of the Active Record pattern, where each model class represents a table in the database and contains methods to interact with the data such as `save()`, `update()`, `delete()`.