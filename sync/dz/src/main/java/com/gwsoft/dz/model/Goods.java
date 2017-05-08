package com.gwsoft.dz.model;

public class Goods {

    public Goods(String code, String category1, String category2, String category3, String name, String material,
            String standard, String address, String unit, String mark, String remark) {
        super();
        this.code = code;
        this.category1 = category1;
        this.category2 = category2;
        this.category3 = category3;
        this.name = name;
        this.material = material;
        this.standard = standard;
        this.address = address;
        this.unit = unit;
        this.mark = mark;
        this.remark = remark ;
    }
    /**
     * 代码
     */
    private String code ;
    /**
     * 分类1
     */
    private String category1 ;
    /**
     * 分类2
     */
    private String category2 ;
    /**
     * 分类3
     */
    private String category3 ;
    /**
     * 品名
     */
    private String name ;
    /**
     * 材质
     */
    private String material ;
    /**
     * 规格
     */
    private String standard ;
    /**
     * 产地
     */
    private String address ;
    /**
     * 计量单位
     */
    private String unit ;
    /**
     * 助记码
     */
    private String mark ;
    /**
     * 备注
     */
    private String remark ;
    public String getCode() {
        return code;
    }
    public void setCode(String code) {
        this.code = code;
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
    public String getCategory3() {
        return category3;
    }
    public void setCategory3(String category3) {
        this.category3 = category3;
    }
    public String getName() {
        return name;
    }
    public void setName(String name) {
        this.name = name;
    }
    public String getMaterial() {
        return material;
    }
    public void setMaterial(String material) {
        this.material = material;
    }
    public String getStandard() {
        return standard;
    }
    public void setStandard(String standard) {
        this.standard = standard;
    }
    public String getAddress() {
        return address;
    }
    public void setAddress(String address) {
        this.address = address;
    }
    public String getUnit() {
        return unit;
    }
    public void setUnit(String unit) {
        this.unit = unit;
    }
    public String getMark() {
        return mark;
    }
    public void setMark(String mark) {
        this.mark = mark;
    }
    public String getRemark() {
        return remark;
    }
    public void setRemark(String remark) {
        this.remark = remark;
    }
    
}
