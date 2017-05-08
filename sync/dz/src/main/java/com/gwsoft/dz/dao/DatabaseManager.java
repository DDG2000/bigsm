package com.gwsoft.dz.dao;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.Statement;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.gwsoft.dz.AppException;

public class DatabaseManager {
    
    private final Logger logger = LoggerFactory.getLogger(DatabaseManager.class) ;
    
    private String driver ;

    private String url ;
    
    private String user ;
    
    private String password ;
    
    public Connection getConnection() throws AppException {
        Connection conn = null ;
        try {
            Class.forName(this.driver);
            conn = DriverManager.getConnection(url,user,password) ;
        } catch (Exception e) {
            this.logger.error(e.getMessage());
            throw new AppException() ;
        }
        return conn ;
    }
    
    public void close (Connection conn , PreparedStatement ps , ResultSet rs) throws AppException {
        try {
            if (null != conn) {
                conn.close();
            }
            if (null != ps) {
                ps.close();
            }
            if (null != rs) {
                rs.close();
            }
        } catch (Exception e) {
            this.logger.error(e.getMessage());
            throw new AppException() ;
        }
    }
    
    public void close (Connection conn , PreparedStatement ps) throws AppException {
        this.close(conn, ps, null);
    }
    
    public void close (Connection conn) throws AppException {
        this.close(conn, null);
    }
    
    public void close (Connection conn , Statement st , ResultSet rs) throws AppException {
        try {
            if (null != conn) {
                conn.close();
            }
            if (null != st) {
                st.close();
            }
            if (null != rs) {
                rs.close();
            }
        } catch (Exception e) {
            this.logger.error(e.getMessage());
            throw new AppException() ;
        }
    }
    
    public void close (Connection conn , Statement st) throws AppException {
        this.close(conn, st, null);
    }

    public String getDriver() {
        return driver;
    }

    public void setDriver(String driver) {
        this.driver = driver;
    }

    public String getUrl() {
        return url;
    }

    public void setUrl(String url) {
        this.url = url;
    }

    public String getUser() {
        return user;
    }

    public void setUser(String user) {
        this.user = user;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }
    
}
