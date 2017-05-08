package com.gwsoft.dz.dao.dz;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import com.gwsoft.dz.AppException;
import com.gwsoft.dz.BatchInsertable;
import com.gwsoft.dz.dao.DatabaseManager;
import com.gwsoft.dz.dao.DzTableReplaceDao;
import com.gwsoft.dz.model.Bill;
import static com.gwsoft.dz.utils.DateUtils.*;

@Repository
public class DzBillDao extends DzTableReplaceDao<Bill> implements BatchInsertable<Bill> {
    
    private final Logger logger = LoggerFactory.getLogger(DzBillDao.class) ;
    
    @Autowired
    private DatabaseManager dzDbManager ;

    @Override
    public void batchInsert(List<Bill> list) throws AppException {
        String sql = "insert into " + this.getTempTableName() + " ("
                + "Vc_industry,Vc_projNo,Vc_proj,Vc_orderSn,Vc_goods_type,"
                + "Vc_goods_class,Vc_goods_breed,Vc_goods_material,Vc_goods_spec,"
                + "Vc_goods_factory,N_arrived_weight,N_ac_settlement,Dt_arrived,"
                + "Dt_repayment,N_loan_amount,I_loan_days,N_loan_interest,N_settlement,"
                + "Vc_billstatus,state,Createtime) values (?,?,?,?,?,?,?,?,?,"
                + "?,?,?,?,?,?,?,?,?,?,1,now()) " ;
        Connection conn = null ;
        PreparedStatement ps = null ;
        try {
            conn = this.dzDbManager.getConnection() ;
            ps = conn.prepareStatement(sql, ResultSet.TYPE_SCROLL_SENSITIVE, ResultSet.CONCUR_READ_ONLY) ;
            for (Bill bill : list) {
                ps.setString(1, bill.getIndustry());
                ps.setString(2, bill.getProjectNumber());
                ps.setString(3, bill.getProjectName());
                ps.setString(4, bill.getOrderNumber());
                ps.setString(5, bill.getCategory1());
                ps.setString(6, bill.getCategory2());
                ps.setString(7, bill.getGoodsName());
                ps.setString(8, bill.getGoodsMaterial());
                ps.setString(9, bill.getGoodsStandard());
                ps.setString(10, bill.getGoodsAddress());
                ps.setFloat(11, bill.getReceiveWeight());
                ps.setFloat(12, bill.getRealitySettlementPrice());
                ps.setTimestamp(13, getTimestamp(bill.getReceiveDate()));
                ps.setTimestamp(14, getTimestamp(bill.getPaybackDate()));
                ps.setFloat(15, bill.getInvestPrice());
                ps.setInt(16, bill.getInvestDays());
                ps.setFloat(17, bill.getInvestInterest());
                ps.setFloat(18, bill.getSettlementPrice());
                ps.setString(19, bill.getStatus());
                ps.addBatch();
            }
            ps.executeBatch() ;
        } catch (SQLException e) {
            logger.error(e.getMessage());
            throw new AppException() ;
        } finally {
            this.dzDbManager.close(conn, ps);
        }
    }

    @Override
    public Class<Bill> getBean() {
        return Bill.class;
    }

    @Override
    protected String getCreateTempTableSql() {
        return "CREATE TABLE `" + this.getTempTableName() + "` ( " +
                "  `id` bigint(20) NOT NULL AUTO_INCREMENT, " +
                "  `Vc_industry` varchar(50) DEFAULT NULL COMMENT '所属行业名称', " +
                "  `Vc_projNo` varchar(100) DEFAULT NULL COMMENT '所属项目编号', " +
                "  `Vc_proj` varchar(100) DEFAULT NULL COMMENT '所属项目', " +
                "  `Vc_orderSn` varchar(100) DEFAULT NULL COMMENT '订单号', " +
                "  `Vc_goods_type` varchar(100) DEFAULT NULL COMMENT '物资分类', " +
                "  `Vc_goods_class` varchar(100) DEFAULT NULL COMMENT '物资大类', " +
                "  `Vc_goods_breed` varchar(100) DEFAULT NULL COMMENT '品名', " +
                "  `Vc_goods_material` varchar(100) DEFAULT NULL COMMENT '材质', " +
                "  `Vc_goods_spec` varchar(100) DEFAULT NULL COMMENT '规格', " +
                "  `Vc_goods_factory` varchar(100) DEFAULT NULL COMMENT '产地', " +
                "  `N_arrived_weight` decimal(11,2) DEFAULT NULL COMMENT '到货重量', " +
                "  `N_ac_settlement` decimal(11,2) DEFAULT NULL COMMENT '实提结算金额', " +
                "  `Dt_arrived` datetime DEFAULT NULL COMMENT '到货日期', " +
                "  `Dt_repayment` datetime DEFAULT NULL COMMENT '还款日期', " +
                "  `N_loan_amount` decimal(11,2) DEFAULT NULL COMMENT '垫资金额', " +
                "  `I_loan_days` int(11) DEFAULT NULL COMMENT '垫资天数', " +
                "  `N_loan_interest` decimal(11,2) DEFAULT NULL COMMENT '垫资利息', " +
                "  `N_settlement` decimal(11,2) DEFAULT NULL COMMENT '结算金额', " +
                "  `Vc_billstatus` varchar(20) DEFAULT NULL COMMENT '账单状态', " +
                "  `state` tinyint(4) NOT NULL DEFAULT '1', " +
                "  `Createtime` datetime DEFAULT NULL, " +
                "  PRIMARY KEY (`id`) " +
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8 " ;
    }

    @Override
    protected String getTableName() {
        return "erp_syszd";
    }

    @Override
    protected DatabaseManager getDbManager() {
        return this.dzDbManager;
    }


}
