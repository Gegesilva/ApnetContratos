CREATE TABLE [dbo].[SUBS_CONTRATO_ACESSORIOS](
	[DTCAD] [smalldatetime] NULL,
	[OPCAD] [varchar](20) NULL,
	[DTALT] [smalldatetime] NULL,
	[OPALT] [varchar](20) NULL,
	[CONTRATO] [varchar](12) NULL,
	[PRODUTO] [varchar](5) NULL,
	[NUMSERIE] [varchar](50) NULL,
	[CODACESSORIO] [varchar](5) NULL,
	[QTPROD] [int] NULL,
	[OBS] [text] NULL,
	[NUMSERIEACESSORIO] [varchar](50) NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO


