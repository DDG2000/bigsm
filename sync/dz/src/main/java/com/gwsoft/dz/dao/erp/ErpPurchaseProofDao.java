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
import com.gwsoft.dz.model.PurchaseProof;
import static com.gwsoft.dz.utils.DateUtils.parseErpDate;;

@Repository
public class ErpPurchaseProofDao extends SimpleErpDao<PurchaseProof> implements BatchReadable<PurchaseProof> {
    
    private static final Logger logger = LoggerFactory.getLogger(ErpPurchaseProofDao.class) ;
    
    @Autowired
    private DatabaseManager erpDbManager ;

    @Override
    public List<PurchaseProof> getList(int start) throws AppException {
        return super.getBeanList(start);
    }

    @Override
    protected DatabaseManager getErpDbManager() {
        return this.erpDbManager;
    }

    @Override
    protected String getTable() {
        return "V_API_SysTD";
    }

    @Override
    protected String getPk() {
        return "订单号";
    }

    @Override
    protected PurchaseProof buildBean(ResultSet rs) throws SQLException {
        PurchaseProof purchaseProof = null ;
        String orderno = rs.getString("订单号") ;
        try {
            purchaseProof = new PurchaseProof(orderno , parseErpDate(rs.getString("订单日期")), 
                    parseErpDate(rs.getString("订单审核日期")), parseErpDate(rs.getString("采购日期")), 
                    parseErpDate(rs.getString("提货日期")), parseErpDate(rs.getString("发货日期")), parseErpDate(rs.getString("到货日期")), 
                    rs.getString("所属项目编号"), rs.getString("所属项目"), rs.getString("物流公司"), 
                    rs.getString("运单号"), rs.getString("收货联系人"), rs.getString("收货联系人电话"), rs.getString("承运联系人"),
                    rs.getString("承运人电话"), rs.getString("发货地"), rs.getString("到货地"), rs.getString("送货车牌"), 
                    rs.getString("订单状态"), "", "", rs.getString("品名"), rs.getString("材质"), rs.getString("规格"),
                    rs.getString("产地"), rs.getString("单位"), rs.getFloat("计划重量"), rs.getFloat("实际重量"), 
                    rs.getFloat("实提单价"), rs.getFloat("实提结算金额"),rs.getString("产品类型"),rs.getString("物资大类"));
        } catch (ParseException e) {
            logger.error("订单号["+orderno+"]日期格式错误");
        }
        return purchaseProof;
    }

//    @Override
//    public Integer getCount() {
//        return 100 ;
//    }

}
