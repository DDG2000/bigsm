package com.gwsoft.dz.model;

import java.util.Date;

public class PurchaseProof {

    public PurchaseProof(String orderno, Date orderDate, Date orderAuditDate, Date purchaseDate, Date takeDate,
            Date dispatchDate, Date receiveDate, String projectCode, String projectName, String transportCompany,
            String transportNumber, String receiveName, String receivePhone, String transportName,
            String transportPhone, String startAddress, String endAddress, String carNumber, String orderStatus,
            String goodsCategory1, String goodsCategory2, String goodsName, String goodsMeterial, String goodsStandard,
            String goodsAddress, String goodsUnit, float planWeight, float weight, float price, float amount,
            String category1, String category2) {
        super();
        this.orderno = orderno;
        this.orderDate = orderDate;
        this.orderAuditDate = orderAuditDate;
        this.purchaseDate = purchaseDate;
        this.takeDate = takeDate;
        this.dispatchDate = dispatchDate;
        this.receiveDate = receiveDate;
        this.projectCode = projectCode;
        this.projectName = projectName;
        this.transportCompany = transportCompany;
        this.transportNumber = transportNumber;
        this.receiveName = receiveName;
        this.receivePhone = receivePhone;
        this.transportName = transportName;
        this.transportPhone = transportPhone;
        this.startAddress = startAddress;
        this.endAddress = endAddress;
        this.carNumber = carNumber;
        this.orderStatus = orderStatus;
        this.goodsCategory1 = goodsCategory1;
        this.goodsCategory2 = goodsCategory2;
        this.goodsName = goodsName;
        this.goodsMeterial = goodsMeterial;
        this.goodsStandard = goodsStandard;
        this.goodsAddress = goodsAddress;
        this.goodsUnit = goodsUnit;
        this.planWeight = planWeight;
        this.weight = weight;
        this.price = price;
        this.amount = amount;
        this.category1 = category1 ;
        this.category2 = category2 ;
    }
    /**
     * 订单号
     */
    private String orderno ;
    /**
     * 订单日期
     */
    private Date orderDate ;
    /**
     * 订单审核日期
     */
    private Date orderAuditDate ;
    /**
     * 采购日期
     */
    private Date purchaseDate ;
    /**
     * 提货日期
     */
    private Date takeDate ;
    /**
     * 发货日期
     */
    private Date dispatchDate ;
    /**
     * 到货日期
     */
    private Date receiveDate ;
    /**
     * 项目编号
     */
    private String projectCode ;
    /**
     * 项目名称
     */
    private String projectName ;
    /**
     * 物流公司
     */
    private String transportCompany ;
    /**
     * 运单号
     */
    private String transportNumber ;
    /**
     * 收货联系人
     */
    private String receiveName ;
    /**
     * 收获联系人电话
     */
    private String receivePhone ;
    /**
     * 承运人
     */
    private String transportName ;
    /**
     * 承运人电话
     */
    private String transportPhone ;
    /**
     * 发货地址
     */
    private String startAddress ;
    /**
     * 收货地址
     */
    private String endAddress;
    /**
     * 送货车牌
     */
    private String carNumber ;
    /**
     * 订单状态
     */
    private String orderStatus ;
    /**
     * 物资分类
     */
    private String goodsCategory1 ;
    /**
     * 物资大类
     */
    private String goodsCategory2 ;
    /**
     * 品名
     */
    private String goodsName ;
    /**
     * 材质
     */
    private String goodsMeterial ;
    /**
     * 规格
     */
    private String goodsStandard ;
    /**
     * 产地
     */
    private String goodsAddress ;
    /**
     * 单位
     */
    private String goodsUnit ;
    /**
     * 计划重量
     */
    private float planWeight ;
    /**
     * 实际重量
     */
    private float weight ;
    /**
     * 实提单价
     */
    private float price ;
    /**
     * 实提结算金额
     */
    private float amount ;
    /**
     * 产品类型
     */
    private String category1 ;
    /**
     * 物资大类
     */
    private String category2 ;
    
