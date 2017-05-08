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
import com.gwsoft.dz.model.Company;

@Repository
public class ErpCompanyDao extends SimpleErpDao<Company> implements BatchReadable<Company> {
    
    private static final String TABLE = "V_API_SysDW" ;
    
    @Autowired
    private DatabaseManager erpDbManager ;
    
    public List<Company> getList(int start) throws AppException {
        return this.getBeanList(start);
    }

    @Override
    protected String getPk() {
        return "dwdm";
    }

    @Override
    protected Company buildBean(ResultSet rs) throws SQLException {
        Company company = new Company(rs.getString("dwdm"), rs.getString("country"), 
                rs.getString("dwmc"), rs.getString("dqmc") , rs.getString("dwjc"), 
                rs.getString("pydm"), rs.getInt("isgys"), rs.getInt("iskh")) ;
        return company;
    }

    @Override
    protected DatabaseManager getErpDbManager() {
        return this.erpDbManager;
    }

    @Override
    protected String getTable() {
        return ErpCompanyDao.TABLE;
    }

}
