DROP PROCEDURE [dbo].[api_blackpulp_GetUserRoles]
GO

SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[api_blackpulp_GetUserRoles]

	@DomainID int
	,@UserID int
AS
BEGIN

	SELECT Role_ID 
	INTO #Roles
	FROM dp_User_Roles
	WHERE [User_ID] = @UserID AND Domain_ID = @DomainID

	-- Table 0: Roles
	SELECT * FROM #Roles

	-- Table 1: Tools
	SELECT DISTINCT
	  T.Tool_ID
	  ,T.Tool_Name
	FROM dp_Tools T
	  INNER JOIN dp_Role_Tools RT ON T.Tool_ID=RT.Tool_ID
        AND RT.Role_ID IN (SELECT Role_ID FROM #Roles)

END

GO


