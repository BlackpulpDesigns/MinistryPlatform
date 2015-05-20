
/****** Object:  StoredProcedure [dbo].[api_blackpulp_getUserInfoByGuid]    Script Date: 05/20/2015 13:02:41 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[api_blackpulp_getUserInfoByGuid]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[api_blackpulp_getUserInfoByGuid]
GO

/****** Object:  StoredProcedure [dbo].[api_blackpulp_getUserInfoByGuid]    Script Date: 05/20/2015 13:02:41 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO



/**************************************************************************************************/
/*************************     api_blackpulp_getUserInfo                  *************************/
/**************************************************************************************************/
CREATE PROCEDURE [dbo].[api_blackpulp_getUserInfoByGuid]
  @DomainID int,
  @GUID uniqueidentifier
  
AS
BEGIN
  DECLARE @ContactsTabID int
  DECLARE @HouseholdID int
  DECLARE @DefaultCountryCode varchar(10)
  DECLARE @HOHPositionID int
  SELECT @DefaultCountryCode = Value FROM dp_Configuration_Settings WHERE Application_Code = 'COMMON' AND Key_Name = 'DefaultCountryCode' AND Domain_ID = @DomainID
  SELECT @HOHPositionID = Value FROM dp_Configuration_Settings WHERE Application_Code = 'COMMON' AND Key_Name = 'hhPosition' AND Domain_ID = @DomainID

  SELECT @ContactsTabID = Page_ID FROM dp_Pages WHERE Table_Name LIKE 'Contacts'
  SET @HouseholdID = 0

  SELECT @HouseholdID = Household_ID
  FROM Contacts C
    INNER JOIN dp_Users U ON C.User_Account=U.User_ID
  WHERE C.Domain_ID = @DomainID
    AND U.User_GUID = @GUID
    AND Household_Position_ID = @HOHPositionID

  /* Table 0 */
  SELECT
    C.*
    ,H.Address_ID
    ,dp_Files.Unique_Name
    ,dp_Files.Extension
    ,U.User_GUID
    ,U.User_Name
    ,U.Can_Impersonate

  FROM Contacts C
  
    LEFT OUTER JOIN dp_Users U ON C.User_Account = U.[User_ID]
    LEFT OUTER JOIN Households H ON H.Household_ID = C.Household_ID
    LEFT OUTER JOIN dp_Files ON dp_Files.Page_ID = @ContactsTabID
      AND dp_Files.Record_ID = C.Contact_ID
      AND dp_Files.Default_Image = 1 

  WHERE C.Domain_ID = @DomainID AND U.User_GUID = @GUID
    
    /* Table 1 */
    SELECT
        U.*
    FROM dp_Users U
    WHERE U.Domain_ID = @DomainID AND U.User_GUID = @GUID

  /* Table 2 */
  SELECT Prefix_ID, Prefix FROM Prefixes ORDER BY Prefix

  /* Table 3 */
  SELECT Suffix_ID, Suffix FROM Suffixes ORDER BY Suffix

  /* Table 4 */
  SELECT Gender_ID, Gender FROM Genders ORDER BY Gender DESC

  /* Table 5 */
  SELECT Marital_Status_ID, Marital_Status FROM Marital_Statuses ORDER BY Marital_Status

  /* Table 6 */
  SELECT Contact_ID, Display_Name 
  FROM Contacts 
  WHERE Domain_ID = @DomainID
    AND Household_ID = @HouseholdID
  ORDER BY Date_of_Birth
  
END

GO


