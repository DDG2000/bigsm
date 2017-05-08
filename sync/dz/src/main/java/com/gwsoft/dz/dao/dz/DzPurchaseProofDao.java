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
import com.gwsoft.dz.model.PurchaseProof;
import static com.gwsoft.dz.utils.DateUtils.*;

@Repository
public class DzPurchaseProofDao extends DzTableReplaceDao<PurchaseProof> implements BatchInsertable<PurchaseProof> {
    
    private final Logger logger = LoggerFactory.getLogger(DzPurchaseProofDao.class) ;
    
    @Autowired
    private DatabaseManager dzDbManager ;

    @Override
    public void batchInsert(List<PurchaseProof> list) throws AppException {
        Connection conn = null ;
        PreparedStatement ps = null ;
        String sql = "insert into " + this.getTempTableName() 
                + " (Vc_orderSn,Dt_orderdate,Dt_checkdate,Dt_purchase,Dt_pickup,"
                + "Dt_senddate,Dt_arrived,Vc_projNo,Vc_proj,Vc_express,Vc_waybillSn,"
                + "Vc_contact,Vc_contact_phone,Vc_shipper,Vc_shipper_phone,Vc_sendplace,"
                + "Vc_arrivedplace,Vc_truckID,Vc_orderstatus,Vc_goods_breed,Vc_goods_material,"
                + "Vc_goods_spec,Vc_goods_factory,Vc_goods_uint,N_plan_weight,N_ac_weight,"
                + "N_ac_price,N_ac_settlement,Vc_goods_type,Vc_goods_class,state,Createtime) values (?,?,?,?,?,?,?,?,"
                + "?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,now())";
        try {
            conn = this.dzDbManager.getConnection() ;
            ps = conn.prepareStatement(sql, ResultSet.TYPE_SCROLL_SENSITIVE, ResultSet.CONCUR_READ_ONLY) ;
            for (PurchaseProof proof : list) {
//                `Vc_orderSn` varchar(100) DEFAULT NULL COMMENT '订单号',
                ps.setString(1, proof.getOrderno());
//                `Dt_orderdate` datetime DEFAULT NULL COMMENT '订单日期',
                ps.setTimestamp(2, getTimestamp(proof.getOrderDate()));
//                `Dt_checkdate` datetime DEFAULT NULL COMMENT '订单审核日期',
                ps.setTimestamp(3, getTimestamp(proof.getOrderAuditDate()));
//                `Dt_purchase` datetime DEFAULT NULL COMMENT '采购日期',
                ps.setTimestamp(4, getTimestamp(proof.getPurchaseDate()));
//                `Dt_pickup` datetime DEFAULT NULL COMMENT '提货日期',
                ps.setTimestamp(5, getTimestamp(proof.getTakeDate()));
//                `Dt_senddate` datetime DEFAULT NULL COMMENT '发货日期',
                ps.setTimestamp(6, getTimestamp(proof.getDispatchDate()));
//                `Dt_arrived` datetime DEFAULT NULL COMMENT '到货日期',
                ps.setTimestamp(7, getTimestamp(proof.getReceiveDate()));
//                `Vc_projNo` varchar(100) DEFAULT NULL COMMENT '所属项目编号',
                ps.setString(8, proof.getProjectCode());
//                `Vc_proj` varchar(100) DEFAULT NULL COMMENT '所属项目',
                ps.setString(9, proof.getProjectName());
//                `Vc_express` varchar(100) DEFAULT NULL COMMENT '物流公司',
                ps.setString(10, proof.getTransportCompany());
//                `Vc_waybillSn` varchar(100) DEFAULT NULL COMMENT '运单号/发货单号',
                ps.setString(11, proof.getTransportNumber());
//                `Vc_contact` varchar(100) DEFAULT NULL COMMENT '收货联系人',
                ps.setString(12, proof.getReceiveName());
//                `Vc_contact_phone` varchar(20) DEFAULT NULL COMMENT '收货联系人电话',
                ps.setString(13, proof.getReceivePhone());
//                `Vc_shipper` varchar(100) DEFAULT NULL COMMENT '承运联系人',
                ps.setString(14, proof.getTransportName());
//                `Vc_shipper_phone` varchar(20) DEFAULT NULL COMMENT '承运人电话',
                ps.setString(15, proof.getTransportPhone());
//                `Vc_sendplace` varchar(100) DEFAULT NULL COMMENT '发货地',
                ps.setString(16, proof.getStartAddress());
//                `Vc_arrivedplace` varchar(100) DEFAULT NULL COMMENT '到货地',
                ps.setString(17, proof.getEndAddress());
//                `Vc_truckID` varchar(20) DEFAULT NULL COMMENT '送货车牌',
                ps.setString(18, proof.getCarNumber());
//                `Vc_orderstatus` varchar(20) DEFAULT NULL COMMENT '订单状态',
                ps.setString(19, proof.getOrderStatus());
//                `Vc_goods_breed` varchar(100) DEFAULT NULL COMMENT '品名',
                ps.setString(20, proof.getGoodsName());
//                `Vc_goods_material` varchar(100) DEFAULT NULL COMMENT '材质',
                ps.setString(21, proof.getGoodsMeterial());
//                `Vc_goods_spec` varchar(100) DEFAULT NULL COMMENT '规格',
                ps.setString(22, proof.getGoodsStandard());
//                `Vc_goods_factory` varchar(100) DEFAULT NULL COMMENT '产地',
                ps.setString(23, proof.getGoodsAddress());
//                `Vc_goods_uint` varchar(20) DEFAULT NULL COMMENT '单位',
                ps.setString(24, proof.getGoodsUnit());
//                `N_plan_weight` decimal(11,2) DEFAULT NULL COMMENT '计划重量',
                ps.setFloat(25, proof.getPlanWeight());
//                `N_ac_weight` decimal(11,2) DEFAULT NULL COMMENT '实际重量',
                ps.setFloat(26, proof.getWeight());
//                `N_ac_price` decimal(11,2) DEFAULT NULL COMMENT '实提单价',
                ps.setFloat(27, proof.getPrice());
//                `N_ac_settlement` decimal(11,2) DEFAULT NULL COMMENT '实提结算金额',
                ps.setFloat(28, proof.getAmount());
//                `Vc_goods_type` varchar(100) DEFAULT NULL COMMENT '产品类型', 
                ps.setString(29, proof.getCategory1());
//                `Vc_goods_class` varchar(100) DEFAULT NULL COMMENT '物资大类', 
                ps.setString(30, proof.getCategory2());
                ps.addBatch();
            }
            ps.executeBatch() ;
        } catch (SQLException e) {
            logger.error(e.getMessage());
        }
    }

