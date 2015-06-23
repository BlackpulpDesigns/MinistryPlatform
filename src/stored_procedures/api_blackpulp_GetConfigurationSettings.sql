
/****** Object:  StoredProcedure [dbo].[api_blackpulp_GetConfigurationSettings]    Script Date: 06/23/2015 14:38:00 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[api_blackpulp_GetConfigurationSettings]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[api_blackpulp_GetConfigurationSettings]
GO


/****** Object:  StoredProcedure [dbo].[api_blackpulp_GetConfigurationSettings]    Script Date: 06/23/2015 14:38:00 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO



CREATE PROCEDURE [dbo].[api_blackpulp_GetConfigurationSettings]

  @DomainID int,
  @ApplicationCode varchar(50),
  @KeyName varchar(50) = ''
  
AS
BEGIN

  SELECT 
    CS.Key_Name, 
    CASE WHEN CS.Application_Code='COMMON' AND EXISTS(SELECT 1 FROM dp_Configuration_Settings CS2 WHERE CS2.Application_Code=@ApplicationCode AND CS2.Key_Name=CS.Key_Name AND CS2.Domain_ID=@DomainID)
      THEN (SELECT CS2.Value FROM dp_Configuration_Settings CS2 WHERE CS2.Application_Code=@ApplicationCode AND CS2.Key_Name=CS.Key_Name AND CS2.Domain_ID=@DomainID)
      ELSE CS.Value END
      AS Value,
    CS.Description,
    CS.Configuration_Setting_ID 
  FROM dp_Configuration_Settings CS
  WHERE Domain_ID = @DomainID
    AND ([Application_Code] = @ApplicationCode OR [Application_Code] = 'COMMON')
    AND ([KEY_NAME] LIKE @KeyName OR @KeyName = '')
    ORDER BY CS.Key_Name;


END


GO


