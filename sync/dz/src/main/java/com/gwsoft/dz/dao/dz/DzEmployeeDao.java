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
import com.gwsoft.dz.model.Employee;

@Repository
public class DzEmployeeDao extends DzBaseDao<Employee> implements BatchInsertable<Employee> {
    
    private final Logger logger = LoggerFactory.getLogger(DzEmployeeDao.class) ;

    @Autowired
    private DatabaseManager dzDbManager ;

    public void batchInsert(List<Employee> list) throws AppException {
        Connection conn = null ;
        PreparedStatement ps = null ;
        String sql = "insert into erp_emp (Vc_code,Vc_name,state,Createtime) "
                + "values (?,?,1,now()) on duplicate key update Vc_code=?,Vc_name=?" ;
        try {
            conn = this.dzDbManager.getConnection() ;
            ps = conn.prepareStatement(sql,ResultSet.TYPE_SCROLL_SENSITIVE,ResultSet.CONCUR_READ_ONLY) ;
            for (Employee employee : list) {
                ps.setString(1, employee.getCode());
                ps.setString(2, employee.getName());
                ps.setString(3, employee.getCode());
                ps.setString(4, employee.getName());
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

    public Class<Employee> getBean() {
        return Employee.class;
    }

}
