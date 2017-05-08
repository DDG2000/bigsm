package com.gwsoft.dz.service;

import java.util.List;
import java.util.Set;
import java.util.TimerTask;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;

import com.gwsoft.dz.AppException;
import com.gwsoft.dz.BatchInsertable;
import com.gwsoft.dz.BatchReadable;
import com.gwsoft.dz.Constant;

public class SynchronizeTask<T> extends TimerTask {
    
    private BatchInsertable<T> dzDao ;
    private BatchReadable<T> erpDao ;
    
    @Autowired
    private SynchronizeSchedule syncSchedule ;
    
    private final Logger logger = LoggerFactory.getLogger(SynchronizeTask.class) ;
    
    @Override
    public void run () {
        Class<T> bean = this.dzDao.getBean() ;
        String beanName = bean.getName() ;
        Set<String> statusSet = this.syncSchedule.getStatusSet() ;
        if (statusSet.contains(beanName)) {
            logger.info("Synchronize {} is running , task skipped .",beanName);
        } else {
            statusSet.add(beanName) ;
            try {
                List<T> list = null ;
                int totalCount = this.erpDao.getCount() ;
                int pageCount = (int) Math.ceil(totalCount / Constant.SYNC_SIZE) ;
                logger.info("Synchronize {} start : {} records .", beanName, totalCount);
                dzDao.beforeInsert();
                for (int i = 0 ; i <= pageCount ; i ++) {
                    list = this.erpDao.getList(i * Constant.SYNC_SIZE) ;
                    this.dzDao.batchInsert(list);
                }
                logger.info("Synchronize {} success !", beanName);
                dzDao.afterInsert();
            } catch (AppException e) {
                // nothing :)
            } finally {
                statusSet.remove(beanName) ;
            }
        }
    }

    public BatchInsertable<T> getDzDao() {
        return dzDao;
    }

    public void setDzDao(BatchInsertable<T> dzDao) {
        this.dzDao = dzDao;
    }

    public BatchReadable<T> getErpDao() {
        return erpDao;
    }

    public void setErpDao(BatchReadable<T> erpDao) {
        this.erpDao = erpDao;
    }

}
