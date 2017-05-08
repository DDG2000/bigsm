package com.gwsoft.dz.model;

public class Employee {
    
    public Employee(String code, String name) {
        super();
        this.code = code;
        this.name = name;
    }

    private Integer id ;
    
    private String code ;
    
    private String name ;
    
    private String address ;
    
    private String phone ;
    
    private String department ;
    
    private String fgs ;
    
    private String position ;

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

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public String getDepartment() {
        return department;
    }

    public void setDepartment(String department) {
        this.department = department;
    }

    public String getFgs() {
        return fgs;
    }

    public void setFgs(String fgs) {
        this.fgs = fgs;
    }

    public String getPosition() {
        return position;
    }

    public void setPosition(String position) {
        this.position = position;
    }

}
