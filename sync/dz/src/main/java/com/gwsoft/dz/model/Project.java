package com.gwsoft.dz.model;

import java.sql.Date;

public class Project {

    public Project(String code, String contractNumber, Date signDate, String requireCompany, String projectName,
            String projectAddress, float contractWeight, float contractAmount, float maxAmount, float maxTime,
            String deliverType, String area, String employee, String note, float quota, String raiseCondition,
            String blame) {
        super();
        this.code = code;
        this.contractNumber = contractNumber;
        this.signDate = signDate;
        this.requireCompany = requireCompany;
        this.projectName = projectName;
        this.projectAddress = projectAddress;
        this.contractWeight = contractWeight;
        this.contractAmount = contractAmount;
        this.maxAmount = maxAmount;
        this.maxTime = maxTime;
        this.deliverType = deliverType;
        this.area = area;
        this.employee = employee;
        this.note = note;
        this.quota = quota ;
        this.raiseCondition = raiseCondition ;
        this.blame = blame ;
    }

    /**
     * 代码
     */
    private String code ;
    /**
     * 合同编号
     */
    private String contractNumber ;
    /**
     * 签约日期
     */
    private Date signDate ;
    /**
     * 需方公司
     */
    private String requireCompany ;
    /**
     * 项目名称
     */
    private String projectName ;
    /**
     * 项目地址
     */
    private String projectAddress ;
    /**
     * 合同总量
     */
    private float contractWeight ;
    /**
     * 合同金额
     */
    private float contractAmount ;
    /**
     * 最高垫资金额
     */
    private float maxAmount ;
    /**
     * 最长垫资时间
     */
    private float maxTime ;
    /**
     * 交货方式
     */
    private String deliverType ;
    /**
     * 产地范围
     */
    private String area ;
    /**
     * 业务员
     */
    private String employee ;
    /**
     * 备注
     */
    private String note ;
    /**
     * 可用额度
     */
    private float quota ;
    /**
     * 加价条件
     */
    private String raiseCondition ;
    /**
     * 违约责任
     */
    private String blame ;

    public String getCode() {
        return code;
    }

    public void setCode(String code) {
        this.code = code;
    }

    public String getContractNumber() {
        return contractNumber;
    }

    public void setContractNumber(String contractNumber) {
        this.contractNumber = contractNumber;
    }

    public Date getSignDate() {
        return signDate;
    }

    public void setSignDate(Date signDate) {
        this.signDate = signDate;
    }

    public String getRequireCompany() {
        return requireCompany;
    }

    public void setRequireCompany(String requireCompany) {
        this.requireCompany = requireCompany;
    }

    public String getProjectName() {
        return projectName;
    }

    public void setProjectName(String projectName) {
        this.projectName = projectName;
    }

    public String getProjectAddress() {
        return projectAddress;
    }

    public void setProjectAddress(String projectAddress) {
        this.projectAddress = projectAddress;
    }

    public float getContractWeight() {
        return contractWeight;
    }

    public void setContractWeight(float contractWeight) {
        this.contractWeight = contractWeight;
    }

    public float getContractAmount() {
        return contractAmount;
    }

    public void setContractAmount(float contractAmount) {
        this.contractAmount = contractAmount;
    }

    public float getMaxAmount() {
        return maxAmount;
    }

    public void setMaxAmount(float maxAmount) {
        this.maxAmount = maxAmount;
    }

    public float getMaxTime() {
        return maxTime;
    }

    public void setMaxTime(float maxTime) {
        this.maxTime = maxTime;
    }

    public String getDeliverType() {
        return deliverType;
    }

    public void setDeliverType(String deliverType) {
        this.deliverType = deliverType;
    }

    public String getArea() {
        return area;
    }

    public void setArea(String area) {
        this.area = area;
    }

    public String getEmployee() {
        return employee;
    }

    public void setEmployee(String employee) {
        this.employee = employee;
    }

    public String getNote() {
        return note;
    }

    public void setNote(String note) {
        this.note = note;
    }

    public float getQuota() {
        return quota;
    }

    public void setQuota(float quota) {
        this.quota = quota;
    }

    public String getRaiseCondition() {
        return raiseCondition;
    }

    public void setRaiseCondition(String raiseCondition) {
        this.raiseCondition = raiseCondition;
    }

    public String getBlame() {
        return blame;
    }

    public void setBlame(String blame) {
        this.blame = blame;
    }
    
}
