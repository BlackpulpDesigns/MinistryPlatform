IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[api_blackpulp_get_custom_form_details]') AND type in (N'P', N'PC'))
  DROP PROCEDURE [dbo].[api_blackpulp_get_custom_form_details]
GO

SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[api_blackpulp_get_custom_form_details]

  @DomainID INT
  ,@FormID INT

  AS
  BEGIN
    

    --
    ---- Table 0: Custom Form
    --

    SELECT 
      F.*
      ,(SELECT COUNT(Form_Field_ID) AS Field_Count FROM Form_Fields FF WHERE FF.Form_ID = @FormID AND FF.Domain_ID=@DomainID)
    FROM Forms F 
    WHERE F.Form_ID=@FormID

  --
  ---- Table 1: Form Field Details
  --

  SELECT 
      FF.*
      ,FFT.Field_Type
    FROM Forms F 
      INNER JOIN Form_Fields FF ON FF.Form_ID=F.Form_ID
      INNER JOIN Form_Field_Types FFT ON FF.Field_Type_ID=FFT.Form_Field_Type_ID
    WHERE F.Form_ID=@FormID


  END

GO


