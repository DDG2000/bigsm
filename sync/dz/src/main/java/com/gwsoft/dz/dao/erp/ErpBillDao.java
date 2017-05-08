package com.gwsoft.dz.dao.erp;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.ParseException;
import java.util.List;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import com.gwsoft.dz.AppException;
import com.gwsoft.dz.BatchReadable;
import com.gwsoft.dz.dao.DatabaseManager;
import com.gwsoft.dz.dao.SimpleErpDao;
import com.gwsoft.dz.model.Bill;

import static com.gwsoft.dz.utils.DateUtils.parseErpDate ;

@Repository
public class ErpBillDao extends SimpleErpDao<Bill> implements BatchReadable<Bill> {
    
    private static final Logger logger = LoggerFactory.getLogger(ErpBillDao.class) ;
    
    @Autowired
    private DatabaseManager erpDbManager ;

    @Override
    public List<Bill> getList(int start) throws AppException{
        return super.getBeanList(start);
    }

    @Override
    protected String getPk() {
        return "订单号";
    }

    @Override
    protected Bill buildBean(ResultSet rs) throws SQLException {
        Bill bill = null ;
        String orderno = rs.getString("订单号") ;
        try {
            bill = new Bill(rs.getString("所属行业名称"), rs.getString("所属项目编号"),
                    rs.getString("所属项目"), orderno, rs.getString("物资分类"), 
                    rs.getString("物资大类"), rs.getString("品名"), rs.getString("材质"), 
                    rs.getString("规格"), rs.getString("产地"), rs.getFloat("到货重量"), rs.getFloat("实提结算金额"), 
                    parseErpDate(rs.getString("到货日期")), parseErpDate(rs.getString("还款日期")), 
                    rs.getFloat("垫资金额"), rs.getInt("垫资天数"), rs.getFloat("垫资利息"), 
                    rs.getFloat("结算金额"), rs.getString("账单状态"));
        } catch (ParseException e) {
            logger.error("订单号[" + orderno + "]日期异常");
        }
        return bill;
    }

    @Override
    protected DatabaseManager getErpDbManager() {
        return this.erpDbManager;
    }

    @Override
    protected String getTable() {
        return "V_API_SysZD";
    }

}
