package com.gwsoft.dz.model;

public class Place {

    public Place(String code, String name, String note) {
        super();
        this.code = code;
        this.name = name;
        this.note = note;
    }

    private Integer id ;
    
    private String code ;
    
    private String name ;
    
    private String note ;

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

    public String getNote() {
        return note;
    }

    public void setNote(String note) {
        this.note = note;
    }
    
}
