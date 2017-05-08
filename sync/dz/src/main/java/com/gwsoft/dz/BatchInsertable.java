package com.gwsoft.dz;

import java.util.List;

public interface BatchInsertable<T> {

    public void batchInsert (List<T> list) throws AppException ;
    
    public void beforeInsert () throws AppException ;
    
    public void afterInsert () throws AppException ;
    
    public Class<T> getBean () ;
    
}
