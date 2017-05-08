package com.gwsoft.dz.model;

import java.util.Date;
import java.util.TimerTask;

public class TimerConfig {
    
    public TimerConfig(String type, Integer syncHours, Integer syncMinutes, Date lastSync) {
        super();
        this.type = type;
        this.syncHours = syncHours;
        this.syncMinutes = syncMinutes;
        this.lastSync = lastSync;
    }

    private String type ;
    
    private Integer syncHours ;
    
    private Integer syncMinutes ;
    
    private Date lastSync ;
    
    private TimerTask task ;
    
    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public Date getLastSync() {
        return lastSync;
    }

    public void setLastSync(Date lastSync) {
        this.lastSync = lastSync;
    }

    public TimerTask getTask() {
        return task;
    }

    public void setTask(TimerTask task) {
        this.task = task;
    }

    public Integer getSyncHours() {
        return syncHours;
    }

    public void setSyncHours(Integer syncHours) {
        this.syncHours = syncHours;
    }

    public Integer getSyncMinutes() {
        return syncMinutes;
    }

    public void setSyncMinutes(Integer syncMinutes) {
        this.syncMinutes = syncMinutes;
    }
    
}
