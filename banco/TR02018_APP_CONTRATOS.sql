USE [APNET]
GO
/****** Object:  Trigger [dbo].[TR02018_APP_CONTRATOS]    Script Date: 30/12/2025 10:54:44 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
ALTER TRIGGER [dbo].[TR02018_APP_CONTRATOS]
ON [dbo].[TB02018]
AFTER UPDATE
AS
BEGIN


    DECLARE @Orcamento VARCHAR(6)
    DECLARE @Status VARCHAR(2)

    DECLARE APP_CONTRATOS_CURSOR CURSOR FOR
    SELECT 
	    I.TB02018_CODIGO AS Orc,
	    I.TB02018_STATUS AS Status
    FROM inserted I
    INNER JOIN deleted D ON D.TB02018_CODIGO = I.TB02018_CODIGO
    WHERE I.TB02018_STATUS = '98'
        AND D.TB02018_STATUS = '93'

    OPEN APP_CONTRATOS_CURSOR

    FETCH NEXT FROM APP_CONTRATOS_CURSOR INTO @Orcamento, @Status

    WHILE @@FETCH_STATUS = 0
    BEGIN
        --  acoes 
        IF @Status = '98'
        BEGIN
			--Atualiza produtos no orçamento, que estão diferentes do pedido/ pegar da tabela auxiliar 
			UPDATE T19
			SET T19.TB02019_PRODUTO = P.TB02054_PRODUTO
			FROM TB02019 T19
			LEFT JOIN TB02030 T30 ON T30.TB02030_ORCAMENTO = T19.TB02019_CODIGO
			CROSS APPLY (
				SELECT TOP 1 TB02054_PRODUTO
				FROM SUBS_CONTRATO
                LEFT JOIN TB02054 ON TB02054_PAT = PAT_ENTRADA
				WHERE PEDIDO_COMPRA = T30.TB02030_CODIGO
				ORDER BY TB02054_PRODUTO
			) P
			WHERE T19.TB02019_CODIGO = @Orcamento
			  AND P.TB02054_PRODUTO <> T19.TB02019_PRODUTO

			--WAITFOR DELAY '00:00:01'

            INSERT INTO [dbo].[TB02055]
               ([TB02055_PRODUTO]
               ,[TB02055_NUMSERIE]
               ,[TB02055_CODIGO]
               ,[TB02055_TABELA]
               ,[TB02055_OPERACAO]
               ,[TB02055_QTPROD]
               ,[TB02055_CODEMP]
               ,[TB02055_DTVALIDADE]
               ,[TB02055_QTPRODB]
               ,[TB02055_CODEMP2]
               ,[TB02055_RMA]
               ,[TB02055_CODIGO2]
               ,[TB02055_IMOB]
               ,[TB02055_CODFOR]
               ,[TB02055_NOMEFOR]
               ,[TB02055_LOCSER]
               ,[TB02055_TIPOCON]
               ,[TB02055_LOCPPROD]
               ,[TB02055_PAT]
               ,[TB02055_OPCAD]
               ,[TB02055_DTFABRICACAO]
               ,[TB02055_STATUS]
               ,[TB02055_NOMESTATUS]
               ,[TB02055_MEDIDORPB]
               ,[TB02055_MEDIDORCOLOR]
               ,[TB02055_MEDIDORTOTAL]
               ,[TB02055_VALORUSD]
               ,[TB02055_PONTUACAO]
               ,[TB02055_FATOR])
         SELECT
                TB02019_PRODUTO     ---TB02055_PRODUTO
               ,TB02208_NUMSERIE     ---TB02055_NUMSERIE
               ,@Orcamento     ---TB02055_CODIGO, 
               ,'TB02018'     ---TB02055_TABELA, 
               ,'C'     ---TB02055_OPERACAO,
               ,TB02019_QTPROD     ---TB02055_QTPROD, 
               ,TB02030_CODEMP     ---TB02055_CODEMP, 
               ,NULL     ---TB02055_DTVALIDADE
               ,TB02019_QTPRODB     ---TB02055_QTPRODB,
               ,TB02030_CODEMP     ---TB02055_CODEMP2,
               ,NULL     ---TB02055_RMA, 
               ,NULL     ---TB02055_CODIGO2,
               ,'N'     ---TB02055_IMOB, 
               ,TB02030_CODFOR     ---TB02055_CODFOR, 
               ,TB02030_NOME     ---TB02055_NOMEFOR, 
               ,NULL     ---TB02055_LOCSER, 
               ,'N'     ---TB02055_TIPOCON, 
               ,NULL     ---TB02055_LOCPPROD, 
               ,TB02208_PAT     ---TB02055_PAT, 
               ,'TR'     ---TB02055_OPCAD, 
               ,NULL     ---TB02055_DTFABRICACA
               ,'05'     ---TB02055_STATUS, 
               ,'EM CONTRATO'     ---TB02055_NOMESTATUS,
               ,0     ---TB02055_MEDIDORPB, 
               ,0     ---TB02055_MEDIDORCOLO
               ,0     ---TB02055_MEDIDORTOTA
               ,0     ---TB02055_VALORUSD, 
               ,0     ---TB02055_PONTUACAO, 
               ,0     ---TB02055_FATOR, 

            FROM TB02019
			LEFT JOIN TB02018 ON TB02018_CODIGO = TB02019_CODIGO

            LEFT JOIN TB02030 ON TB02030_ORCAMENTO = TB02018_CODIGO

            LEFT JOIN TB02208 ON TB02208_PEDIDO = TB02030_CODIGO

            LEFT JOIN TB01039 ON TB01039_CODIGO = TB02030_STATUS
            WHERE TB02030_ORCAMENTO = @Orcamento


			
        END
        

        FETCH NEXT FROM APP_CONTRATOS_CURSOR INTO @Orcamento, @Status
    END

    -- Fechar e desalocar o cursor
    CLOSE APP_CONTRATOS_CURSOR
    DEALLOCATE APP_CONTRATOS_CURSOR
END
