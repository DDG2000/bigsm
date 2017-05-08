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
import com.gwsoft.dz.model.Goods;

@Repository
public class ErpGoodsDao extends SimpleErpDao<Goods> implements BatchReadable<Goods> {
    
    @Autowired
    private DatabaseManager erpDbManager ;
    
    private static final String TABLE = "V_API_SysWZ" ;
    
    public Integer getCount() throws AppException {
        return super.getCount() ;
    }

    public List<Goods> getList (int start) throws AppException {
        return this.getBeanList(start) ;
    }

    @Override
    protected String getTable() {
        return ErpGoodsDao.TABLE ;
    }

    @Override
    protected DatabaseManager getErpDbManager() {
        return this.erpDbManager;
    }

    @Override
    protected String getPk() {
        return "dm_";
    }

    @Override
    protected Goods buildBean(ResultSet rs) throws SQLException {
        Goods goods = new Goods(rs.getString("dm_"), rs.getString("wzstr1_"), rs.getString("wzstr2_"), 
                "", rs.getString("pm_"), rs.getString("cz_"), rs.getString("gg_"), 
                rs.getString("cd_"), rs.getString("jldw1_"), rs.getString("pydm_"), rs.getString("bz_")) ;
        return goods;
    }
    
}
