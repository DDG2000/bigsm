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
import com.gwsoft.dz.model.Goods;

@Repository
public class DzGoodsDao extends DzBaseDao<Goods> implements BatchInsertable<Goods> {
    
    private final Logger logger = LoggerFactory.getLogger(DzGoodsDao.class) ;

    @Autowired
    private DatabaseManager dzDbManager ;
    
//    `id` int(11) NOT NULL AUTO_INCREMENT,
//    `Vc_goods_code` varchar(50) DEFAULT NULL COMMENT '物资代码',
//    `Vc_goods_type` varchar(255) DEFAULT NULL COMMENT '产品类型',
//    `Vc_goods_class` varchar(255) DEFAULT NULL COMMENT '物资大类',
//    `Vc_goods_breed` varchar(255) DEFAULT NULL COMMENT '品名',
//    `Vc_goods_spec` varchar(255) DEFAULT NULL COMMENT '规格',
//    `Vc_goods_material` varchar(255) DEFAULT NULL COMMENT '材质',
//    `Vc_goods_factory` varchar(255) DEFAULT NULL COMMENT '产地',
//    `Vc_unit` varchar(20) DEFAULT NULL COMMENT '计量单位',
//    `T_note` varchar(100) DEFAULT NULL COMMENT '备注',
//    `Vc_helpcode` varchar(100) DEFAULT NULL COMMENT '助记码（拼音代码)',
//    `state` tinyint(4) NOT NULL DEFAULT '1',
//    `Createtime` datetime NOT NULL,
    public void batchInsert (List<Goods> list) throws AppException {
        Connection conn = null ;
        PreparedStatement ps = null ;
        String sql = "insert into erp_goods_tree (Vc_goods_code,Vc_goods_type,Vc_goods_class,Vc_goods_breed,"
                + "Vc_goods_spec,Vc_goods_material,Vc_goods_factory,Vc_unit,T_note,Vc_helpcode,state,Createtime) "
                + "values (?,?,?,?,?,?,?,?,?,?,1,now()) on duplicate key update Vc_goods_code=?,Vc_goods_type=?,"
                + "Vc_goods_class=?,Vc_goods_breed=?,Vc_goods_spec=?,Vc_goods_material=?,Vc_goods_factory=?,"
                + "Vc_unit=?,T_note=?,Vc_helpcode=? " ;
        try {
            conn = this.dzDbManager.getConnection() ;
            ps = conn.prepareStatement(sql, ResultSet.TYPE_SCROLL_SENSITIVE, ResultSet.CONCUR_READ_ONLY) ;
            for (Goods goods : list) {
                ps.setString(1, goods.getCode());
                ps.setString(2, goods.getCategory1());
                ps.setString(3, goods.getCategory2());
                ps.setString(4, goods.getName());
                ps.setString(5, goods.getStandard());
                ps.setString(6, goods.getMaterial());
                ps.setString(7, goods.getAddress());
                ps.setString(8, goods.getUnit());
                ps.setString(9, goods.getRemark());
                ps.setString(10, goods.getMark());
                ps.setString(11, goods.getCode());
                ps.setString(12, goods.getCategory1());
                ps.setString(13, goods.getCategory2());
                ps.setString(14, goods.getName());
                ps.setString(15, goods.getStandard());
                ps.setString(16, goods.getMaterial());
                ps.setString(17, goods.getAddress());
                ps.setString(18, goods.getUnit());
                ps.setString(19, goods.getRemark());
                ps.setString(20, goods.getMark());
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

    public Class<Goods> getBean() {
        return Goods.class;
    }

}