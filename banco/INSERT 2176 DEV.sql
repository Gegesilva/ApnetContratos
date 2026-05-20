
INSERT INTO [dbo].[TB02176]
           ([TB02176_DTCAD]
           ,[TB02176_OPCAD]
           ,[TB02176_DTALT]
           ,[TB02176_OPALT]
           ,[TB02176_CODIGO]
           ,[TB02176_CODCLI]
           ,[TB02176_CONTRATO]
           ,[TB02176_ID]
           ,[TB02176_NOME]
           ,[TB02176_END]
           ,[TB02176_NUM]
           ,[TB02176_BAIRRO]
           ,[TB02176_CIDADE]
           ,[TB02176_ESTADO]
           ,[TB02176_CEP]
           ,[TB02176_CONTATO]
           ,[TB02176_EMAIL]
           ,[TB02176_FONE]
           ,[TB02176_COMP]
           ,[TB02176_ESTOQUE]
           ,[TB02176_INTEGRA]
           ,[TB02176_VEICULO]
           ,[TB02176_CODEMP]
           ,[TB02176_CODCLIINT]
           ,[TB02176_TIPODESC]
           ,[TB02176_SITUACAO]
           ,[TB02176_DTINTEGRA]
           ,[TB02176_EXPEDIENTE]
           ,[TB02176_EXPCHKDOM]
           ,[TB02176_INIDOM]
           ,[TB02176_INTDOM]
           ,[TB02176_FIMINTDOM]
           ,[TB02176_FINDOM]
           ,[TB02176_EXPCHKSEG]
           ,[TB02176_INISEG]
           ,[TB02176_INTSEG]
           ,[TB02176_FIMINTSEG]
           ,[TB02176_FINSEG]
           ,[TB02176_EXPCHKTER]
           ,[TB02176_INITER]
           ,[TB02176_INTTER]
           ,[TB02176_FIMINTTER]
           ,[TB02176_FINTER]
           ,[TB02176_EXPCHKQUA]
           ,[TB02176_INIQUA]
           ,[TB02176_INTQUA]
           ,[TB02176_FIMINTQUA]
           ,[TB02176_FINQUA]
           ,[TB02176_EXPCHKQUI]
           ,[TB02176_INIQUI]
           ,[TB02176_INTQUI]
           ,[TB02176_FIMINTQUI]
           ,[TB02176_FINQUI]
           ,[TB02176_EXPCHKSEX]
           ,[TB02176_INISEX]
           ,[TB02176_INTSEX]
           ,[TB02176_FIMINTSEX]
           ,[TB02176_FINSEX]
           ,[TB02176_EXPCHKSAB]
           ,[TB02176_INISAB]
           ,[TB02176_INTSAB]
           ,[TB02176_FIMINTSAB]
           ,[TB02176_FINSAB])
     SELECT
           GETDATE()    --TB02176_DTCAD, smalldatetime,>
           ,'APP_CONTRATOS'  --TB02176_OPCAD, varchar(20),>
           ,NULL --TB02176_DTALT, smalldatetime,>
           ,NULL    --TB02176_OPALT, varchar(20),>
           ,'@NovCodSite'   --TB02176_CODIGO, varchar(6),>
           ,'@CodCli'   --TB02176_CODCLI, varchar(8),>
           ,'@ContratoNov    '--TB02176_CONTRATO, varchar(12),>
           ,NULL    --TB02176_ID, int,>
           ,TB02176_NOME    --TB02176_NOME, varchar(255),>
           ,TB02176_END --TB02176_END, varchar(60),>
           ,TB02176_NUM  --TB02176_NUM, int,>
           ,TB02176_BAIRRO  --TB02176_BAIRRO, varchar(30),>
           ,TB02176_CIDADE  --TB02176_CIDADE, varchar(30),>
           ,TB02176_ESTADO  --TB02176_ESTADO, varchar(2),>
           ,TB02176_CEP --TB02176_CEP, varchar(8),>
           ,TB02176_CONTATO   --TB02176_CONTATO, varchar(30),>
           ,TB02176_EMAIL   --TB02176_EMAIL, varchar(200),>
           ,TB02176_FONE    --TB02176_FONE, varchar(11),>
           ,TB02176_COMP    --TB02176_COMP, varchar(30),>
           ,TB02176_ESTOQUE --TB02176_ESTOQUE, int,>
           ,TB02176_INTEGRA --TB02176_INTEGRA, varchar(1),>
           ,TB02176_VEICULO --TB02176_VEICULO, varchar(7),>
           ,TB02176_CODEMP --TB02176_CODEMP, varchar(2),>
           ,TB02176_CODCLIINT --TB02176_CODCLIINT, varchar(8),>
           ,TB02176_TIPODESC --TB02176_TIPODESC, varchar(2),>
           ,TB02176_SITUACAO --TB02176_SITUACAO, varchar(1),>
           ,TB02176_DTINTEGRA --TB02176_DTINTEGRA, datetime,>
           ,TB02176_EXPEDIENTE --TB02176_EXPEDIENTE, varchar(1),>
           ,TB02176_EXPCHKDOM --TB02176_EXPCHKDOM, varchar(1),>
           ,TB02176_INIDOM --TB02176_INIDOM, varchar(5),>
           ,TB02176_INTDOM --TB02176_INTDOM, varchar(5),>
           ,TB02176_FIMINTDOM --TB02176_FIMINTDOM, varchar(5),>
           ,TB02176_FINDOM --TB02176_FINDOM, varchar(5),>
           ,TB02176_EXPCHKSEG --TB02176_EXPCHKSEG, varchar(1),>
           ,TB02176_INISEG --TB02176_INISEG, varchar(5),>
           ,TB02176_INTSEG --TB02176_INTSEG, varchar(5),>
           ,TB02176_FIMINTSEG --TB02176_FIMINTSEG, varchar(5),>
           ,TB02176_FINSEG --TB02176_FINSEG, varchar(5),>
           ,TB02176_EXPCHKTER --TB02176_EXPCHKTER, varchar(1),>
           ,TB02176_INITER --TB02176_INITER, varchar(5),>
           ,TB02176_INTTER --TB02176_INTTER, varchar(5),>
           ,TB02176_FIMINTTER --TB02176_FIMINTTER, varchar(5),>
           ,TB02176_FINTER --TB02176_FINTER, varchar(5),>
           ,TB02176_EXPCHKQUA --TB02176_EXPCHKQUA, varchar(1),>
           ,TB02176_INIQUA --TB02176_INIQUA, varchar(5),>
           ,TB02176_INTQUA --TB02176_INTQUA, varchar(5),>
           ,TB02176_FIMINTQUA --TB02176_FIMINTQUA, varchar(5),>
           ,TB02176_FINQUA --TB02176_FINQUA, varchar(5),>
           ,TB02176_EXPCHKQUI --TB02176_EXPCHKQUI, varchar(1),>
           ,TB02176_INIQUI --TB02176_INIQUI, varchar(5),>
           ,TB02176_INTQUI --TB02176_INTQUI, varchar(5),>
           ,TB02176_FIMINTQUI --TB02176_FIMINTQUI, varchar(5),>
           ,TB02176_FINQUI --TB02176_FINQUI, varchar(5),>
           ,TB02176_EXPCHKSEX --TB02176_EXPCHKSEX, varchar(1),>
           ,TB02176_INISEX --TB02176_INISEX, varchar(5),>
           ,TB02176_INTSEX --TB02176_INTSEX, varchar(5),>
           ,TB02176_FIMINTSEX --TB02176_FIMINTSEX, varchar(5),>
           ,TB02176_FINSEX --TB02176_FINSEX, varchar(5),>
           ,TB02176_EXPCHKSAB --TB02176_EXPCHKSAB, varchar(1),>
           ,TB02176_INISAB --TB02176_INISAB, varchar(5),>
           ,TB02176_INTSAB --TB02176_INTSAB, varchar(5),>
           ,TB02176_FIMINTSAB --TB02176_FIMINTSAB, varchar(5),>
           ,TB02176_FINSAB --TB02176_FINSAB, varchar(5),>
     FROM TB02176
     WHERE TB02176_CONTRATO = '1765'
     --AND TB02176_CODIGO IN (SELECT TB02112_CODSITE FROM TB02112 WHERE TB02112_PAT IN (SELECT PAT FROM @PatsTable))


