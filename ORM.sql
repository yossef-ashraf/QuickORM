BEGIN TRANSACTION;

BEGIN TRY


    COMMIT TRANSACTION;
END TRY
BEGIN CATCH

    ROLLBACK TRANSACTION;
END CATCH;







CREATE Table Employees
(
	Id int primary key identity,
	[Name] nvarchar(50),
	Email nvarchar(50),
	Department nvarchar(50)
)
Go

SET NOCOUNT ON
Declare @counter int = 1

While(@counter <= 1000000)
Begin
	Declare @Name nvarchar(50) = 'ABC ' + RTRIM(@counter)
	Declare @Email nvarchar(50) = 'abc' + RTRIM(@counter) + '@ITI.com'
	Declare @Dept nvarchar(10) = 'Dept ' + RTRIM(@counter)

	Insert into Employees values (@Name, @Email, @Dept)

	Set @counter = @counter +1

	If(@Counter%100000 = 0)
		Print RTRIM(@Counter) + ' rows inserted'
END




Select * from Employees where Id = 932000


Select * from Employees Where Name = 'ABC 932000'


-------------------------------------------------------------------------------

Create Table Gender
(
	GenderId int,
	GenderName nvarchar(20)
)
Go

Insert into Gender values(1, 'Male')
Insert into Gender values(3, 'Not Specified')
Insert into Gender values(2, 'Female')

-- to get all Table indexes 
sp_helpindex Gender


Select * from Gender where GenderName = 'Male'

Create nonclustered index IX_Gender_GenderName
on Gender(GenderName)


Select * from Gender where GenderName = 'Male'



Select * from Gender with (Index(IX_Gender_GenderName))
where GenderName = 'Male'