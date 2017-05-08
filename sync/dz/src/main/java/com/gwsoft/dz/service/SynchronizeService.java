package com.gwsoft.dz.service;

import java.util.List;
import java.util.TimerTask;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.ApplicationContext;
import org.springframework.context.support.ApplicationObjectSupport;

import com.gwsoft.dz.dao.TimerConfigDao;
import com.gwsoft.dz.model.TimerConfig;

public class SynchronizeService extends ApplicationObjectSupport {
    
    @Autowired
    private TimerConfigDao timerConfigDao ;
    
    public List<TimerConfig> getConfigList () {
        List<TimerConfig> configs = this.timerConfigDao.getConfigList() ;
        for (TimerConfig config : configs) {
            TimerTask task = this.getSynchronizeTask(config.getType()) ;
            config.setTask(task);
        }
        return configs ;
    }
    
    private TimerTask getSynchronizeTask (String name) {
        ApplicationContext ctx = this.getApplicationContext() ;
        TimerTask task = (TimerTask)ctx.getBean(name) ;
        return task ;
    }
    
}