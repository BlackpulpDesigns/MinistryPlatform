
/****** Object:  StoredProcedure [dbo].[api_blackpulp_FindMatchingContact]    Script Date: 06/03/2015 09:07:43 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[api_blackpulp_FindMatchingContact]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[api_blackpulp_FindMatchingContact]
GO

/****** Object:  StoredProcedure [dbo].[api_blackpulp_FindMatchingContact]    Script Date: 06/03/2015 09:07:43 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO



CREATE PROCEDURE [dbo].[api_blackpulp_FindMatchingContact]

  @DomainID int
  ,@FirstName varchar(50)
  ,@LastName varchar(50)
  ,@EmailAddress varchar(150) = ''
  ,@Phone varchar(50) = ''
  ,@DOB datetime = NULL

AS
BEGIN

  SET @Phone = REPLACE(REPLACE(REPLACE(REPLACE(@Phone,' ',''),'-',''),')',''),'(','')

  SELECT 
    C.*
    ,ISNULL(H.Address_ID,0) AS [Address_ID]
    ,ISNULL(H.Congregation_ID,0) AS [Congregation_ID]
    ,U.[User_Name]
    ,U.User_GUID
  FROM Contacts C
    LEFT OUTER JOIN dp_Users U ON U.[User_ID] = C.User_Account
    LEFT OUTER JOIN Households H ON H.Household_ID = C.Household_ID
  WHERE
    C.Last_Name LIKE @LastName
    AND (C.First_Name LIKE @FirstName OR C.Nickname LIKE @FirstName)
    AND (
    C.Last_Name LIKE @LastName
    AND (C.First_Name LIKE @FirstName OR C.Nickname LIKE @FirstName)
    AND (
      -- email
      C.Email_Address LIKE @EmailAddress
      OR
      -- OR phone
        (
        REPLACE(REPLACE(REPLACE(REPLACE(C.Mobile_Phone,' ',''),'-',''),')',''),'(','') LIKE @Phone
        OR  REPLACE(REPLACE(REPLACE(REPLACE(C.Company_Phone,' ',''),'-',''),')',''),'(','') LIKE @Phone
        OR  REPLACE(REPLACE(REPLACE(REPLACE(H.Home_Phone,' ',''),'-',''),')',''),'(','') LIKE @Phone
        )
      -- OR DOB
      OR
        C.Date_of_Birth = @DOB
      )
    )
    OR (U.User_Name=@EmailAddress AND @EmailAddress <> '')

END



GO


