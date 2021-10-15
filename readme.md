### 进出口综合贸易申报工具类
本工具仅对接直属与海关的机构, 第三方机构的对接实现不集成在此。

本工具实现上层业务在特定关区所需要的 [地域海关节点] 所出具的 [各类型贸易] + [各种报关单据] 的对接报文组装。

支付单和运单目前不属于直属海关的报关结构, 不在此实现。

申报工具目前对接了以下通路服务:
* 1、海关总署 -- 跨境CEB报文
* 2、广州单一窗口 -- 跨境进出口KJ报文
* 3、中国单一窗口 -- 非跨境报文
* 4、江西综服 -- 跨境进口报文

### 申报工具目前服务清单 
TO ADD 持续增加..

* ArrivalExportService 海关直连总署 - CEB报文 - 出口运抵单
* CancelDeclareExportService 海关直连总署 - CEB报文 - 出口撤销申请单
* ChecklistCrossImportService  海关直连总署 - CEB报文 - 进口清单申报
* ChecklistCrossService  广州单一窗口 - KJ报文 - 进出口清单申报
* DeclareListExportService 海关直连总署 - CEB报文 - 出口申报清单.
* DepartureOrderExportService  海关直连总署 - CEB报文 - 出口离境单.
* ElectronicOrderExportService 海关直连总署 - CEB报文 - 出口电子订单申报.
* GoodsCrossService  广州单一窗口 - KJ报文 - 进出口商品申报.
* GoodsLoadCrossService  广州单一窗口 - KJ报文 - 进出口装载单申报.
* InboundCrossService 广州单一窗口 - KJ报文 - 进出口入仓单申报.
* JxCrossImportService 江西进口申报 - 地方报文 - 进口订单申报.
* JxImportListService 江西进口申报 - 地方报文 - 进口清单申报.
* OrderCrossImportService 海关直连总署 - CEB报文 - 进口订单申报.
* OrderCrossService 广州单一窗口 - KJ报文 - 进出口订单申报.
* OrderExpressImportService 中国单一窗口 - 快件报文 - 进口快件订单申报.
* PayReceiveCrossExportService 海关直连总署 - CEB报文 - 出口收款单申报.
* SummaryBillExportService 海关直连总署 - CEB报文 - 出口汇总单.
* TransportBillExportService 海关直连总署 - CEB报文 - 出口运单申报
* TransportBillImportService 海关直连总署 - CEB报文 - 进口运单申报
* TotalDeclareListExportService 海关直连总署 - CEB报文 - 出口清单总分单