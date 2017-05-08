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
import com.gwsoft.dz.model.Place;

@Repository
public class ErpPlaceDao extends SimpleErpDao<Place> implements BatchReadable<Place> {
    
    private static final String TABLE = "V_API_SysCD" ;
    
    @Autowired
    private DatabaseManager erpDbManager ;
    
    public Integer getCount() throws AppException {
        return super.getCount() ;
    }

    public List<Place> getList(int start) throws AppException {
        return this.getBeanList(start);
    }

    @Override
    protected DatabaseManager getErpDbManager() {
        return erpDbManager;
    }

    @Override
    protected String getTable() {
        return ErpPlaceDao.TABLE;
    }

    @Override
    protected String getPk() {
        return "dm";
    }

    @Override
    protected Place buildBean(ResultSet rs) throws SQLException {
        Place place = new Place(rs.getString("dm"), rs.getString("mc"), rs.getString("bz")) ;
        return place;
    }

}
