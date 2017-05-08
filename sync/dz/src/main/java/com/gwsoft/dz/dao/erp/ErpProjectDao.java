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
import com.gwsoft.dz.model.Project;

@Repository
public class ErpProjectDao extends SimpleErpDao<Project> implements BatchReadable<Project> {
    
    @Autowired
    private DatabaseManager erpDbManager ;

    @Override
    public List<Project> getList(int start) throws AppException {
        return super.getBeanList(start);
    }

    @Override
    protected String getPk() {
        return "代码";
    }

    @Override
    protected Project buildBean(ResultSet rs) throws SQLException {
        Project project = new Project(rs.getString("代码"), rs.getString("合同编号"), 
                rs.getDate("签约日期"), rs.getString("需方公司"), rs.getString("项目名称"), 
                rs.getString("项目地址"), rs.getFloat("合同总量"), rs.getFloat("合同总金额"), 
                rs.getFloat("最高垫资金额"), rs.getFloat("最长垫资时间"), rs.getString("交货方式"), 
                rs.getString("产地范围"), rs.getString("业务员"), rs.getString("备注"),
                rs.getFloat("可用额度"),rs.getString("加价条件"),rs.getString("违约责任")) ;
        return project;
    }

    @Override
    protected DatabaseManager getErpDbManager() {
        return this.erpDbManager;
    }

    @Override
    protected String getTable() {
        return "V_API_SysHT";
    }

}
