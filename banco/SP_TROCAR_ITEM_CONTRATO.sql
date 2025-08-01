
CREATE PROCEDURE [dbo].[TROCAR_ITEM_CONTRATO]
    @Contrato VARCHAR(10),
    @ItemRetirado VARCHAR(20),
    @ItemAdicionado VARCHAR(20),
    @User VARCHAR(30)
AS
BEGIN
    SET NOCOUNT ON;

    INSERT INTO [dbo].[TB02112]
           ([TB02112_DTCAD]
           ,[TB02112_OPCAD]
           ,[TB02112_DTALT]
           ,[TB02112_OPALT]
           ,[TB02112_CODIGO]
           ,[TB02112_PRODUTO]
           ,[TB02112_NUMSERIE]
           ,[TB02112_DATA]
           ,[TB02112_END]
           ,[TB02112_NUM]
           ,[TB02112_COMP]
           ,[TB02112_BAIRRO]
           ,[TB02112_CIDADE]
           ,[TB02112_ESTADO]
           ,[TB02112_CEP]
           ,[TB02112_FONE]
           ,[TB02112_FONEAUX]
           ,[TB02112_FAX]
           ,[TB02112_CONTATO]
           ,[TB02112_EMAIL]
           ,[TB02112_TIPOINTERV]
           ,[TB02112_HORAS]
           ,[TB02112_TIPOHORAS]
           ,[TB02112_CODTEC]
           ,[TB02112_PREST]
           ,[TB02112_OBS]
           ,[TB02112_NOVO]
           ,[TB02112_VEND]
           ,[TB02112_TIPODESC]
           ,[TB02112_CONDPAG]
           ,[TB02112_STATUS]
           ,[TB02112_CODVENDA]
           ,[TB02112_SITUACAO]
           ,[TB02112_DTINATIVO]
           ,[TB02112_LOCAL]
           ,[TB02112_IBTRACKER1]
           ,[TB02112_IBTRACKER2]
           ,[TB02112_CUSTO]
           ,[TB02112_PAT]
           ,[TB02112_FRANQPB]
           ,[TB02112_FRANQCOLOR]
           ,[TB02112_FRANQTOTAL]
           ,[TB02112_FRANQDG]
           ,[TB02112_FRANQGF]
           ,[TB02112_NETNAME]
           ,[TB02112_IP]
           ,[TB02112_ORIG]
           ,[TB02112_TIPOEQUIP]
           ,[TB02112_NDDID]
           ,[TB02112_NDDSITE]
           ,[TB02112_NDDDEPTO]
           ,[TB02112_ETIQSERV]
           ,[TB02112_FRANQGFC]
           ,[TB02112_HORASPAPEL]
           ,[TB02112_MODULO]
           ,[TB02112_HORASSUP]
           ,[TB02112_HORASATENDE]
           ,[TB02112_TIPOINSTALL]
           ,[TB02112_GRUPO]
           ,[TB02112_CODSITE]
           ,[TB02112_CODDEPTO]
           ,[TB02112_LIBLEITURA]
           ,[TB02112_NUMSERIEINTEGRADO]
           ,[TB02112_PRECONTRATO]
           ,[TB02112_TIPOSLA])
    SELECT TOP 1
           GETDATE()
           ,@User
           ,GETDATE()
           ,NULL
           ,A.[TB02112_CODIGO]
           ,ItemAdicionado.Prod
           ,ItemAdicionado.Serie
           ,GETDATE()
           ,A.[TB02112_END]
           ,A.[TB02112_NUM]
           ,A.[TB02112_COMP]
           ,A.[TB02112_BAIRRO]
           ,A.[TB02112_CIDADE]
           ,A.[TB02112_ESTADO]
           ,A.[TB02112_CEP]
           ,A.[TB02112_FONE]
           ,A.[TB02112_FONEAUX]
           ,A.[TB02112_FAX]
           ,A.[TB02112_CONTATO]
           ,A.[TB02112_EMAIL]
           ,A.[TB02112_TIPOINTERV]
           ,A.[TB02112_HORAS]
           ,A.[TB02112_TIPOHORAS]
           ,A.[TB02112_CODTEC]
           ,A.[TB02112_PREST]
           ,A.[TB02112_OBS]
           ,A.[TB02112_NOVO]
           ,A.[TB02112_VEND]
           ,A.[TB02112_TIPODESC]
           ,A.[TB02112_CONDPAG]
           ,A.[TB02112_STATUS]
           ,A.[TB02112_CODVENDA]
           ,'A'
           ,A.[TB02112_DTINATIVO]
           ,A.[TB02112_LOCAL]
           ,A.[TB02112_IBTRACKER1]
           ,A.[TB02112_IBTRACKER2]
           ,ItemAdicionado.Custo
           ,ItemAdicionado.Pat
           ,A.[TB02112_FRANQPB]
           ,A.[TB02112_FRANQCOLOR]
           ,A.[TB02112_FRANQTOTAL]
           ,A.[TB02112_FRANQDG]
           ,A.[TB02112_FRANQGF]
           ,A.[TB02112_NETNAME]
           ,A.[TB02112_IP]
           ,A.[TB02112_ORIG]
           ,A.[TB02112_TIPOEQUIP]
           ,A.[TB02112_NDDID]
           ,A.[TB02112_NDDSITE]
           ,A.[TB02112_NDDDEPTO]
           ,A.[TB02112_ETIQSERV]
           ,A.[TB02112_FRANQGFC]
           ,A.[TB02112_HORASPAPEL]
           ,A.[TB02112_MODULO]
           ,A.[TB02112_HORASSUP]
           ,A.[TB02112_HORASATENDE]
           ,A.[TB02112_TIPOINSTALL]
           ,A.[TB02112_GRUPO]
           ,A.[TB02112_CODSITE]
           ,A.[TB02112_CODDEPTO]
           ,A.[TB02112_LIBLEITURA]
           ,A.[TB02112_NUMSERIEINTEGRADO]
           ,A.[TB02112_PRECONTRATO]
           ,A.[TB02112_TIPOSLA]
    FROM TB02112 A
    OUTER APPLY (
        SELECT 
            TB02054_CUSTO AS Custo,
            TB02054_PRODUTO AS Prod,
            TB02054_NUMSERIE AS Serie,
            TB02054_PAT AS Pat
        FROM TB02054
        WHERE TB02054_PAT = @ItemAdicionado
    ) AS ItemAdicionado
    WHERE A.TB02112_PAT = @ItemRetirado
      AND A.TB02112_CODIGO = @Contrato
      AND A.TB02112_SITUACAO = 'A'

	UPDATE 
		TB02112 
	SET TB02112_SITUACAO = 'I'
	WHERE TB02112_CODIGO = @Contrato
	AND TB02112_PAT = @ItemRetirado
END
GO


