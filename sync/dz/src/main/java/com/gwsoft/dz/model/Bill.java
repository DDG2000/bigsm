package com.gwsoft.dz.model;

import java.util.Date;

public class Bill {

    public Bill(String industry, String projectNumber, String projectName, String orderNumber, String category1,
            String category2, String goodsName, String goodsMaterial, String goodsStandard, String goodsAddress,
            float receiveWeight, float realitySettlementPrice, Date receiveDate, Date paybackDate, float investPrice,
            int investDays, float investInterest, float settlementPrice, String status) {
        super();
        this.industry = industry;
        this.projectNumber = projectNumber;
        this.projectName = projectName;
        this.orderNumber = orderNumber;
        this.category1 = category1;
        this.category2 = category2;
        this.goodsName = goodsName;
        this.goodsMaterial = goodsMaterial;
        this.goodsStandard = goodsStandard;
        this.goodsAddress = goodsAddress;
        this.receiveWeight = receiveWeight;
        this.realitySettlementPrice = realitySettlementPrice;
        this.receiveDate = receiveDate;
        this.paybackDate = paybackDate;
        this.investPrice = investPrice;
        this.investDays = investDays;
        this.investInterest = investInterest;
        this.settlementPrice = settlementPrice;
        this.status = status;
    }
    /**
     * 所属行业
     */
    private String industry ;
    /**
     * 项目编号
     */
    private String projectNumber ;
    /**
     * 所属项目
     */
    private String projectName ;
    /**
     * 订单号
     */
    private String orderNumber ;
    /**
     * 物资分类
     */
    private String category1 ;
    /**
     * 物资大类
     */
    private String category2 ;
    /**
     * 品名
     */
    private String goodsName ;
    /**
     * 材质
     */
    private String goodsMaterial ;
    /**
     * 规格
     */
    private String goodsStandard ;
    /**
     * 产地
     */
    private String goodsAddress ;
    /**
     * 到货重量
     */
    private float receiveWeight ;
    /**
     * 实际结算金额
     */
    private float realitySettlementPrice ;
    /**
     * 到货日期
     */
    private Date receiveDate ;
    /**
     * 还款日期
     */
    private Date paybackDate ;
    /**
     * 垫资金额
     */
    private float investPrice ;
    /**
     * 垫资天数
     */
    private int investDays ;
    /**
     * 垫资利息
     */
    private float investInterest ;
    /**
     * 结算金额
     */
    private float settlementPrice ;
    /**
     * 账单状态
     */
    private String status ;
    public String getIndustry() {
        return industry;
    }
    public void setIndustry(String industry) {
        this.industry = industry;
    }
    public String getProjectNumber() {
        return projectNumber;
    }
    public void setProjectNumber(String projectNumber) {
        this.projectNumber = projectNumber;
    }
    public String getProjectName() {
        return projectName;
    }
    public void setProjectName(String projectName) {
        this.projectName = projectName;
    }
    public String getOrderNumber() {
        return orderNumber;
    }
    public void setOrderNumber(String orderNumber) {
        this.orderNumber = orderNumber;
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
    public String getGoodsName() {
        return goodsName;
    }
    public void setGoodsName(String goodsName) {
        this.goodsName = goodsName;
    }
    public String getGoodsMaterial() {
        return goodsMaterial;
    }
    public void setGoodsMaterial(String goodsMaterial) {
        this.goodsMaterial = goodsMaterial;
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
    public float getReceiveWeight() {
        return receiveWeight;
    }
    public void setReceiveWeight(float receiveWeight) {
        this.receiveWeight = receiveWeight;
    }
    public float getRealitySettlementPrice() {
        return realitySettlementPrice;
    }
    public void setRealitySettlementPrice(float realitySettlementPrice) {
        this.realitySettlementPrice = realitySettlementPrice;
    }
    public Date getReceiveDate() {
        return receiveDate;
    }
    public void setReceiveDate(Date receiveDate) {
        this.receiveDate = receiveDate;
    }
    public Date getPaybackDate() {
        return paybackDate;
    }
    public void setPaybackDate(Date paybackDate) {
        this.paybackDate = paybackDate;
    }
    public float getInvestPrice() {
        return investPrice;
    }
    public void setInvestPrice(float investPrice) {
        this.investPrice = investPrice;
    }
    public int getInvestDays() {
        return investDays;
    }
    public void setInvestDays(int investDays) {
        this.investDays = investDays;
    }
    public float getInvestInterest() {
        return investInterest;
    }
    public void setInvestInterest(float investInterest) {
        this.investInterest = investInterest;
    }
    public float getSettlementPrice() {
        return settlementPrice;
    }
    public void setSettlementPrice(float settlementPrice) {
        this.settlementPrice = settlementPrice;
    }
    public String getStatus() {
        return status;
    }
    public void setStatus(String status) {
        this.status = status;
    }
    
}
 