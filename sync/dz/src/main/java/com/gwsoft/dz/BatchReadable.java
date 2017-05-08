package com.gwsoft.dz;

import java.util.List;

public interface BatchReadable<T> {

    public Integer getCount() throws AppException;
    
    public List<T> getList(int start) throws AppException;
    
}
