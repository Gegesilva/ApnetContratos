

CREATE TABLE [dbo].[SUBS_CONTRATO](
	[PAT_ENTRADA] [varchar](20) NULL,
	[PAT_SAIDA] [varchar](20) NULL,
	[PEDIDO_COMPRA] [varchar](6) NULL,
	[ORCAMENTO] [varchar](20) NULL,
	[STATUS_COMPRA] [varchar](2) NULL,
	[OPCAD] [varchar](30) NULL,
	[COD_CONTRATO] [varchar](12) NULL,
	[CONCLUIDO_S_N] [char](1) NULL,
	[TIPO_OP] [varchar](30) NULL,
	[ACESSORIOS_S_N] [char](1) NULL,
	[DTCAD] [datetime] NULL
) ON [PRIMARY]
GO


