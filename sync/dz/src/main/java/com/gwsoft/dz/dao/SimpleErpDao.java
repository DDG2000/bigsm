package com.gwsoft.dz.dao;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.gwsoft.dz.AppException;
import com.gwsoft.dz.Constant;

public abstract class SimpleErpDao<T> extends ErpBaseDao {
    
    private final Logger logger = LoggerFactory.getLogger(ErpBaseDao.class) ;
    
    protected abstract String getPk() ;
    
    protected abstract T buildBean (ResultSet rs) throws SQLException ;

    protected List<T> getBeanList (int start) throws AppException {
        DatabaseManager erpDbManager = this.getErpDbManager() ;
        Connection conn = null ;
        PreparedStatement ps = null ;
        ResultSet rs = null ;
        List<T> list = new ArrayList<T>() ;
        StringBuilder sb = new StringBuilder("select top ") ;
        sb.append(Constant.SYNC_SIZE).append(" * from ").append(this.getTable())
            .append(" where ").append(this.getPk()).append(" not in(select top ")
            .append(start).append(" ").append(this.getPk()).append(" from ").append(this.getTable())
            .append(" order by ").append(this.getPk()).append(") order by ").append(this.getPk());
        String sql = sb.toString() ;
        try {
            conn = erpDbManager.getConnection() ;
            ps = conn.prepareStatement(sql) ;
            rs = ps.executeQuery() ;
            while (rs.next()) {
                T bean = this.buildBean(rs) ;
                list.add(bean) ;
            }
        } catch (Exception e) {
            this.logger.error(e.getMessage());
            throw new AppException() ;
        } finally {
            erpDbManager.close(conn, ps, rs);
        }
        return list ;
    }
    
}
