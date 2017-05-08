package com.gwsoft.dz.dao;

import com.gwsoft.dz.AppException;
import com.gwsoft.dz.BatchInsertable;

public abstract class DzBaseDao<T> implements BatchInsertable<T> {

    @Override
    public void beforeInsert() throws AppException {}

    @Override
    public void afterInsert() throws AppException {}

    
    
}
