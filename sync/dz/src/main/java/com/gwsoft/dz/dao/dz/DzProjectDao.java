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
import com.gwsoft.dz.dao.DzBaseDao;
import com.gwsoft.dz.model.Project;
import static com.gwsoft.dz.utils.DateUtils.*;

@Repository
public class DzProjectDao extends DzBaseDao<Project> implements BatchInsertable<Project> {
    
    private final Logger logger = LoggerFactory.getLogger(DzProjectDao.class) ;
    
    @Autowired
    private DatabaseManager dzDbManager ;

    @Override
    public void batchInsert(List<Project> list) throws AppException {
        Connection conn = null ;
        PreparedStatement ps = null ;
        String sql = "insert into erp_project (Vc_code,D_signdate,Vc_companycode,"
                + "Vc_name,Vc_address,N_ct_totalweight,N_ct_totalprice,N_loan_maxprice,"
                + "Vc_delivery_type,Vc_contractSn,I_ct_loan_life,Vc_ct_factory,"
                + "Vc_price_rule,Vc_transport,N_transportfee,Vc_emp,Vc_pay_type,"
                + "Vc_plusprice_type,Vc_planner,Vc_accounter,Vc_receiver,Vc_dutyinfo,"
                + "T_note,N_usable_loan,state,Createtime) values (?,?,?,?,?,?,?,?,?,?,"
                + "?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,now()) on duplicate key update "
                + "Vc_code=?,D_signdate=?,Vc_companycode=?,Vc_name=?,"
                + "Vc_address=?,N_ct_totalweight=?,N_ct_totalprice=?,N_loan_maxprice=?,"
                + "Vc_delivery_type=?,Vc_contractSn=?,I_ct_loan_life=?,Vc_ct_factory=?,"
                + "Vc_price_rule=?,Vc_transport=?,N_transportfee=?,Vc_emp=?,Vc_pay_type=?,"
                + "Vc_plusprice_type=?,Vc_planner=?,Vc_accounter=?,Vc_receiver=?,Vc_dutyinfo=?,"
                + "T_note=?,N_usable_loan=? " ;
        try {
            conn = this.dzDbManager.getConnection() ;
            ps = conn.prepareStatement(sql, ResultSet.TYPE_SCROLL_SENSITIVE, ResultSet.CONCUR_READ_ONLY) ;
            for (Project project : list) {
//            `Vc_code` varchar(100) DEFAULT NULL,
                ps.setString(1, project.getCode());
//            `D_signdate` datetime DEFAULT NULL COMMENT '合同签约日期',
                ps.setTimestamp(2, getTimestamp(project.getSignDate()));
//            `Vc_companycode` varchar(50) DEFAULT NULL COMMENT '需方单位代码',
                ps.setString(3, project.getRequireCompany());
//            `Vc_name` varchar(255) DEFAULT NULL COMMENT '项目名称',
                ps.setString(4, project.getProjectName());
//            `Vc_address` varchar(255) DEFAULT NULL COMMENT '项目地址',
                ps.setString(5, project.getProjectAddress());
//            `N_ct_totalweight` decimal(11,2) DEFAULT NULL COMMENT '合同总量(吨)',
                ps.setFloat(6, project.getContractWeight());
//            `N_ct_totalprice` decimal(11,2) DEFAULT NULL COMMENT '合同总金额(元)',
                ps.setFloat(7, project.getContractAmount());
//            `N_loan_maxprice` decimal(2,0) DEFAULT NULL COMMENT '最高垫资金额(元)',
                ps.setFloat(8, project.getMaxAmount());
//            `Vc_delivery_type` varchar(200) DEFAULT NULL COMMENT '交货方式',
                ps.setString(9, project.getDeliverType());
//            `Vc_contractSn` varchar(100) DEFAULT NULL COMMENT '合同编号',
                ps.setString(10, project.getContractNumber());
//            `I_ct_loan_life` int(11) DEFAULT NULL COMMENT '合同垫资期限，单位：天',
                ps.setInt(11, (int) project.getMaxTime());
//            `Vc_ct_factory` varchar(255) DEFAULT NULL COMMENT '产地范围',
                ps.setString(12, project.getArea());
//            `Vc_price_rule` varchar(255) DEFAULT NULL COMMENT '价格约定',
                ps.setString(13, "");
//            `Vc_transport` varchar(255) DEFAULT NULL COMMENT '运输条件',
                ps.setString(14, "");
//            `N_transportfee` decimal(11,2) DEFAULT NULL COMMENT '运输费',
                ps.setFloat(15, 0);
//            `Vc_emp` varchar(100) DEFAULT NULL COMMENT '业务员',
                ps.setString(16, project.getEmployee());
//            `Vc_pay_type` varchar(100) DEFAULT NULL COMMENT '支付条件',
                ps.setString(17, "");
//            `Vc_plusprice_type` varchar(255) DEFAULT NULL COMMENT '加价条件',
                ps.setString(18, project.getRaiseCondition());
//            `Vc_planner` varchar(100) DEFAULT NULL COMMENT '计划人',
                ps.setString(19, "");
//            `Vc_accounter` varchar(100) DEFAULT NULL COMMENT '对账人',
                ps.setString(20, "");
//            `Vc_receiver` varchar(100) DEFAULT NULL COMMENT '收货人',
                ps.setString(21, "");
//            `Vc_dutyinfo` varchar(255) DEFAULT NULL COMMENT '违约责任',
                ps.setString(22, project.getBlame());
//            `T_note` text COMMENT '备注',
                ps.setString(23, project.getNote());
//                N_usable_loan
                ps.setFloat(24, project.getQuota());
                
//              `Vc_code` varchar(100) DEFAULT NULL,
                ps.setString(24+1, project.getCode());
//            `D_signdate` datetime DEFAULT NULL COMMENT '合同签约日期',
                ps.setTimestamp(24+2, getTimestamp(project.getSignDate()));
//            `Vc_companycode` varchar(50) DEFAULT NULL COMMENT '需方单位代码',
                ps.setString(24+3, project.getRequireCompany());
//            `Vc_name` varchar(255) DEFAULT NULL COMMENT '项目名称',
                ps.setString(24+4, project.getProjectName());
//            `Vc_address` varchar(255) DEFAULT NULL COMMENT '项目地址',
                ps.setString(24+5, project.getProjectAddress());
//            `N_ct_totalweight` decimal(11,2) DEFAULT NULL COMMENT '合同总量(吨)',
                ps.setFloat(24+6, project.getContractWeight());
//            `N_ct_totalprice` decimal(11,2) DEFAULT NULL COMMENT '合同总金额(元)',
                ps.setFloat(24+7, project.getContractAmount());
//            `N_loan_maxprice` decimal(2,0) DEFAULT NULL COMMENT '最高垫资金额(元)',
                ps.setFloat(24+8, project.getMaxAmount());
//            `Vc_delivery_type` varchar(200) DEFAULT NULL COMMENT '交货方式',
                ps.setString(24+9, project.getDeliverType());
//            `Vc_contractSn` varchar(100) DEFAULT NULL COMMENT '合同编号',
                ps.setString(24+10, project.getContractNumber());
//            `I_ct_loan_life` int(11) DEFAULT NULL COMMENT '合同垫资期限，单位：天',
                ps.setInt(24+11, (int) project.getMaxTime());
//            `Vc_ct_factory` varchar(255) DEFAULT NULL COMMENT '产地范围',
                ps.setString(24+12, project.getArea());
//            `Vc_price_rule` varchar(255) DEFAULT NULL COMMENT '价格约定',
                ps.setString(24+13, "");
//            `Vc_transport` varchar(255) DEFAULT NULL COMMENT '运输条件',
                ps.setString(24+14, "");
//            `N_transportfee` decimal(11,2) DEFAULT NULL COMMENT '运输费',
                ps.setFloat(24+15, 0);
//            `Vc_emp` varchar(100) DEFAULT NULL COMMENT '业务员',
                ps.setString(24+16, project.getEmployee());
//            `Vc_pay_type` varchar(100) DEFAULT NULL COMMENT '支付条件',
                ps.setString(24+17, "");
//            `Vc_plusprice_type` varchar(255) DEFAULT NULL COMMENT '加价条件',
                ps.setString(24+18, project.getRaiseCondition());
//            `Vc_planner` varchar(100) DEFAULT NULL COMMENT '计划人',
                ps.setString(24+19, "");
//            `Vc_accounter` varchar(100) DEFAULT NULL COMMENT '对账人',
                ps.setString(24+20, "");
//            `Vc_receiver` varchar(100) DEFAULT NULL COMMENT '收货人',
                ps.setString(24+21, "");
//            `Vc_dutyinfo` varchar(255) DEFAULT NULL COMMENT '违约责任',
                ps.setString(24+22, project.getBlame());
//            `T_note` text COMMENT '备注',
                ps.setString(24+23, project.getNote());
//                N_usable_loan
                ps.setFloat(24+24, project.getQuota());
                ps.addBatch();
            }
            ps.executeBatch() ;
        } catch (SQLException e) {
            logger.error(e.getMessage());
        } finally {
            this.dzDbManager.close(conn, ps);
        }
    }

    @Override
    public Class<Project> getBean() {
        return Project.class;
    }

}