    @Override
    public Class<PurchaseProof> getBean() {
        return PurchaseProof.class;
    }

    @Override
    protected String getCreateTempTableSql() {
        return "CREATE TABLE `" + this.getTempTableName() + "` ( " +
                "  `id` bigint(20) NOT NULL AUTO_INCREMENT, " +
                "  `Vc_orderSn` varchar(100) DEFAULT NULL COMMENT '订单号', " +
                "  `Dt_orderdate` datetime DEFAULT NULL COMMENT '订单日期', " +
                "  `Dt_checkdate` datetime DEFAULT NULL COMMENT '订单审核日期', " +
                "  `Dt_purchase` datetime DEFAULT NULL COMMENT '采购日期', " +
                "  `Dt_pickup` datetime DEFAULT NULL COMMENT '提货日期', " +
                "  `Dt_senddate` datetime DEFAULT NULL COMMENT '发货日期', " +
                "  `Dt_arrived` datetime DEFAULT NULL COMMENT '到货日期', " +
                "  `Vc_projNo` varchar(100) DEFAULT NULL COMMENT '所属项目编号', " +
                "  `Vc_proj` varchar(100) DEFAULT NULL COMMENT '所属项目', " +
                "  `Vc_express` varchar(100) DEFAULT NULL COMMENT '物流公司', " +
                "  `Vc_waybillSn` varchar(100) DEFAULT NULL COMMENT '运单号/发货单号', " +
                "  `Vc_contact` varchar(100) DEFAULT NULL COMMENT '收货联系人', " +
                "  `Vc_contact_phone` varchar(20) DEFAULT NULL COMMENT '收货联系人电话', " +
                "  `Vc_shipper` varchar(100) DEFAULT NULL COMMENT '承运联系人', " +
                "  `Vc_shipper_phone` varchar(20) DEFAULT NULL COMMENT '承运人电话', " +
                "  `Vc_sendplace` varchar(100) DEFAULT NULL COMMENT '发货地', " +
                "  `Vc_arrivedplace` varchar(100) DEFAULT NULL COMMENT '到货地', " +
                "  `Vc_truckID` varchar(20) DEFAULT NULL COMMENT '送货车牌', " +
                "  `Vc_orderstatus` varchar(20) DEFAULT NULL COMMENT '订单状态', " +
                "  `Vc_goods_breed` varchar(100) DEFAULT NULL COMMENT '品名', " +
                "  `Vc_goods_material` varchar(100) DEFAULT NULL COMMENT '材质', " +
                "  `Vc_goods_spec` varchar(100) DEFAULT NULL COMMENT '规格', " +
                "  `Vc_goods_factory` varchar(100) DEFAULT NULL COMMENT '产地', " +
                "  `Vc_goods_uint` varchar(20) DEFAULT NULL COMMENT '单位', " +
                "  `N_plan_weight` decimal(11,2) DEFAULT NULL COMMENT '计划重量', " +
                "  `N_ac_weight` decimal(11,2) DEFAULT NULL COMMENT '实际重量', " +
                "  `N_ac_price` decimal(11,2) DEFAULT NULL COMMENT '实提单价', " +
                "  `N_ac_settlement` decimal(11,2) DEFAULT NULL COMMENT '实提结算金额', " +
                "  `Vc_goods_type` varchar(100) DEFAULT NULL COMMENT '产品类型', " +
                "  `Vc_goods_class` varchar(100) DEFAULT NULL COMMENT '物资大类', " +
                "  `state` tinyint(4) NOT NULL DEFAULT '1', " +
                "  `Createtime` datetime DEFAULT NULL, " +
                "  PRIMARY KEY (`id`) " +
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单信息表' " ;
    }

    @Override
    protected String getTableName() {
        return "erp_systd";
    }

    @Override
    protected DatabaseManager getDbManager() {
        return this.dzDbManager;
    }

}