    public String getOrderno() {
        return orderno;
    }
    public void setOrderno(String orderno) {
        this.orderno = orderno;
    }
    public Date getOrderDate() {
        return orderDate;
    }
    public void setOrderDate(Date orderDate) {
        this.orderDate = orderDate;
    }
    public Date getOrderAuditDate() {
        return orderAuditDate;
    }
    public void setOrderAuditDate(Date orderAuditDate) {
        this.orderAuditDate = orderAuditDate;
    }
    public Date getPurchaseDate() {
        return purchaseDate;
    }
    public void setPurchaseDate(Date purchaseDate) {
        this.purchaseDate = purchaseDate;
    }
    public Date getTakeDate() {
        return takeDate;
    }
    public void setTakeDate(Date takeDate) {
        this.takeDate = takeDate;
    }
    public Date getDispatchDate() {
        return dispatchDate;
    }
    public void setDispatchDate(Date dispatchDate) {
        this.dispatchDate = dispatchDate;
    }
    public Date getReceiveDate() {
        return receiveDate;
    }
    public void setReceiveDate(Date receiveDate) {
        this.receiveDate = receiveDate;
    }
    public String getProjectCode() {
        return projectCode;
    }
    public void setProjectCode(String projectCode) {
        this.projectCode = projectCode;
    }
    public String getProjectName() {
        return projectName;
    }
    public void setProjectName(String projectName) {
        this.projectName = projectName;
    }
    public String getTransportCompany() {
        return transportCompany;
    }
    public void setTransportCompany(String transportCompany) {
        this.transportCompany = transportCompany;
    }
    public String getTransportNumber() {
        return transportNumber;
    }
    public void setTransportNumber(String transportNumber) {
        this.transportNumber = transportNumber;
    }
    public String getReceiveName() {
        return receiveName;
    }
    public void setReceiveName(String receiveName) {
        this.receiveName = receiveName;
    }
    public String getReceivePhone() {
        return receivePhone;
    }
    public void setReceivePhone(String receivePhone) {
        this.receivePhone = receivePhone;
    }
    public String getTransportName() {
        return transportName;
    }
    public void setTransportName(String transportName) {
        this.transportName = transportName;
    }
    public String getTransportPhone() {
        return transportPhone;
    }
    public void setTransportPhone(String transportPhone) {
        this.transportPhone = transportPhone;
    }
    public String getStartAddress() {
        return startAddress;
    }
    public void setStartAddress(String startAddress) {
        this.startAddress = startAddress;
    }
    public String getEndAddress() {
        return endAddress;
    }
    public void setEndAddress(String endAddress) {
        this.endAddress = endAddress;
    }
    public String getCarNumber() {
        return carNumber;
    }
    public void setCarNumber(String carNumber) {
        this.carNumber = carNumber;
    }
    public String getOrderStatus() {
        return orderStatus;
    }
    public void setOrderStatus(String orderStatus) {
        this.orderStatus = orderStatus;
    }
    public String getGoodsCategory1() {
        return goodsCategory1;
    }
    public void setGoodsCategory1(String goodsCategory1) {
        this.goodsCategory1 = goodsCategory1;
    }
    public String getGoodsCategory2() {
        return goodsCategory2;
    }
    public void setGoodsCategory2(String goodsCategory2) {
        this.goodsCategory2 = goodsCategory2;
    }
    public String getGoodsName() {
        return goodsName;
    }
    public void setGoodsName(String goodsName) {
        this.goodsName = goodsName;
    }
    public String getGoodsMeterial() {
        return goodsMeterial;
    }
    public void setGoodsMeterial(String goodsMeterial) {
        this.goodsMeterial = goodsMeterial;
    }
    public String getGoodsStandard() {
        return goodsStandard;
    }
    public void setGoodsStandard(String goodsStandard) {
        this.goodsStandard = goodsStandard;
    }
    public String getGoodsAddress() {
        return goodsAddress;
    }
    public void setGoodsAddress(String goodsAddress) {
        this.goodsAddress = goodsAddress;
    }
    public String getGoodsUnit() {
        return goodsUnit;
    }
    public void setGoodsUnit(String goodsUnit) {
        this.goodsUnit = goodsUnit;
    }
    public float getPlanWeight() {
        return planWeight;
    }
    public void setPlanWeight(float planWeight) {
        this.planWeight = planWeight;
    }
    public float getWeight() {
        return weight;
    }
    public void setWeight(float weight) {
        this.weight = weight;
    }
    public float getPrice() {
        return price;
    }
    public void setPrice(float price) {
        this.price = price;
    }
    public float getAmount() {
        return amount;
    }
    public void setAmount(float amount) {
        this.amount = amount;
    }
    public String getCategory1() {
        return category1;
    }
    public void setCategory1(String category1) {
        this.category1 = category1;
    }
    public String getCategory2() {
        return category2;
    }
    public void setCategory2(String category2) {
        this.category2 = category2;
    }
    
    
}
