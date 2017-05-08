package com.gwsoft.dz.dao;

import java.sql.Connection;
import java.sql.SQLException;
import java.sql.Statement;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.gwsoft.dz.AppException;

public abstract class DzTableReplaceDao<T> extends DzBaseDao<T> {
    
    private final Logger logger = LoggerFactory.getLogger(DzBaseDao.class) ;

    @Override
    public void beforeInsert() throws AppException {
        String dropSql = "DROP TABLE IF EXISTS " + this.getTempTableName() ;
        String createSql = this.getCreateTempTableSql() ;
        DatabaseManager dbm = this.getDbManager() ;
        Connection conn = null ;
        Statement st = null ;
        try {
            conn = dbm.getConnection() ;
            st = conn.createStatement() ;
            st.addBatch(dropSql);
            st.addBatch(createSql);
            st.executeBatch() ;
        } catch (SQLException e) {
            this.logger.error(e.getMessage());
            throw new AppException() ;
        } finally {
            dbm.close(conn, st);
        }
    }

    @Override
    public void afterInsert() throws AppException {
        String tn = this.getTableName() ;
        String ttn = this.getTempTableName() ;
        String btn = this.getBackupTableName() ;
        String dropBackupSql = "DROP TABLE IF EXISTS " + btn ;
        String renameSql = "RENAME TABLE " + tn + " TO " + btn + 
                " , " + ttn + " TO " + tn + " , " + btn + " TO " + ttn;
        DatabaseManager dbm = this.getDbManager() ;
        Connection conn = null ;
        Statement st = null ;
        try {
            conn = dbm.getConnection() ;
            st = conn.createStatement() ;
            st.addBatch(dropBackupSql);
            st.addBatch(renameSql);
            st.executeBatch() ;
        } catch (SQLException e) {
            this.logger.error(e.getMessage());
            throw new AppException() ;
        } finally {
            dbm.close(conn, st);
        }
    }
    
    protected String getBackupTableName() {
        return this.getTableName() + "_bak" ;
    }
    
    protected String getTempTableName () {
        return this.getTableName() + "_tmp" ;
    };
    
    protected abstract String getCreateTempTableSql () ;
    
    protected abstract String getTableName () ;
    
    protected abstract DatabaseManager getDbManager()  ;
    
}
