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
import com.gwsoft.dz.model.Company;

@Repository
public class DzCompanyDao extends DzBaseDao<Company> implements BatchInsertable<Company> {
    
    private final Logger logger = LoggerFactory.getLogger(DzCompanyDao.class) ;
    
    @Autowired
    private DatabaseManager dzDbManager ;

    public void batchInsert(List<Company> list) throws AppException {
        Connection conn = null ;
        PreparedStatement ps = null ;
        String sql = "insert into erp_company (Vc_companycode,Vc_name,Vc_shortname,Vc_helpcode,"
                + "Vc_area,Vc_country,I_issupplier,I_iscustomer,state,Createtime) "
                + "values (?,?,?,?,?,?,?,?,1,now()) on duplicate key update "
                + "Vc_companycode=?,Vc_name=?,Vc_shortname=?,Vc_helpcode=?,Vc_area=?,"
                + "Vc_country=?,I_issupplier=?,I_iscustomer=?" ;
        try {
            conn = dzDbManager.getConnection() ;
            ps = conn.prepareStatement(sql,ResultSet.TYPE_SCROLL_SENSITIVE,ResultSet.CONCUR_READ_ONLY) ;
            for (Company company : list) {
                ps.setString(1, company.getCode());
                ps.setString(2, company.getName());
                ps.setString(3, company.getShortName());
                ps.setString(4, company.getMark());
                ps.setString(5, company.getArea());
                ps.setString(6, company.getCountry());
                ps.setInt(7, company.getIsSupplier());
                ps.setInt(8, company.getIsCustomer());
                ps.setString(9, company.getCode());
                ps.setString(10, company.getName());
                ps.setString(11, company.getShortName());
                ps.setString(12, company.getMark());
                ps.setString(13, company.getArea());
                ps.setString(14, company.getCountry());
                ps.setInt(15, company.getIsSupplier());
                ps.setInt(16, company.getIsCustomer());
                ps.addBatch();
            }
            ps.executeBatch() ;
        } catch (Exception e) {
            logger.error(e.getMessage());
            throw new AppException() ;
        } finally {
            dzDbManager.close(conn, ps);
        }
    }

    public Class<Company> getBean() {
        return Company.class;
    }

}
