package com.gwsoft.dz.dao.dz;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.List;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import com.gwsoft.dz.AppException;
import com.gwsoft.dz.BatchInsertable;
import com.gwsoft.dz.dao.DatabaseManager;
import com.gwsoft.dz.dao.DzBaseDao;
import com.gwsoft.dz.model.Place;

@Repository
public class DzPlaceDao extends DzBaseDao<Place> implements BatchInsertable<Place> {
    
    private final Logger logger = LoggerFactory.getLogger(DzPlaceDao.class) ;

    @Autowired
    private DatabaseManager dzDbManager ;
    
    public void batchInsert(List<Place> list) throws AppException {
        Connection conn = null ;
        PreparedStatement ps = null ;
        String sql = "insert into erp_goods_factory (Vc_code,Vc_name,T_note,state,Createtime) "
                + "values (?,?,?,1,now()) on duplicate key update Vc_code=?,Vc_name=?,T_note=?" ;
        try {
            conn = dzDbManager.getConnection() ;
            ps = conn.prepareStatement(sql,ResultSet.TYPE_SCROLL_SENSITIVE,ResultSet.CONCUR_READ_ONLY) ;
            for (Place place : list) {
                ps.setString(1, place.getCode());
                ps.setString(2, place.getName());
                ps.setString(3, place.getName());
                ps.setString(4, place.getCode());
                ps.setString(5, place.getName());
                ps.setString(6, place.getName());
                ps.addBatch();
            }
            ps.executeBatch() ;
        } catch (Exception e) {
            logger.error(e.getMessage());
            throw new AppException() ;
        } finally {
            this.dzDbManager.close(conn, ps);
        }
    }

    public Class<Place> getBean() {
        return Place.class;
    }

}
