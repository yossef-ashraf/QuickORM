# db/orm This repository contains a project that leverages ORM (Object-Relational Mapping) technology to manage and interact with databases in a convenient and efficient manner. The project demonstrates how to use ORM to manipulate databases through code without needing to write SQL queries directly.

## Features - **Simple API:** Easily manipulate databases using objects and classes.
- **Full CRUD Support:** Insert, read, update, and delete data effortlessly.
- **Dynamic Queries:** Build SQL queries dynamically with `QueryBuilder`.
- **Relationship Management:** Handle relationships between tables, including one-to-one, one-to-many, and many-to-many.
- **Database Compatibility:** Works with most relational databases supported by PDO.

## Installation To install `db/orm` via Composer, run the following command in your project directory: ```bash composer require db/orm ``` ## Basic Usage ### Setting up a Database Connection Configure the database connection settings in your configuration file: ```php use DB\ORM\Database;

Database::configure([ 'host' => 'localhost', 'dbname' => 'your_database_name', 'user' => 'your_database_user', 'password' => 'your_database_password', ]);
``` ### Creating a Model Create a model that represents a table in your database: ```php use DB\ORM\Model;

class User extends Model { protected $table = 'users';
} ``` ### CRUD Operations - **Insert a New Record:** ```php $user = new User();
$user->name = 'John Doe';
$user->email = 'john@example.com';
$user->save();
``` - **Read a Record:** ```php $user = User::find(1);
echo $user->name;
``` - **Update a Record:** ```php $user = User::find(1);
$user->name = 'Jane Doe';
$user->save();
``` - **Delete a Record:** ```php $user = User::find(1);
$user->delete();
``` ### Custom Queries Use `QueryBuilder` to create custom SQL queries: ```php use DB\ORM\QueryBuilder;

$results = QueryBuilder::table('users') ->where('age', '>', 30) ->get();
``` ### Relationship Management Define relationships between models: ```php class Post extends Model { protected $table = 'posts';

 public function user() { return $this->belongsTo(User::class);
 } } class User extends Model { protected $table = 'users';

 public function posts() { return $this->hasMany(Post::class);
 } } ``` ## Contents - **Introduction to ORM** - **Practical Examples** - Creating objects and storing them in the database - Retrieving data from the database - Updating existing data - Deleting data - **Best Practices ** ## Project Advantages - Provides a convenient way to interact with databases.
- Facilitates the process of managing software data.
- Reduces errors caused by manual SQL queries.
- Improves the efficiency and performance of applications.

## How to Use 1. **Clone the Repository:** ```bash git clone https://github.com/yossef-ashraf/ORM.git ``` 2. **Environment Setup:** Prepare your programming environment and install all required dependencies.

3. **Running Examples:** Execute the examples included in the project to understand how to use the ORM.

## Contribution We welcome contributions to enhance this project. If you would like to participate, please: - Open an [Issue](https://github.com/yossef-ashraf/ORM/issues) for any questions or feedback.
- Submit a [Pull Request](https://github.com/yossef-ashraf/ORM/pulls) with your improvements.

## License This project is licensed under the [MIT License](LICENSE).

--- Best regards, [Yossef Ashraf](https://github.com/yossef-ashraf)