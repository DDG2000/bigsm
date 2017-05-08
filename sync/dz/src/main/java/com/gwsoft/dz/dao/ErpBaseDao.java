package com.gwsoft.dz.dao;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.gwsoft.dz.AppException;

public abstract class ErpBaseDao {
    
    private final Logger logger = LoggerFactory.getLogger(ErpBaseDao.class) ;
    
    protected abstract DatabaseManager getErpDbManager() ;
    
    protected abstract String getTable() ;
    
    public Integer getCount() throws AppException {
        Connection conn = null ;
        PreparedStatement ps = null ;
        ResultSet rs = null ;
        try {
            conn = getErpDbManager().getConnection() ;
            ps = conn.prepareStatement("select count(*) from " + getTable()) ;
            rs = ps.executeQuery() ;
            if (rs.next()) {
                return rs.getInt(1) ;
            }
        } catch (SQLException e) {
            this.logger.error(e.getMessage());
            throw new AppException() ;
        } finally {
            this.getErpDbManager().close(conn, ps);
        }
        return 0 ;
    }

}
