package com.gwsoft.dz.dao.erp;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import com.gwsoft.dz.AppException;
import com.gwsoft.dz.BatchReadable;
import com.gwsoft.dz.dao.DatabaseManager;
import com.gwsoft.dz.dao.SimpleErpDao;
import com.gwsoft.dz.model.Employee;

@Repository
public class ErpEmployeeDao extends SimpleErpDao<Employee> implements BatchReadable<Employee> {

    @Autowired
    private DatabaseManager erpDbManager ;
    
    private static final String TABLE = "V_API_SysYW" ;

    public Integer getCount() throws AppException{
        return super.getCount() ;
    }

    public List<Employee> getList(int start) throws AppException {
        return this.getBeanList(start);
    }

    @Override
    protected DatabaseManager getErpDbManager() {
        return this.erpDbManager;
    }

    @Override
    protected String getTable() {
        return ErpEmployeeDao.TABLE;
    }

    @Override
    protected String getPk() {
        return "dm";
    }

    @Override
    protected Employee buildBean(ResultSet rs) throws SQLException {
        Employee employee = new Employee(rs.getString("dm"), rs.getString("xm")) ;
        return employee;
    }
    
}
