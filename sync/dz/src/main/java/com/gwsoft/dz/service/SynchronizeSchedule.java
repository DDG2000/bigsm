package com.gwsoft.dz.service;

import java.util.Calendar;
import java.util.Date;
import java.util.HashSet;
import java.util.List;
import java.util.Set;
import java.util.Timer;
import java.util.TimerTask;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.ApplicationListener;
import org.springframework.context.event.ContextRefreshedEvent;

import com.gwsoft.dz.model.TimerConfig;

public class SynchronizeSchedule implements ApplicationListener<ContextRefreshedEvent> {
    
    private final Logger logger = LoggerFactory.getLogger(SynchronizeSchedule.class) ;
    
    @Autowired
    private SynchronizeService synchronizeService ;
    
    private Set<String> statusSet = new HashSet<String>() ;
    
    private static final long PERIOD_DAY = 24 * 60 * 60 * 1000;  
    
    private Timer timer = null ;
    
    public void reset() {
        if (null != this.timer) {
            this.timer.cancel();
        }
        this.loadConfig();
        logger.info("load config success");
    }
    
    public void loadConfig () {
        logger.info("load config");
        this.timer = new Timer() ;
        List<TimerConfig> list = this.synchronizeService.getConfigList() ;
        for (TimerConfig config : list) {
            Date date = this.getDate(config) ;
            this.addTask(config.getTask(), date);
        }
    }
    
    private void addTask (TimerTask task , Date date) {
         this.timer.schedule(task, date, PERIOD_DAY);
    }
    
    private Date getDate (TimerConfig config) {
        Calendar calendar = Calendar.getInstance() ;
        calendar.set(Calendar.HOUR_OF_DAY, config.getSyncHours());
        calendar.set(Calendar.MINUTE, config.getSyncMinutes()) ;
        calendar.set(Calendar.SECOND, 00) ;
        return calendar.getTime() ;
    }

    public Set<String> getStatusSet() {
        return statusSet;
    }

    public void setStatusSet(Set<String> statusSet) {
        this.statusSet = statusSet;
    }

    @Override
    public void onApplicationEvent(ContextRefreshedEvent event) {
        this.loadConfig();
    }

}
