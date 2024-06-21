- SELECT from two tables
```sql
SELECT * FROM Table1;
SELECT * FROM Table2;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob

-- Table2
id | course
---|-------
1  | Math
2  | Science

-- Query:
SELECT * FROM Table1;
-- Respons:
id | name
---|------
1  | Alice
2  | Bob

SELECT * FROM Table2;
-- Respons:
id | course
---|-------
1  | Math
2  | Science
```

- INNER JOIN
```sql
SELECT * 
FROM Table1 t1 
INNER JOIN Table2 t2 
ON t1.id = t2.id;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob

-- Table2
id | course
---|-------
1  | Math
2  | Science

-- Query:
SELECT * 
FROM Table1 t1 
INNER JOIN Table2 t2 
ON t1.id = t2.id;

-- Respons:
id | name  | course
---|-------|--------
1  | Alice | Math
2  | Bob   | Science
```

- LEFT OUTER JOIN
```sql
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob
3  | Carol

-- Table2
id | course
---|-------
1  | Math
2  | Science

-- Query:
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id;

-- Respons:
id | name  | course
---|-------|--------
1  | Alice | Math
2  | Bob   | Science
3  | Carol | NULL
```

- RIGHT OUTER JOIN
```sql
SELECT * 
FROM Table1 t1 
RIGHT OUTER JOIN Table2 t2 
ON t1.id = t2.id;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob

-- Table2
id | course
---|-------
1  | Math
2  | Science
3  | History

-- Query:
SELECT * 
FROM Table1 t1 
RIGHT OUTER JOIN Table2 t2 
ON t1.id = t2.id;

-- Respons:
id | name  | course
---|-------|--------
1  | Alice | Math
2  | Bob   | Science
3  | NULL  | History
```

- SEMI JOIN (Similar to INNER JOIN, with less duplication)
```sql
SELECT * 
FROM Table1 t1 
WHERE EXISTS (SELECT 1 
              FROM Table2 t2 
              WHERE t1.id = t2.id);
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob
3  | Carol

-- Table2
id | course
---|-------
1  | Math
2  | Science

-- Query:
SELECT * 
FROM Table1 t1 
WHERE EXISTS (SELECT 1 
              FROM Table2 t2 
              WHERE t1.id = t2.id);

-- Respons:
id | name
---|------
1  | Alice
2  | Bob
```

- ANTI SEMI JOIN
```sql
SELECT * 
FROM Table1 t1 
WHERE NOT EXISTS (SELECT 1 
                  FROM Table2 t2 
                  WHERE t1.id = t2.id);
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob
3  | Carol

-- Table2
id | course
---|-------
1  | Math
2  | Science

-- Query:
SELECT * 
FROM Table1 t1 
WHERE NOT EXISTS (SELECT 1 
                  FROM Table2 t2 
                  WHERE t1.id = t2.id);

-- Respons:
id | name
---|------
3  | Carol
```

- LEFT OUTER JOIN with exclusion
```sql
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
WHERE t2.id IS NULL;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob
3  | Carol

-- Table2
id | course
---|-------
1  | Math
2  | Science

-- Query:
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
WHERE t2.id IS NULL;

-- Respons:
id | name  | course
---|-------|--------
3  | Carol | NULL
```

- RIGHT OUTER JOIN with exclusion
```sql
SELECT * 
FROM Table1 t1 
RIGHT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
WHERE t1.id IS NULL;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob

-- Table2
id | course
---|-------
1  | Math
2  | Science
3  | History

-- Query:
SELECT * 
FROM Table1 t1 
RIGHT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
WHERE t1.id IS NULL;

-- Respons:
id | name  | course
---|-------|--------
3  | NULL  | History
```

- FULL OUTER JOIN
```sql
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id
UNION
SELECT * 
FROM Table1 t1 
RIGHT OUTER JOIN Table2 t2 
ON t1.id = t2.id;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob
3  | Carol

-- Table2
id | course
---|-------
1  | Math
2  | Science
4  | History

-- Query:
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id
UNION
SELECT * 
FROM Table1 t1 
RIGHT OUTER JOIN Table2 t2 
ON t1.id = t2.id;

-- Respons:
id | name  | course
---|-------|--------
1  | Alice | Math
2  | Bob   | Science
3  | Carol | NULL
4  | NULL  | History
```

-. FULL OUTER JOIN with exclusion
```sql
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
WHERE t2.id IS NULL
UNION
SELECT * 
FROM Table1 t1 
RIGHT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
WHERE t1.id IS NULL;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob
3  | Carol

-- Table2
id | course
---|-------
1  | Math
2  | Science
4  | History

-- Query:
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
WHERE t2.id IS NULL
UNION
SELECT * 
FROM Table1 t1 
RIGHT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
WHERE t1.id IS NULL;

-- Respons:
id | name  | course
---|-------|--------
3  | Carol | NULL
4  | NULL  | History
```

-. Two INNER JOINs
```sql
SELECT * 
FROM Table1 t1 
INNER JOIN Table2 t2 
ON t1.id = t2.id 
INNER JOIN Table3 t3 
ON t1.id = t3.id;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob

-- Table2
id | course
---|-------
1  | Math
2  | Science

-- Table3
id | grade
---|------
1  | A
2  | B

-- Query:
SELECT * 
FROM Table1 t1 
INNER JOIN Table2 t2 
ON t1.id = t2.id 
INNER JOIN Table3 t3 
ON t1.id = t3.id;

-- Respons:
id | name  | course   | grade
---|-------|----------|------
1  | Alice | Math     | A
2  | Bob   | Science  | B
```

-. Two LEFT OUTER JOINs
```sql
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
LEFT OUTER

 JOIN Table3 t3 
ON t1.id = t3.id;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob
3  | Carol

-- Table2
id | course
---|-------
1  | Math
2  | Science

-- Table3
id | grade
---|------
1  | A
2  | B

-- Query:
SELECT * 
FROM Table1 t1 
LEFT OUTER JOIN Table2 t2 
ON t1.id = t2.id 
LEFT OUTER JOIN Table3 t3 
ON t1.id = t3.id;

-- Respons:
id | name  | course   | grade
---|-------|----------|------
1  | Alice | Math     | A
2  | Bob   | Science  | B
3  | Carol | NULL     | NULL
```

-. INNER JOIN and a LEFT OUTER JOIN
```sql
SELECT * 
FROM Table1 t1 
INNER JOIN Table2 t2 
ON t1.id = t2.id 
LEFT OUTER JOIN Table3 t3 
ON t1.id = t3.id;
```
-----------------------
```sql
-- Table1
id | name
---|------
1  | Alice
2  | Bob

-- Table2
id | course
---|-------
1  | Math
2  | Science

-- Table3
id | grade
---|------
1  | A

-- Query:
SELECT * 
FROM Table1 t1 
INNER JOIN Table2 t2 
ON t1.id = t2.id 
LEFT OUTER JOIN Table3 t3 
ON t1.id = t3.id;

-- Respons:
id | name  | course   | grade
---|-------|----------|------
1  | Alice | Math     | A
2  | Bob   | Science  | NULL
```

هذه الأمثلة توضح كيفية استخدام أنواع مختلفة من الانضمامات في SQL مع بيانات توضيحية وناتج متوقع لكل Query. إذا كنت بحاجة إلى مزيد من التفاصيل أو توضيح إضافي لأي من هذه الأمثلة، فلا تتردد في السؤال.