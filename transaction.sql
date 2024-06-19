
-- ### Basic Structure of a Transaction

-- A typical transaction in SQL includes the following steps:
-- 1. Start the transaction.
-- 2. Perform the required SQL operations.
-- 3. Commit the transaction if all operations are successful.
-- 4. Rollback the transaction if any operation fails.

-- ### Example Scenario

-- Suppose you are managing a banking system, and you need to transfer money from one account to another. This operation involves two steps:
-- 1. Deduct the amount from the sender's account.
-- 2. Add the amount to the receiver's account.

-- Both operations must succeed together; otherwise, the transaction should be rolled back to prevent inconsistencies.

-- ### SQL Transaction Query

-- Here’s how you can perform a transaction in SQL:

-- ```sql
BEGIN TRANSACTION;

-- Deduct amount from sender's account
UPDATE accounts
SET balance = balance - 100
WHERE account_id = 1;

-- Add amount to receiver's account
UPDATE accounts
SET balance = balance + 100
WHERE account_id = 2;

-- Check for any errors
IF @@ERROR <> 0
BEGIN
    -- Rollback the transaction if there is an error
    ROLLBACK TRANSACTION;
    PRINT 'Transaction failed and was rolled back';
END
ELSE
BEGIN
    -- Commit the transaction if all operations are successful
    COMMIT TRANSACTION;
    PRINT 'Transaction succeeded and was committed';
END;
-- ```

-- ### Explanation

-- 1. **BEGIN TRANSACTION**: This command starts a new transaction.
-- 2. **UPDATE accounts SET balance = balance - 100 WHERE account_id = 1**: Deducts 100 units from the sender's account (account ID 1).
-- 3. **UPDATE accounts SET balance = balance + 100 WHERE account_id = 2**: Adds 100 units to the receiver's account (account ID 2).
-- 4. **IF @@ERROR <> 0**: Checks if any error occurred during the transaction. `@@ERROR` is a system function in SQL Server that returns the error number for the last Transact-SQL statement executed.
-- 5. **ROLLBACK TRANSACTION**: Rolls back the transaction if an error is detected, undoing all changes made in the transaction.
-- 6. **COMMIT TRANSACTION**: Commits the transaction if no errors are detected, making all changes permanent.

-- ### Notes
-- - In MySQL, you can use the `START TRANSACTION`, `ROLLBACK`, and `COMMIT` statements instead:
--     ```sql
    START TRANSACTION;

    -- Deduct amount from sender's account
    UPDATE accounts
    SET balance = balance - 100
    WHERE account_id = 1;

    -- Add amount to receiver's account
    UPDATE accounts
    SET balance = balance + 100
    WHERE account_id = 2;

    -- Check for any errors and commit or rollback
    IF @@ERROR <> 0 THEN
        ROLLBACK;
    ELSE
        COMMIT;
    END IF;



--     ```
-- - For PostgreSQL, use `BEGIN`, `COMMIT`, and `ROLLBACK` without additional error checking:
--     ```sql
    BEGIN;

    -- Deduct amount from sender's account
    UPDATE accounts
    SET balance = balance - 100
    WHERE account_id = 1;

    -- Add amount to receiver's account
    UPDATE accounts
    SET balance = balance + 100
    WHERE account_id = 2;

    -- Commit the transaction
    COMMIT;
    -- ```

-- This example demonstrates the fundamental concept of transactions in SQL, ensuring atomicity, consistency, isolation, and durability (ACID properties) in database operations.