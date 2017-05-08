package com.gwsoft.dz.dao;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import com.gwsoft.dz.model.TimerConfig;

@Repository("timerConfigDao")
public class TimerConfigDao {
    
    private final Logger logger = LoggerFactory.getLogger(TimerConfigDao.class) ;

    @Autowired
    private DatabaseManager dzDbManager ;
    
    public List<TimerConfig> getConfigList() {
        List<TimerConfig> list = new ArrayList<TimerConfig>() ;
        Connection conn = null ;
        PreparedStatement ps = null ;
        ResultSet rs = null ;
        try {
            conn = dzDbManager.getConnection() ;
            ps = conn.prepareStatement("select * from sync_config where state = 1 ") ;
            rs = ps.executeQuery() ;
            while (rs.next()) {
                TimerConfig config = new TimerConfig(rs.getString("Vc_type"), 
                        rs.getInt("Vc_sync_hour"), rs.getInt("Vc_sync_min"), rs.getDate("Dt_last_sync")) ;
                list.add(config) ;
            }
        } catch (Exception e) {
            logger.error(e.getMessage());
        } finally {
            try {
                this.dzDbManager.close(conn, ps, rs);
            } catch (Exception e) {
                logger.error(e.getMessage());
            }
        }
        return list ;
    }
    
}
