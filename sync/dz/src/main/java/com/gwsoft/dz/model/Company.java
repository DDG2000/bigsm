package com.gwsoft.dz.model;

public class Company {

    public Company(String code, String country, String name, String area,
            String shortName, String mark, Integer isSupplier, Integer isCustomer) {
        super();
        this.code = code;
        this.country = country;
        this.name = name;
        this.area = area;
        this.shortName = shortName;
        this.mark = mark;
        this.isSupplier = isSupplier;
        this.isCustomer = isCustomer;
    }

    private Integer id ;
    
    private String code ;
    
    private String country ;
    
    private String area ;
    
    private String name ;
    
    private String shortName ;
    
    private String mark ;
    
    private Integer isSupplier ;
    
    private Integer isCustomer ;

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        this.id = id;
    }

    public String getCode() {
        return code;
    }

    public void setCode(String code) {
        this.code = code;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getShortName() {
        return shortName;
    }

    public void setShortName(String shortName) {
        this.shortName = shortName;
    }

    public String getMark() {
        return mark;
    }

    public void setMark(String mark) {
        this.mark = mark;
    }

    public Integer getIsSupplier() {
        return isSupplier;
    }

    public void setIsSupplier(Integer isSupplier) {
        this.isSupplier = isSupplier;
    }

    public Integer getIsCustomer() {
        return isCustomer;
    }

    public void setIsCustomer(Integer isCustomer) {
        this.isCustomer = isCustomer;
    }

    public String getArea() {
        return area;
    }

    public void setArea(String area) {
        this.area = area;
    }
    
}
